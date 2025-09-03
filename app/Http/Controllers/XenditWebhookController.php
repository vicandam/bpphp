<?php

namespace App\Http\Controllers;

use App\Models\PendingOrder;
use App\Models\User;
use App\Models\Event; // Assuming event details are linked somehow
use App\Models\Ticket;
use App\Services\Payment\PaymentLinksService;
use App\Services\QrCodeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeNewUserMail;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;

class XenditWebhookController extends Controller
{
    protected $qrCodeGeneratorService;
    protected PaymentLinksService $paymentService;

    public function __construct(QrCodeGeneratorService $qrCodeGeneratorService, PaymentLinksService $paymentService)
    {
        $this->middleware('auth')->except(['handlePaymentLinksCallback']); // 'callback' and 'scanRedeem' must be public for Xendit/scanning to access them.
        $this->qrCodeGeneratorService = $qrCodeGeneratorService;
        $this->paymentService = $paymentService;
    }

    public function handlePaymentLinksCallback(Request $request)
    {
        $data = $request->all();

        logger('xendit callback ', $data);

        if (($data['status'] ?? null) === 'PAID') {

            logger('status', [$data['status']]);

            try {

                $payment = $this->paymentService->normalizePaymentResponse((object) $data);

                $result = $this->paymentService->finalizeSuccessfulPayment($payment);

                logger('Finalization success', $result);
            } catch (\Throwable $e) {
                logger('Error in finalizeSuccessfulPayment:', [$e->getMessage()]);
            }
        }
    }

    /**
     * Handles webhooks from Xendit Payment Links (e.g., invoice.paid).
     * This is the main registration and ticket creation endpoint for Facebook Ads flow.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handlePaymentLinkCallbackOld(Request $request)
    {
        Log::info('Xendit Webhook received...', $request->all());

        // Webhook Signature Validation (Recommended)
        $xCallbackToken = $request->header('X-Callback-Token');
        $expectedToken = config('services.xendit.webhook_verification_token');

        if ($xCallbackToken !== $expectedToken) {
            Log::warning('Webhook Verification Failed: Invalid token from IP ' . $request->ip());
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        // Detect Payload Type
        if (isset($payload['event']) && isset($payload['data'])) {
            // API Invoice Webhook
            $eventType = $payload['event'];
            $data = $payload['data'];
            $source = 'API';
        } else {
            // Payment Link Webhook
            $eventType = 'invoice.paid'; // Assume paid
            $data = $payload;
            $source = 'PaymentLink';
        }

        Log::info("Xendit $source Webhook Event: $eventType");

        // Extract Common Data
        $invoiceId = $data['id'] ?? null;
        $externalId = $data['external_id'] ?? null;
        $status = $data['status'] ?? null;
        $amount = $data['amount'] ?? null;
        $payerEmail = $data['payer_email'] ?? null;
        $description = $data['description'] ?? '';
        $metadata = $data['metadata'] ?? [];

        // Extract name
        if ($source === 'PaymentLink') {
            $first = $data['on_demand_payload']['first_name'] ?? '';
            $last = $data['on_demand_payload']['last_name'] ?? '';
            $userName = trim("$first $last");
        } else {
            $userName = $data['customer']['given_names'] ?? $data['customer']['name'] ?? 'New User';
        }

        if ($eventType !== 'invoice.paid' || $status !== 'PAID') {
            Log::info("Non-paid event received. Skipping External ID: $externalId");
            return response()->json(['message' => 'Not a paid invoice'], 200);
        }

        // Idempotency Check
        if (Ticket::where('ticket_code', $externalId)->exists()) {
            Log::warning("Duplicate webhook for External ID: $externalId");
            return response()->json(['message' => 'Already processed'], 200);
        }

        try {
            DB::transaction(function () use ($payerEmail, $externalId, $amount, $description, $data, $metadata, $userName, $source) {
                $user = User::where('email', $payerEmail)->first();
                $generatedPassword = null;

                if (!$user) {
                    Log::info("Creating user for $payerEmail");

                    $plainPassword = Str::random(12);
                    $hashedPassword = Hash::make($plainPassword);

                    $defaultMembership = \App\Models\MembershipType::where('name', 'United Moviegoers and Musiclovers Dream Club International')->first();

                    $user = User::create([
                        'name' => $userName,
                        'email' => $payerEmail,
                        'password' => $hashedPassword,
                        'membership_type_id' => $defaultMembership->id ?? null,
                        'referral_code' => Str::upper(Str::random(8)),
                        'email_verified_at' => now(),
                        'bpp_wallet_balance' => 0.00,
                        'bpp_points_balance' => 0.00,
                    ]);

                    $generatedPassword = $plainPassword;
                    Log::info("New user created: " . $user->email);
                }

                // Determine Event
                $event = null;

                if (!empty($metadata['event_id'])) {
                    $event = Event::find($metadata['event_id']);
                } else {
                    $eventName = Str::after($description, 'Ticket for ');
                    $event = Event::where('name', $eventName)->first();
                }

                if (!$event) {
                    Log::error("No event matched. Description: $description, Metadata: " . json_encode($metadata));
                    throw new \Exception("Event not found.");
                }

                // Create Ticket
                $joyPointsEarned = floor($amount / 500) * 10;
                $qrCodePath = $this->qrCodeGeneratorService->generateForTicket($externalId);

                $ticket = Ticket::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'ticket_code' => $externalId,
                    'is_redeemed' => false,
                    'joy_points_earned' => $joyPointsEarned,
                    'purchase_date' => now(),
                    'virtual_membership_card_qr' => $qrCodePath,
                ]);

                $user->bpp_points_balance += $joyPointsEarned;
                $user->save();

                Log::info("Ticket {$ticket->id} created for {$user->email} for event {$event->name}");

                // Email credentials
                if ($generatedPassword) {
                    try {
                        Mail::to($user->email)->send(new WelcomeNewUserMail($user, $generatedPassword));
                        Log::info("Welcome email sent to {$user->email}");
                    } catch (\Exception $e) {
                        Log::error("Failed to send welcome email: " . $e->getMessage());
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error("Processing failed for External ID $externalId: " . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }

        return response()->json(['message' => 'Webhook processed successfully'], 200);
    }
    public function handle(Request $request)
    {
        // Log for debugging purposes
        Log::info('Xendit Webhook Received', $request->all());

        $data = $request->all();

        // Validate the event type
        if ($data['status'] !== 'PAID') {
            return response()->json(['message' => 'Not a paid invoice'], 200);
        }

        // Find the matching pending order using external_id
        $externalId = $data['external_id'] ?? null;

        if (!$externalId) {
            Log::warning('Missing external_id in webhook');
            return response()->json(['message' => 'Missing external_id'], 400);
        }

        $pendingOrder = PendingOrder::where('external_id', $externalId)->first();

        if (!$pendingOrder) {
            Log::warning('Pending order not found for external_id: ' . $externalId);
            return response()->json(['message' => 'Pending order not found'], 404);
        }

        // Double check if a ticket has already been created for this external_id
        $existingTicket = Ticket::where('event_id', $pendingOrder->event_id)
            ->where('user_id', $pendingOrder->user_id)
            ->where('purchase_date', '!=', null)
            ->first();

        if ($existingTicket) {
            return response()->json(['message' => 'Ticket already issued'], 200);
        }

        // Create the ticket
        Ticket::create([
            'user_id' => $pendingOrder->user_id,
            'event_id' => $pendingOrder->event_id,
            'ticket_code' => strtoupper(Str::random(8)),
            'purchase_date' => now(),
            'is_redeemed' => false,
        ]);

        // Optional: you can also delete the pending order or update its status
        $pendingOrder->delete();

        return response()->json(['message' => 'Ticket created successfully'], 200);
    }
}

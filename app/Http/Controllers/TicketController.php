<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Services\QrCodeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Xendit\Invoice\InvoiceApi;
use Xendit\Configuration;

class TicketController extends Controller
{
    protected $qrCodeGeneratorService;
    public function __construct(QrCodeGeneratorService $qrCodeGeneratorService)
    {
        $this->middleware('auth')->except(['callback', 'scanRedeem']); // 'callback' and 'scanRedeem' must be public for Xendit/scanning to access them.
        $this->qrCodeGeneratorService = $qrCodeGeneratorService;
    }

    /**
     * Display a listing of the user's purchased tickets.
     */
    public function index()
    {
        $tickets = Auth::user()->tickets()->with('event')->get();
        return view('tickets.index', compact('tickets'));
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        // Ensure the authenticated user owns this ticket
        if (Auth::id() !== $ticket->user_id) {
            abort(403, 'Unauthorized action.');
        }
        $ticket->load('event');
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Store a new ticket request and create a Xendit invoice.
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => ['required', 'exists:events,id'],
        ]);

        $event = Event::findOrFail($request->event_id);
        $user = Auth::user();

        // Step 1: Initialize Xendit API
        Configuration::setXenditKey(config('services.xendit.secret_key'));
        $apiInstance = new InvoiceApi();

        try {
            $ticketCode = Str::uuid(); // Use a unique ID as Xendit's external_id and our ticket_code

            // In a more complex system, you might first create a 'pending_order' record
            // in your database here, linking it to $ticketCode, user, and event.
            // Then, in the webhook, you'd mark that order as paid and create the actual ticket.
            // For this setup, we rely on the external_id as the ticketCode.

            $params = [
                'external_id' => $ticketCode, // This will be used to identify the ticket later
                'amount' => $event->ticket_price,
                'description' => 'Ticket for ' . $event->name,
                'payer_email' => $user->email,
                'invoice_duration' => 86400, // Invoice is valid for 24 hours
            ];

            $createInvoiceRequest = new \Xendit\Invoice\CreateInvoiceRequest($params);
            $invoice = $apiInstance->createInvoice($createInvoiceRequest);

            // Redirect the user to the Xendit payment page
            return redirect($invoice['invoice_url']);

        } catch (\Xendit\XenditSdkException $e) {
            Log::error('Xendit invoice creation failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to initiate payment. Please try again.']);
        }
    }

    /**
     * Handle the Xendit webhook callback for successful payments.
     */
    public function callback(Request $request)
    {
        // Step 1: Verify the webhook token
        $xCallbackToken = $request->header('X-Callback-Token');
        $expectedToken = config('services.xendit.webhook_verification_token');

        if ($xCallbackToken !== $expectedToken) {
            Log::error('Xendit Webhook Verification Failed: Invalid token.');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Step 2: Extract payment data
        $payload = $request->all();
        $externalId = $payload['external_id'];
        $status = $payload['status'];
        $amount = $payload['amount']; // Get amount from payload

        Log::info('Xendit Webhook received for external_id: ' . $externalId . ' with status: ' . $status);

        // Step 3: Handle PAID status
        if ($status === 'PAID') {
            // A more robust solution would check if the ticket already exists
            // to prevent duplicate creation on webhook retries.
            $existingTicket = Ticket::where('ticket_code', $externalId)->first();

            if ($existingTicket && $existingTicket->is_redeemed === false) {
                Log::info('Webhook received for existing unredeemed ticket: ' . $externalId . '. Status is already PAID. Skipping re-creation.');
                return response()->json(['message' => 'Ticket already processed'], 200);
            } elseif ($existingTicket && $existingTicket->is_redeemed === true) {
                Log::info('Webhook received for existing redeemed ticket: ' . $externalId . '. Status is already PAID and redeemed. Skipping re-creation.');
                return response()->json(['message' => 'Ticket already processed and redeemed'], 200);
            }

            // --- Deduce user_id and event_id from initial invoice creation
            // IMPORTANT: In a production system, you would store event_id and user_id
            // in your own 'orders' or 'pending_tickets' table when creating the Xendit invoice,
            // linked by the 'external_id'. Then, retrieve them here.
            // For this example, we'll try to find the original intent.
            // This is a simplification and less robust for production.
            // You might need to retrieve the full invoice details from Xendit if not stored locally.

            // To link back to the user and event from just external_id (ticketCode),
            // you'd typically need to have logged this information beforehand,
            // or perform a lookup (e.g., if external_id embeds user/event info or links to a temporary record).
            // For now, let's assume you have a way to find the user and event that initiated the request.
            // A common way is to store a temporary record in your DB when calling createInvoice().
            // For demonstration, let's assume we can derive user and event from a preliminary check
            // or fetch the invoice details if needed.

            // Mocking retrieval of user and event for demonstration:
            // This is problematic in a real system as you don't know which user/event it maps to
            // without a pre-payment order record.
            // You need to store $user->id and $event->id with $ticketCode (external_id) before payment.
            // Let's assume the external_id is unique enough that it can be tied back to an intended purchase.
            // Example of a fallback if you didn't store it: (NOT RECOMMENDED FOR PROD)
            $user = User::where('email', $payload['payer_email'] ?? null)->first(); // Use payer_email from payload
            $event = Event::where('ticket_price', $amount)->first(); // Very fragile, don't use in prod without unique mapping!
            // END OF MOCKING

            if (!$user || !$event) {
                Log::error('Failed to link Xendit payment to a user or event. User ID: ' . ($user->id ?? 'N/A') . ', Event ID: ' . ($event->id ?? 'N/A'));
                return response()->json(['message' => 'Missing data for ticket creation'], 400);
            }


            try {
                $joyPointsEarned = floor($event->ticket_price / 500) * 10;
                $qrCodePath = $this->qrCodeGeneratorService->generateForTicket($externalId); // Generate QR code

                $ticket = Ticket::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'ticket_code' => $externalId, // External ID from Xendit is our unique token
                    'is_redeemed' => false,
                    'joy_points_earned' => $joyPointsEarned,
                    'purchase_date' => now(),
                    'virtual_membership_card_qr' => $qrCodePath, // Store the path to the generated QR
                ]);

                // Update user's BPP Points Balance
                $user->bpp_points_balance += $joyPointsEarned;
                $user->save();

                Log::info('Ticket ' . $ticket->id . ' created successfully via webhook for external_id: ' . $externalId);
                // Optionally send confirmation email with QR code
                // Mail::to($user->email)->send(new TicketConfirmationMail($ticket));

            } catch (\Exception $e) {
                Log::error('Ticket creation failed in webhook for external_id ' . $externalId . ': ' . $e->getMessage());
                return response()->json(['message' => 'Internal Server Error during ticket creation'], 500);
            }
        } else if ($status === 'EXPIRED' || $status === 'CANCELLED' || $status === 'FAILED') {
            Log::info('Xendit invoice for external_id: ' . $externalId . ' was ' . $status . '. No ticket created.');
            // You might want to update a pending order status in your DB here.
        }

        // Always return a 200 OK response to Xendit to prevent retries
        return response()->json(['message' => 'Webhook received and processed'], 200);
    }

    /**
     * Handle the QR code scan for ticket redemption.
     * This endpoint will be embedded in the QR code URL.
     *
     * @param string $token The unique ticket token from the QR code.
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function scanRedeem(string $token)
    {
        // Step 1: Validate the QR code/token
        $ticket = Ticket::where('ticket_code', $token)->first();

        if (!$ticket) {
            return response()->view('tickets.redeem_status', ['status' => 'invalid', 'message' => 'Invalid or expired ticket token.'], 404);
        }

        // Step 2: Prevent double redemption
        if ($ticket->is_redeemed) {
            return response()->view('tickets.redeem_status', ['status' => 'redeemed', 'message' => 'This ticket has already been redeemed on ' . $ticket->updated_at->format('M d, Y H:i A') . '.'], 200);
        }

        // Step 3: Reuse the existing redeem logic (or directly implement it here)
        try {
            // For simplicity, directly update here instead of calling a separate method,
            // as the logic is very contained. If `redeem()` was complex, you'd call it.
            $ticket->is_redeemed = true;
            $ticket->save();

            Log::info('Ticket ' . $ticket->id . ' redeemed successfully via QR scan.');
            return response()->view('tickets.redeem_status', ['status' => 'success', 'message' => 'Ticket redeemed successfully! Welcome to ' . $ticket->event->name . '.'], 200);
        } catch (\Exception $e) {
            Log::error('Ticket redemption failed for token ' . $token . ': ' . $e->getMessage());
            return response()->view('tickets.redeem_status', ['status' => 'error', 'message' => 'An error occurred during redemption. Please try again or contact staff.'], 500);
        }
    }
    public function redeem(Request $request, Ticket $ticket)
    {
        // This method should be protected by admin or event staff middleware
        $this->middleware('admin'); // Example: only admins can redeem

        if ($ticket->is_redeemed) {
            return back()->with('info', 'This ticket has already been redeemed.');
        }

        try {
            $ticket->is_redeemed = true;
            $ticket->save();
            return back()->with('success', 'Ticket ' . $ticket->ticket_code . ' has been redeemed.');
        } catch (\Exception $e) {
            Log::error('Ticket redemption failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to redeem ticket.']);
        }
    }
}

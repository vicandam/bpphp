<?php

namespace App\Services\Payment;

use App\Mail\InvoiceMail;
use App\Mail\WelcomeNewUserMail;
use App\Models\Event;
use App\Models\MembershipType;
use App\Models\Payout;
use App\Models\Ticket;
use App\Models\User;
use App\Services\QrCodeGeneratorService;
use App\Services\Ticket\TicketService;
use Barryvdh\DomPDF\Facade\Pdf;
use GlennRaya\Xendivel\Xendivel;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentLinksService
{
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Generate a unique external ID for payment/ticket.
     */
    public function generateExternalId(): string
    {
        return 'INV-' . now()->format('Ymd') . '-' . mt_rand(10000, 99999);
    }

    public function handleCardPayment(Request $request): array
    {
        $rawResponse = Xendivel::payWithCard($request)->getResponse();
        $payment = $this->normalizePaymentResponse($rawResponse, 'card');

        Log::info('normalized_card_payment', [$payment]);

        if ($payment->status !== 'CAPTURED') {
            throw new \Exception("Card payment failed. Status: {$payment->status}");
        }

        return $this->finalizeSuccessfulPayment($payment);
    }

    public function handleEwalletPayment(Request $request): array
    {
        $rawResponse = Xendivel::payWithEwallet($request)->getResponse();

        Log::info('raw_ewallet_response', (array)$rawResponse);

        // Just return raw response with checkout_url
        return (array)$rawResponse;
    }

    public function finalizeSuccessfulPayment(object $payment): array
    {
        Log::info('Finalize successful payment received.', [
            'external_id' => $payment->external_id ?? $payment->reference_id,
            'amount'      => $payment->paid_amount ?? null,
        ]);

        $firstName = $payment->on_demand_payload->first_name ?? 'Guest';
        $lastName  = $payment->on_demand_payload->last_name ?? 'User';

        $customerEmail = $payment->payer_email
            ?? ($payment->on_demand_payload->email ?? null);

        $userPayload = $this->buildUserPayload($firstName, $lastName, $customerEmail);

        $user = null;
        $generatedPassword = null;

        // --- Step 1: Try to find by email first ---
        if ($customerEmail) {
            $user = User::where('email', $customerEmail)->first();

            if ($user) {
                // Update only allowed fields (exclude wallet, points, email if you don’t want to overwrite)
                $user->first_name         = $userPayload['first_name'];
                $user->last_name          = $userPayload['last_name'];
                $user->name               = $userPayload['name'];
                $user->password           = $userPayload['password'];
                $user->membership_type_id = $userPayload['membership_type_id'];
                $user->referral_code      = $userPayload['referral_code'];
                $user->email_verified_at  = $userPayload['email_verified_at'];
                $user->save();

                $generatedPassword = $userPayload['plainPassword'];
            }
        }

        // --- Step 2: If no user found by email, try first_name + last_name + password NULL ---
        if (!$user) {
            $user = User::whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
                ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
                ->whereNull('password')
                ->first();

            if ($user) {
                // Update allowed fields
                $user->first_name         = $userPayload['first_name'];
                $user->last_name          = $userPayload['last_name'];
                $user->name               = $userPayload['name'];
                $user->password           = $userPayload['password'];
                $user->membership_type_id = $userPayload['membership_type_id'];
                $user->referral_code      = $userPayload['referral_code'];
                $user->email_verified_at  = $userPayload['email_verified_at'];
                $user->save();

                $generatedPassword = $userPayload['plainPassword'];

                // Use email temporarily for downstream logic, but do NOT save to DB
                $temporaryEmail = $customerEmail;
            }
        }

        // --- Step 3: If still no user, create new user with unique email ---
        if (!$user) {
            // Ensure email is unique
            if (!$customerEmail || User::where('email', $customerEmail)->exists()) {
                $customerEmail = strtolower($firstName . '.' . $lastName) . '.' . uniqid() . '@noemail.local';
            }

            $userPayload['email'] = $customerEmail;
            $user = User::create($userPayload);
            $generatedPassword = $userPayload['plainPassword'];
        }

        // --- Step 4: Handle ticket creation ---
        $event       = Event::getActiveCampaignEvent();
        $ticketPrice = (float) $payment->paid_amount;
        $externalId  = $payment->external_id ?? $payment->reference_id;

        $ticket = $this->ticketService->createTicket($user, $event, $externalId, $ticketPrice);

        // --- Step 5: Build invoice ---
        $items = [
            ['item' => $event->name, 'price' => $ticketPrice, 'quantity' => 1],
        ];

        $invoiceData = $this->buildInvoiceData($payment, $user, $items, $event);
        $filename    = $this->generateInvoice($invoiceData);

        // --- Step 6: Send emails ---
        $this->sendEmails($user, $generatedPassword, $invoiceData, $ticket, $filename);

        return [
            'status'      => 'success',
            'payment'     => $payment,
            'invoice_url' => asset("storage/invoices/{$filename}"),
        ];
    }


    public function finalizeSuccessfulPaymentOld($payment): array
    {
        Log::info('payment', [$payment]);

        $firstName = $payment->on_demand_payload->first_name ?? 'Guest';
        $lastName  = $payment->on_demand_payload->last_name ?? 'User';

        // Step 1: Check if Xendit provided an email
        $customer_email = $payment->payer_email ?? ($payment->on_demand_payload->email ?? null);

        if ($customer_email) {
            // Normal flow
            $userPayload = $this->buildUserPayload($firstName, $lastName, $customer_email);

            $user = User::where('email', $customer_email)->whereNull('password')->first();

            if ($user) { // Naay email og nag match sa user email nga ge create during sa registration webhook

                // Update only allowed fields for existing user
                $user->name               = $userPayload['name'];
                $user->password           = $userPayload['password'];
                $user->membership_type_id = $userPayload['membership_type_id'];
                $user->referral_code      = $userPayload['referral_code'];
                $user->email_verified_at  = $userPayload['email_verified_at'];
                $user->save();

            } else {

                // Naay email pero wala nag match sa user email nga ge create during sa registration webhook
                // Need nga e find ang first name og last name sa DB

                $user = User::whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
                    ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
                    ->whereNull('password')
                    ->first();

                // Override email locally
                if ($user) {
                    $user->name               = $userPayload['name'];
                    $user->password           = $userPayload['password'];
                    $user->membership_type_id = $userPayload['membership_type_id'];
                    $user->referral_code      = $userPayload['referral_code'];
                    $user->email_verified_at  = $userPayload['email_verified_at'];
                    $user->save();

                    $user->email = $customer_email;
                } else {
                    $user = User::create($userPayload);
                }

            }
        } else {
            // Step 2: Search user by first_name + last_name (case-insensitive)
            $user = User::whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
                ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
                ->whereNull('password')
                ->first();

            if (!$user) {
                // Generate unique placeholder email
                $customer_email = strtolower($firstName . '.' . $lastName) . '.' . uniqid() . '@noemail.local';

                $userPayload = $this->buildUserPayload($firstName, $lastName, $customer_email);
                $user = User::create($userPayload);
            }
        }

        $isNewUser = $user->wasRecentlyCreated;
        $generatedPassword = $isNewUser ? $userPayload['plainPassword'] : null;

        $event = Event::getActiveCampaignEvent();
        $ticketPrice = $payment->paid_amount;
        $externalId = $payment->external_id ?? $payment->reference_id;

        $ticket = $this->ticketService->createTicket($user, $event, $externalId, $ticketPrice);

        $items = [
            ['item' => $event->name, 'price' => $ticketPrice, 'quantity' => 1]
        ];

        $invoiceData = $this->buildInvoiceData($payment, $user, $items, $event);
        $filename = $this->generateInvoice($invoiceData);

        $this->sendEmails($user, $generatedPassword, $invoiceData, $ticket, $filename);

        return [
            'status' => 'success',
            'payment' => $payment,
            'invoice_url' => asset('storage/invoices/' . $filename),
        ];
    }


    public function normalizePaymentResponse(object $raw): object
    {
        switch ($raw->payment_channel) {
            case 'GCASH': $wallet_logo = 'gcash.png'; break;
            case 'PAYMAYA': $wallet_logo = 'paymaya.png'; break;
            case 'SHOPEEPAY': $wallet_logo = 'shopeepay.png'; break;
            case 'BPI_DIRECT_DEBIT':
            case 'DD_BPI_ONLINE_BANKING': // 👈 new case from your log
                $wallet_logo = 'bpi.png'; break;
            case 'RCBC_DIRECT_DEBIT': $wallet_logo = 'rcbc.png'; break;
            case 'UBP_DIRECT_DEBIT': $wallet_logo = 'ubp.png'; break;
            case 'CEBUANA': $wallet_logo = 'cebuana.png'; break;
            case 'LBC': $wallet_logo = 'lbc.png'; break;
            case 'QRPH': $wallet_logo = 'qrph.png'; break;
            default: $wallet_logo = null; break;
        }

        $raw->card_brand = 'E-WALLET';
        $raw->masked_card_number = '••••';
        $raw->authorized_amount = $raw->paid_amount;
        $raw->approval_code = $raw->id;
        $raw->wallet_logo = $wallet_logo;

        // Convert nested arrays to objects
        if (isset($raw->on_demand_payload) && is_array($raw->on_demand_payload)) {
            $raw->on_demand_payload = (object) $raw->on_demand_payload;
        }

        if (isset($raw->items) && is_array($raw->items)) {
            $raw->items = array_map(fn($i) => (object) $i, $raw->items);
        }

        return $raw;
    }


    private function buildUserPayload(string $first_name, string $last_name, string $email): array
    {
        $plainPassword = Str::random(12);
        $membership = MembershipType::where('name', 'United Moviegoers and Musiclovers Dream Club International')->first();

        return [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'name' => "$first_name $last_name",
            'email' => $email,
            'password' => Hash::make($plainPassword),
            'membership_type_id' => $membership->id ?? null,
            'referral_code' => strtoupper(Str::random(8)),
            'email_verified_at' => now(),
            'bpp_wallet_balance' => 0.00,
            'bpp_points_balance' => 0.00,
            'plainPassword' => $plainPassword,
        ];
    }

    private function buildInvoiceData(object $charge, User $user, array $items, Event $event): array
    {
        return [
            'invoice_number' => $charge->external_id ?? uniqid('INV-'),
            'card_type' => $charge->card_brand,
            'wallet_logo' => $charge->wallet_logo??'',
            'masked_card_number' => $charge->masked_card_number ?? '',
            'currency' => $charge->currency,
            'charge_date' => date('F j, Y H:i A', strtotime($charge->created ?? now())),
            'merchant' => [
                'name' => config('app.name'),
                'address' => 'Cainta, Rizal 1900',
                'phone' => '+63 971-444-1234',
                'email' => 'support@bpphp.fun',
            ],
            'customer' => [
                'first_name' => $user->first_name,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->mobile_no ?? 'N/A',
                'address' => $user->city_or_province ?? 'N/A',
            ],
            'items' => $items,
            'tax_rate' => 0,
            'tax_id' => '123-456-789',
            'footer_note' => 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
            'footer_note_right' => 'Transaction via ' . ($charge->descriptor ?? 'Payment Gateway'),
            'approval_code' => $charge->approval_code ?? null,
            'total_amount' => $charge->authorized_amount,
            'referral_code' => $user->referral_code
        ];
    }

    private function sendEmails(User $user, ?string $password, array $invoiceData, Ticket $ticket, string $filename): void
    {
        if ($password) {
            try {
                Mail::to($user->email)->send(new WelcomeNewUserMail($user, $password));
            } catch (\Exception $e) {
                Log::error("Welcome email failed: {$e->getMessage()}");
            }
        }

        try {
            Mail::to(new Address($user->email, $user->name))
                ->send(new InvoiceMail($invoiceData, $ticket, $filename));
        } catch (\Exception $e) {
            Log::error("Invoice email failed: {$e->getMessage()}");
        }
    }

    protected function generateInvoice(array $invoice_data): string
    {
        File::ensureDirectoryExists(storage_path('app/invoices'));

        $filename = 'invoice-' . $invoice_data['invoice_number'] . '.pdf';
        $savePath = storage_path('app/invoices/' . $filename);

        Pdf::loadView('invoice.template', compact('invoice_data'))
            ->setPaper('a4', 'portrait')
            ->save($savePath);

        return $filename;
    }
}

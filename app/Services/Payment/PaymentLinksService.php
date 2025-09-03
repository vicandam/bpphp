<?php

namespace App\Services\Payment;

use App\Mail\InvoiceMail;
use App\Mail\WelcomeNewUserMail;
use App\Models\Event;
use App\Models\MembershipType;
use App\Models\Ticket;
use App\Models\User;
use App\Services\QrCodeGeneratorService;
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
    protected QrCodeGeneratorService $qrCodeGeneratorService;

    public function __construct(QrCodeGeneratorService $qrCodeGeneratorService)
    {
        $this->qrCodeGeneratorService = $qrCodeGeneratorService;
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

    public function finalizeSuccessfulPayment($payment): array
    {
        Log::info('payment', [$payment]);

        $firstName = $payment->on_demand_payload->first_name ?? 'Guest';
        $lastName  = $payment->on_demand_payload->last_name ?? 'User';

        // Step 1: Check if Xendit provided an email
        $customer_email = $payment->payer_email ?? ($payment->on_demand_payload->email ?? null);

        if ($customer_email) {
            // Normal flow
            $userPayload = $this->buildUserPayload($firstName, $lastName, $customer_email);
            $user = User::firstOrCreate(['email' => $customer_email], $userPayload);
        } else {
            // Step 2: Search user by first_name + last_name (case-insensitive)
            $user = User::whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
                ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
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

        $ticket = $this->createTicket($user, $event, $externalId, $ticketPrice);

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

    private function createTicket(User $user, Event $event, string $externalId, float $amount): Ticket
    {
        $points = floor($amount / 200) * 1;
        $qrCodePath = $this->qrCodeGeneratorService->generateForTicket($externalId);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'ticket_code' => $externalId,
            'is_redeemed' => false,
            'joy_points_earned' => $points,
            'purchase_date' => now(),
            'virtual_membership_card_qr' => $qrCodePath,
        ]);

        $user->increment('bpp_points_balance', $points);

        return $ticket;
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
                'name' => 'BPPHP.fun',
                'address' => 'Metro Manila',
                'phone' => '+63 971-444-1234',
                'email' => 'support@bpphp.fun',
            ],
            'customer' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->mobile_no ?? 'N/A',
                'address' => $user->city_or_province ?? 'N/A',
            ],
            'items' => $items,
            'tax_rate' => 0.12,
            'tax_id' => '123-456-789',
            'footer_note' => 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
            'footer_note_right' => 'Transaction via ' . ($charge->descriptor ?? 'Payment Gateway'),
            'approval_code' => $charge->approval_code ?? null,
            'total_amount' => $charge->authorized_amount,
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

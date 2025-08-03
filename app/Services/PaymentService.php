<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Mail\WelcomeNewUserMail;
use App\Mail\InvoiceMail;
use App\Services\QrCodeGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use GlennRaya\Xendivel\Xendivel;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PaymentService
{
    protected QrCodeGeneratorService $qrCodeGeneratorService;

    public function __construct(QrCodeGeneratorService $qrCodeGeneratorService)
    {
        $this->qrCodeGeneratorService = $qrCodeGeneratorService;
    }

    public function handleCardPayment(Request $request): array
    {
        $payment = Xendivel::payWithCard($request)->getResponse();
        Log::info('payment', [$payment]);

        if ($payment->status !== 'CAPTURED') {
            throw new \Exception('Payment was not successful.');
        }

        $charge = $payment;
        $customer_email = $charge->metadata->card_holder_email;
        $userPayload = $this->buildUserPayload($charge);

        $user = User::firstOrCreate(
            ['email' => $customer_email],
            $userPayload
        );

        $isNewUser = $user->wasRecentlyCreated;
        $generatedPassword = $isNewUser ? $userPayload['plainPassword'] : null;

        $customer = [
            'name' => $user->name,
            'address' => $user->city_or_province ?? 'N/A',
            'email' => $user->email,
            'phone' => $user->mobile_no ?? 'N/A',
        ];

        $event = Event::where('campaign', 1)->first();
        $ticketPrice = $event->ticket_price;

        $joyPointsEarned = floor($ticketPrice / 500) * 10; // 1 joy point for every P500, 1 joy point = Php10
        $externalId = $charge->external_id;
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

        $items = $request->input('items', [
            ['item' => $event->name, 'price' => $ticketPrice, 'quantity' => 1]
        ]);

        $invoice_data = $this->buildInvoiceData($charge, $customer, $items, $event);
        $filename = $this->generateInvoice($invoice_data);

        if ($generatedPassword) {
            try {
                Mail::to($user->email)->send(new WelcomeNewUserMail($user, $generatedPassword));
            } catch (\Exception $e) {
                Log::error("Error sending welcome email to {$user->email}", [$e->getMessage()]);
            }
        }

        try {
            Mail::to(new Address($user->email, $user->name))
                ->send(new InvoiceMail($invoice_data, $ticket));

        } catch (\Exception $e) {
            Log::error("Error sending invoice to {$user->email}", [$e->getMessage()]);
        }

        return [
            'status' => 'success',
            'payment' => $payment,
            'invoice_url' => asset('storage/invoices/' . $filename),
        ];
    }

    protected function buildUserPayload($charge): array
    {
        $first_name = $charge->metadata->card_holder_first_name;
        $last_name = $charge->metadata->card_holder_last_name;
        $plainPassword = Str::random(12);

        $defaultMembership = \App\Models\MembershipType::where('name', 'United Moviegoers and Musiclovers Dream Club International')->first();

        return [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'name' => "$first_name $last_name",
            'password' => Hash::make($plainPassword),
            'membership_type_id' => $defaultMembership->id ?? null,
            'referral_code' => Str::upper(Str::random(8)),
            'email_verified_at' => now(),
            'bpp_wallet_balance' => 0.00,
            'bpp_points_balance' => 0.00,
            'plainPassword' => $plainPassword, // just for passing to mail
        ];
    }

    protected function buildInvoiceData($charge, $customer, $items, $event): array
    {
        return [
            'invoice_number' => $charge->external_id ?? uniqid('INV-'),
            'card_type' => $charge->card_brand ?? 'CARD',
            'masked_card_number' => $charge->masked_card_number ?? '',
            'currency' => $charge->currency ?? 'PHP',
            'charge_date' => date('F j, Y H:i A', strtotime($charge->created ?? now())),
            'merchant' => [
                'name' => 'BPPHP.fun',
                'address' => 'Metro Manila',
                'phone' => '+63 971-444-1234',
                'email' => 'support@bpphp.fun',
            ],
            'customer' => $customer,
            'items' => $items,
            'tax_rate' => 0.12,
            'tax_id' => '123-456-789',
            'footer_note' => 'Transaction approved by ' . ($charge->issuing_bank_name ?? 'your bank') .
                '. Descriptor: ' . ($charge->descriptor ?? ''),
            'approval_code' => $charge->approval_code ?? null,
            'total_amount' => $charge->authorized_amount ?? 0,
        ];
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

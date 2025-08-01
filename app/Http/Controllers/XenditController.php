<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Mail\WelcomeNewUserMail;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Services\QrCodeGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use GlennRaya\Xendivel\Xendivel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class XenditController extends Controller
{
    protected $qrCodeGeneratorService;

    public function __construct(QrCodeGeneratorService $qrCodeGeneratorService)
    {
        $this->qrCodeGeneratorService = $qrCodeGeneratorService;
    }

    function payWithCard(Request $request)
    {
        // Step 1: Perform the payment
        $payment = Xendivel::payWithCard($request)->getResponse();

        // Log payment
        Log::info('payment', [$payment]);

        // Step 2: Extract relevant data
        $charge = $payment;

        // The CAPTURED status means the payment went successful.
        // And the customer's card was successfully charged.
        if ($payment->status == 'CAPTURED') {
            $customer_email = $charge->metadata->card_holder_email;

            $user = User::where('email', $customer_email)->first();
            $generatedPassword = null;

            if ($user) {
                $customer_name = $user->name;
                $phone = $user->mobile_no;
                $address = $user->city_or_province;

                Log::info($request->email . ' This email already exists. Record just updated.');
            } else {

                $first_name = $payment->metadata->card_holder_first_name;
                $last_name = $payment->metadata->card_holder_last_name;
                $customer_name = $first_name. ' ' . $last_name;

                Log::info("Creating user for $customer_email");

                $plainPassword = Str::random(12);
                $hashedPassword = Hash::make($plainPassword);

                $defaultMembership = \App\Models\MembershipType::where('name', 'United Moviegoers and Musiclovers Dream Club International')->first();

                $user = User::create([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'name' => $customer_name,
                    'email' => $customer_email,
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

            $customer = [
                'name' => $customer_name,
                'address' => $address ?? 'N/A',
                'email' => $customer_email,
                'phone' => $phone ?? 'N/A',
            ];

            $event = Event::where('campaign', 1)->first();

            $externalId = $charge->external_id;
            $ticketPrice = $event->ticket_price;

            // Create Ticket
            $joyPointsEarned = floor($ticketPrice / 500) * 10; // 1 joy point for every P500, 1 joy point = Php10
            $qrCodePath = $this->qrCodeGeneratorService->generateForTicket($externalId);

            $ticket = Ticket::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'ticket_code' => $externalId,
                //'ticket_code' => strtoupper(Str::random(8)),
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

            $items = $request->input('items', [
                ['item' => $event->name, 'price' => $ticketPrice, 'quantity' => 1]
            ]);

            // Step 3: Prepare invoice data
            $invoice_data = [
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


            // Step 4: Generate and store PDF
            $filename = 'invoice-' . $invoice_data['invoice_number'] . '.pdf';
            $savePath = storage_path('app/invoices/' . $filename);

            // Email credentials
            if ($filename) {
                try {
                    Mail::to($user->email)->send(new InvoiceMail($invoice_data));
                    Log::info("Invoice email sent to {$user->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send invoice email: " . $e->getMessage());
                }
            }

            File::ensureDirectoryExists(storage_path('app/invoices'));

            Pdf::loadView('invoice.template', compact('invoice_data'))
                ->setPaper('a4', 'portrait')
                ->save($savePath);

            // Step 6: Return the payment response + optional link to invoice
            return response()->json([
                'status' => 'success',
                'payment' => $payment,
                'invoice_url' => asset('storage/invoices/' . $filename), // if you symlinked storage:link
            ]);
        }
    }

//{
//"status": "CAPTURED",
//"capture_amount": 5198,
//"metadata": {},
//"credit_card_token_id": "656ed874edab5300169c3092",
//  "business_id": "6551f678273a62fd8d86e25a",
//  "merchant_id": "104019905",
//  "merchant_reference_code": "656ed874edab5300169c3091",
//  "eci": "02",
//  "charge_type": "SINGLE_USE_TOKEN",
//  "card_type": "CREDIT",
//  "ucaf": "AJkBBkhgQQAAAE4gSEJydQAAAAA=",
//  "authorization_id": "656ed87c23f3c20015e2fb95",
//  "bank_reconciliation_id": "7017631974056110603955",
//  "issuing_bank_name": "PT BANK NEGARA INDONESIA TBK",
//  "cvn_code": "M",
//  "created": "2023-12-05T07:59:58.453Z",
//  "id": "656ed87e23f3c20015e2fb96",
//  "card_fingerprint": "61d6ed632aa321002350e0b2"
//}

//"authorized_amount": 5198,
//  "approval_code": "831000",
//  "descriptor": "XDT*JSON FAKERY",
//"currency": "PHP",
//  "external_id": "43565633-dd58-47ae-bbe6-648f78d6652c",
//  "card_brand": "MASTERCARD",
//  "masked_card_number": "520000XXXXXX1005",

    public function generateInvoice(array $charge, array $items, array $customer)
    {
        $invoice_data = [
            'invoice_number' => $charge['external_id'] ?? uniqid('INV-'),
            'card_type' => $charge['card_brand'] ?? 'CARD',
            'masked_card_number' => $charge['masked_card_number'] ?? '',
            'currency' => $charge['currency'] ?? 'PHP',
            'charge_date' => date('F j, Y H:i A', strtotime($charge['created'] ?? now())),
            'merchant' => [
                'name' => 'Xendivel LLC',
                'address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
                'phone' => '+63 971-444-1234',
                'email' => 'xendivel@example.com',
            ],
            'customer' => $customer,
            'items' => $items,
            'tax_rate' => 0.12,
            'tax_id' => '123-456-789',
            'footer_note' => 'Transaction approved by ' . ($charge['issuing_bank_name'] ?? 'your bank') .
                '. Descriptor: ' . ($charge['descriptor'] ?? ''),
            'approval_code' => $charge['approval_code'] ?? null,
            'total_amount' => $charge['authorized_amount'] ?? 0,
        ];

        $pdf = Pdf::loadView('invoice.template', $invoice_data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('invoice-' . $invoice_data['invoice_number'] . '.pdf');
    }

    public function generateInvoiceTemplate()
    {
        $invoice_data = [
            'invoice_number' => 1000023,
            'card_type' => 'MASTERCARD',
            'masked_card_number' => '400000XXXXXX0002',
            'merchant' => [
                'name' => 'Xendivel LLC',
                'address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
                'phone' => '+63 971-444-1234',
                'email' => 'xendivel@example.com',
            ],
            'customer' => [
                'name' => 'Victoria Marini',
                'address' => 'Alex Johnson, 4457 Pine Circle, Rivertown, Westhaven, 98765, Silverland',
                'email' => 'victoria@example.com',
                'phone' => '+63 909-098-654',
            ],
            'items' => [
                ['item' => 'iPhone 15 Pro Max', 'price' => 1099, 'quantity' => 5],
                ['item' => 'MacBook Pro 16" M3 Max', 'price' => 2499, 'quantity' => 3],
                ['item' => 'Apple Pro Display XDR', 'price' => 5999, 'quantity' => 2],
                ['item' => 'Pro Stand', 'price' => 999, 'quantity' => 2],
            ],
            'tax_rate' => .12,
            'tax_id' => '123-456-789',
            'footer_note' => 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
        ];

        //return view('invoice.template', compact('invoice_data'));
        $pdf = Pdf::loadView('invoice.template', compact('invoice_data'))
            ->setPaper('a4', 'portrate');

        return $pdf->download('invoice-'.$invoice_data['invoice_number'].'.pdf');
    }
}

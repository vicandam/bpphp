{{-- invoice/template.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF‑8">
    <meta name="viewport" content="width=device‑width,initial‑scale=1">
    <title>Invoice #{{ $invoice_data['invoice_number'] }}</title>
    <style>
        body { font-family: DejaVu Sans, Inter, sans-serif; color: #374151; font-size: 12px; }
        .header { background: linear-gradient(to top, #e2e8f0, #fff); padding: 40px; }
        .container {
            padding: 0 40px;
            box-sizing: border-box;
        }

        .container table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .container th, .container td {
            padding: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        .container .text-right {
            text-align: right;
        }

        .total-box {
            font-weight: bold;
            font-size: 1.1rem;
            margin-top: 10px;
        }

        .footer-note {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #444;
        }
        .flex { display: flex; justify-content: space-between; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border-bottom: 1px solid #e5e7eb;
            padding: .5rem;
        }
        .no-border td {
            border-bottom: none !important;
            padding: 0;
        }

        th { text-align: left; font-weight: 600; }
        .text-right { text-align: right; }
        .total-box { background: black; color: white; padding: .5rem 1rem; border-radius: 6px; display: inline-flex; }
        .footer-note { margin-top: 40px; font-size: 10px; }

        @page {
            margin: 40px 40px 100px 40px;
        }

        .page {
            /*position: relative;*/
            /*padding-bottom: 100px; !* room for footer *!*/
            height: auto;
        }

        .footer-left {
            position: absolute;
            bottom: 20px;
            left: 40px;
            font-size: 10px;
            color: #444;
            width: 50%;
        }

        .footer-right {
            position: absolute;
            bottom: 20px;
            right: 40px;
            font-size: 10px;
            text-align: right;
            color: #444;
            width: 40%;
        }
    </style>
</head>
<body>

<div class="page">
    <div class="header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr valign="top">
                {{-- Left column: logo + merchant --}}
                <td style="width: 50%; vertical-align: top;">
                    <table class="no-border" style="width: 100%;">
                        <tr>
                            <td style="width: 60px;">
                                <a href="https://app.bpphp.fun/login">
                                    <img src="{{public_path('images/bpphp.png')}}" alt="" style="height: 60px; width: auto;">
                                </a>
                            </td>
                            <td style="padding-left: 10px;">
                                <div style="margin-bottom: 5px;">Invoice #: {{ $invoice_data['invoice_number'] }}</div>
                                <div style="font-weight: bold; margin-bottom: 3px;">Merchant</div>
                                <div>
                                    {{ $invoice_data['merchant']['name'] }}<br>
                                    {{ $invoice_data['merchant']['address'] }}<br>
                                    Phone: {{ $invoice_data['merchant']['phone'] }}<br>
                                    Email: {{ $invoice_data['merchant']['email'] }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>

                {{-- Right column: customer --}}
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <div style="margin-bottom: 5px;">Date: {{ now()->format('M. d, Y \a\t g:ia') }}</div>
                    <div style="font-weight: bold; margin-bottom: 3px;">Customer</div>
                    <div>
                        {{ $invoice_data['customer']['name'] }}<br>
                        @if($invoice_data['customer']['address'] != 'N/A'){{ $invoice_data['customer']['address'] }}<br>@endif
                        @if($invoice_data['customer']['phone'] != 'N/A')Phone: {{ $invoice_data['customer']['phone'] }}<br>@endif
                        Email: {{ $invoice_data['customer']['email'] }}
                    </div>
                </td>
            </tr>
        </table>
    </div>


    <div class="container">
        <table>
            <thead>
            <tr><th>Description</th><th>Qty</th><th class="text-right">Unit Price</th><th class="text-right">Subtotal</th></tr>
            </thead>
            <tbody>
            @php $total_price = 0; @endphp
            @foreach($invoice_data['items'] as $item)
                @php $subtotal = $item['price'] * $item['quantity']; $total_price += $subtotal; @endphp
                <tr>
                    <td>{{ $item['item'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td class="text-right">₱{{ number_format($item['price'],2) }}</td>
                    <td class="text-right">₱{{ number_format($subtotal,2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px; text-align: right;">
            <p>Subtotal: ₱{{ number_format($total_price,2) }}</p>
            @php $tax_amount = $total_price * $invoice_data['tax_rate']; @endphp
{{--            <p>Tax ({{ $invoice_data['tax_rate']*100 }}%): ₱{{ number_format($tax_amount,2) }}</p>--}}
            <div class="total-box">
                <span style="margin-right: 1rem;">TOTAL</span>
{{--                <span>₱{{ number_format($total_price + $tax_amount,2) }}</span>--}}
                <span>₱{{ number_format($total_price,2) }}</span>
            </div>
            <p>
                {{-- Visa icon --}}
                @if ($invoice_data['card_type'] === 'VISA')
                    <img src="{{ public_path('images/payments/visa.png') }}" height="10" alt="VISA">
                    <span>{{ $invoice_data['masked_card_number'] }}</span>
                @endif

                {{-- Mastercard icon --}}
                @if ($invoice_data['card_type'] === 'MASTERCARD')
                    <img src="{{ public_path('images/payments/master-card.png') }}" height="10" alt="MASTERCARD">
                    <span>{{ $invoice_data['masked_card_number'] }}</span>
                @endif
            </p>
        </div>
    </div>

    @if(!empty($invoice_data['footer_note']))
        <div class="footer-left">
            <strong>Dear customer,</strong><br>{{ $invoice_data['footer_note'] }}
        </div>
    @endif

    @if(!empty($invoice_data['footer_note_right']))
        <div class="footer-right">
            @if ($invoice_data['card_type'] === 'E-WALLET')
                <p style="margin-bottom: -10px">
                    <img src="{{ public_path('images/payments') . '/' . $invoice_data['wallet_logo'] }}" height="40" alt="{{$invoice_data['wallet_logo']}}">
                </p>
            @endif
            {!! nl2br(e($invoice_data['footer_note_right'])) !!}<br>
            <strong>Tax ID/VAT Number:</strong> {{ $invoice_data['tax_id'] }}
        </div>
    @endif
</div>
</body>
</html>

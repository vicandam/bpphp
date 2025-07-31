{{-- invoice/template.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF‑8">
    <meta name="viewport" content="width=device‑width,initial‑scale=1">
    <title>Invoice #{{ $invoice_data['invoice_number'] }}</title>
    <style>
        body { font-family: Inter, sans-serif; color: #374151; font-size: 12px; }
        .header { background: linear-gradient(to top, #e2e8f0, #fff); padding: 40px; }
        .container {
            width: 100%;
            margin: 0;
            padding: 0 40px;
            box-sizing: border-box;
        }
        .flex { display: flex; justify-content: space-between; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: .5rem; }
        th { text-align: left; font-weight: 600; }
        .text-right { text-align: right; }
        .total-box { background: black; color: white; padding: .5rem 1rem; border-radius: 6px; display: inline-flex; }
        .footer-note { margin-top: 40px; font-size: 10px; }
    </style>
</head>
<body>
<div class="header flex">
    {{-- Merchant --}}
    <div>
        <!-- Logo SVG -->
        <svg width="42" height="42">…</svg>
        <p>Invoice #: {{ $invoice_data['invoice_number'] }}</p>
        <p><strong>Merchant</strong><br>{{ $invoice_data['merchant']['name'] }}<br>{{ $invoice_data['merchant']['address'] }}<br>Phone: {{ $invoice_data['merchant']['phone'] }}<br>Email: {{ $invoice_data['merchant']['email'] }}</p>
    </div>
    {{-- Customer --}}
    <div style="text-align: right;">
        <p>Date: {{ now()->format('M. d, Y \a\t g:ia') }}</p>
        <p><strong>Customer</strong><br>{{ $invoice_data['customer']['name'] }}<br>{{ $invoice_data['customer']['address'] }}<br>Phone: {{ $invoice_data['customer']['phone'] }}<br>Email: {{ $invoice_data['customer']['email'] }}</p>
    </div>
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
                <td class="text-right">${{ number_format($item['price'],2) }}</td>
                <td class="text-right">${{ number_format($subtotal,2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: right;">
        <p>Subtotal: ${{ number_format($total_price,2) }}</p>
        @php $tax_amount = $total_price * $invoice_data['tax_rate']; @endphp
        <p>Tax ({{ $invoice_data['tax_rate']*100 }}%): ${{ number_format($tax_amount,2) }}</p>
        <div class="total-box">
            <span style="margin-right: 1rem;">TOTAL</span>
            <span>${{ number_format($total_price + $tax_amount,2) }}</span>
        </div>

        <p>
            {{-- Visa icon --}}
            @if ($invoice_data['card_type'] === 'VISA')
                <img src="{{ public_path('images/visa-colored.png') }}" height="10" alt="VISA">
            @endif

            {{-- Mastercard icon --}}
            @if ($invoice_data['card_type'] === 'MASTERCARD')
                <img src="{{ public_path('images/master-colored.png') }}" height="10" alt="MASTERCARD">
            @endif

            <span>{{ $invoice_data['masked_card_number'] }}</span>
        </p>
    </div>

    @if(!empty($invoice_data['footer_note']))
        <div class="footer-note">
            <strong>Dear customer,</strong><br>{{ $invoice_data['footer_note'] }}<br><strong>Tax ID/VAT Number:</strong> {{ $invoice_data['tax_id'] }}
        </div>
    @endif
</div>
</body>
</html>

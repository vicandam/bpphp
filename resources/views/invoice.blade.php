<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice_data['invoice_number'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 0; }
        .container { padding: 40px; }
        .header, .footer { width: 100%; text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .section { margin-bottom: 30px; }
        .info-table, .items-table { width: 100%; border-collapse: collapse; }
        .info-table td, .items-table th, .items-table td {
            border: 1px solid #ccc; padding: 8px; vertical-align: top;
        }
        .items-table th { background-color: #f4f4f4; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .mt-2 { margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>INVOICE</h1>
        <p>Invoice #: {{ $invoice_data['invoice_number'] }}</p>
    </div>

    <div class="section">
        <table class="info-table">
            <tr>
                <td>
                    <strong>From:</strong><br>
                    {{ $merchant['name'] }}<br>
                    {{ $merchant['address'] }}<br>
                    Phone: {{ $merchant['phone'] }}<br>
                    Email: {{ $merchant['email'] }}
                </td>
                <td>
                    <strong>To:</strong><br>
                    {{ $customer['name'] }}<br>
                    {{ $customer['address'] }}<br>
                    Phone: {{ $customer['phone'] }}<br>
                    Email: {{ $customer['email'] }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="items-table">
            <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Price</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Total</th>
            </tr>
            </thead>
            <tbody>
            @php $subtotal = 0; @endphp
            @foreach ($items as $item)
                @php $total = $item['price'] * $item['quantity']; $subtotal += $total; @endphp
                <tr>
                    <td>{{ $item['item'] }}</td>
                    <td class="text-right">${{ number_format($item['price'], 2) }}</td>
                    <td class="text-right">{{ $item['quantity'] }}</td>
                    <td class="text-right">${{ number_format($total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="section text-right">
        <p><strong>Subtotal:</strong> ${{ number_format($subtotal, 2) }}</p>
        <p><strong>Tax ({{ $tax_rate * 100 }}%):</strong> ${{ number_format($subtotal * $tax_rate, 2) }}</p>
        <p class="bold">Grand Total: ${{ number_format($subtotal * (1 + $tax_rate), 2) }}</p>
        <p class="mt-2">Tax ID: {{ $tax_id }}</p>
    </div>

    <div class="section">
        <strong>Payment Method:</strong> {{ $card_type }} ({{ $masked_card_number }})
    </div>

    <div class="footer">
        <p>{{ $footer_note }}</p>
    </div>
</div>
</body>
</html>

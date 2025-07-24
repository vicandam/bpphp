<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Redemption Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .status-card {
            max-width: 400px;
            text-align: center;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .status-card.success { background-color: #d4edda; border-color: #28a745; color: #155724; }
        .status-card.redeemed { background-color: #fff3cd; border-color: #ffc107; color: #856404; }
        .status-card.invalid, .status-card.error { background-color: #f8d7da; border-color: #dc3545; color: #721c24; }
        .status-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .status-icon.success { color: #28a745; }
        .status-icon.redeemed { color: #ffc107; }
        .status-icon.invalid, .status-icon.error { color: #dc3545; }
    </style>
</head>
<body>
<div class="card status-card {{ $status }}">
    @if ($status == 'success')
        <div class="status-icon success">&#10004;</div>
        <h1 class="mb-3">Redemption Successful!</h1>
    @elseif ($status == 'redeemed')
        <div class="status-icon redeemed">&#9888;</div>
        <h1 class="mb-3">Already Redeemed</h1>
    @elseif ($status == 'invalid' || $status == 'error')
        <div class="status-icon invalid">&#10006;</div>
        <h1 class="mb-3">Redemption Failed!</h1>
    @endif

    <p class="lead">{{ $message }}</p>

    @if ($status == 'error')
        <p class="text-muted mt-3">Please try again or contact event staff for assistance.</p>
    @endif
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Notification</title>
</head>
<body>
<h2>Login Alert</h2>
<p>A user just logged in:</p>
<ul>
    <li><strong>Name:</strong> {{ $user->name }}</li>
    <li><strong>Email:</strong> {{ $user->email }}</li>
    <li><strong>Time:</strong> {{ now()->toDayDateTimeString() }}</li>
</ul>
<p>Thanks,</p>
<p>BPPHP System</p>
</body>
</html>

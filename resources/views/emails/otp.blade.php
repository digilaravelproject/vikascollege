<!doctype html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>
    <p>Hi {{ $name ?? 'Applicant' }},</p>
    <p>Your one-time verification code (OTP) is:</p>
    <h2>{{ $otp }}</h2>
    <p>This code will expire in 10 minutes.</p>
</body>

</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f4f4f4; padding:20px;">
    <div style="max-width:600px; margin:0 auto; background:#ffffff; padding:20px; border-radius:8px;">
        <h2 style="color:#333;">{{ $title }}</h2>
        <p>Hello,</p>
        <p>Your verification code is:</p>
        <h1 style="color:#2c3e50; letter-spacing:5px;">{{ $otp }}</h1>
        <p>This code will expire in 10 minutes.</p>
        <p style="margin-top:20px;">Best regards,<br><strong>Your App Team</strong></p>
    </div>
</body>
</html>

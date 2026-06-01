<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mailgun test</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #111;">
    <h1 style="font-size: 18px;">IDG — Mailgun connection test</h1>
    <p>If you received this message, Mailgun is configured correctly in the IDG application.</p>
    <p><strong>Sent at:</strong> {{ $sentAt }}</p>
    <p style="color: #666; font-size: 12px;">This is an automated test email from {{ config('app.name') }}.</p>
</body>
</html>

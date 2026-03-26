<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">

        <h1 style="color: #6366f1;">🔐 Verification Code</h1>

        <p>Hello, <strong>{{ is_object($user) && isset($user->name) ? $user->name : (is_array($user) && isset($user['name']) ? $user['name'] : 'User') }}</strong>!</p>

        <p>Your verification code is:</p>

        <div style="background: #f1f5f9; padding: 20px; text-align: center; border-radius: 8px; margin: 20px 0;">
            <span style="font-size: 32px; font-weight: bold; color: #6366f1; letter-spacing: 8px;">
                {{ $otp }}
            </span>
        </div>

        <p>This code will expire in 2 minutes.</p>

        <p style="margin-top: 30px; padding: 15px; background: #fef2f2; border-left: 4px solid #fecaca; color: #991b1b;">
            <strong>Security Notice:</strong> Never share this code with anyone. If you didn't request this code, please ignore this email.
        </p>

        <p style="margin-top: 30px; font-size: 12px; color: #64748b;">
            This email was sent to {{ is_object($user) && isset($user->email) ? $user->email : (is_array($user) && isset($user['email']) ? $user['email'] : 'you') }} from {{ $appName }}.<br>
            © {{ date('Y') }} {{ $appName }}. All rights reserved.
        </p>

    </div>

</body>
</html>
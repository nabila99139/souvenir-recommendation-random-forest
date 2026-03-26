<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Account</title>
    <style>
        /* Reset styles for email clients */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.6;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .email-container {
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
            font-weight: 700;
        }

        .header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message {
            font-size: 15px;
            color: #475569;
            margin-bottom: 30px;
        }

        .otp-box {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border: 2px dashed #6366f1;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }

        .otp-code {
            font-size: 48px;
            font-weight: 800;
            color: #6366f1;
            letter-spacing: 12px;
            font-family: 'Courier New', monospace;
            margin: 0;
        }

        .otp-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #64748b;
            margin-bottom: 10px;
        }

        .instructions {
            background-color: #f8fafc;
            border-left: 4px solid #6366f1;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .instructions h4 {
            margin: 0 0 12px 0;
            color: #1e293b;
            font-size: 16px;
        }

        .instructions ul {
            margin: 0;
            padding-left: 20px;
        }

        .instructions li {
            margin-bottom: 8px;
            font-size: 14px;
            color: #475569;
        }

        .security-notice {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 15px 20px;
            margin: 25px 0;
            font-size: 13px;
            color: #991b1b;
        }

        .security-notice strong {
            display: block;
            margin-bottom: 8px;
        }

        .footer {
            background-color: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #64748b;
        }

        .footer a {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .expiry {
            display: inline-block;
            background-color: #f1f5f9;
            color: #64748b;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-top: 10px;
        }

        .app-name {
            color: #6366f1;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="header">
                <h1>✨ Verification Code</h1>
                <p>{{ $appName }} Souvenir Recommendation</p>
            </div>

            <!-- Content -->
            <div class="content">
                <p class="greeting">
                    Hi {{ $user->name }}, 👋
                </p>

                <p class="message">
                    We received a login request for your <span class="app-name">{{ $appName }}</span> account. To complete the verification process, please use the following one-time verification code:
                </p>

                <!-- OTP Code Box -->
                <div class="otp-box">
                    <div class="otp-label">Your Verification Code</div>
                    <p class="otp-code">{{ $otp }}</p>
                    <div class="expiry">⏰ Expires in 10 minutes</div>
                </div>

                <!-- Instructions -->
                <div class="instructions">
                    <h4>📝 How to verify your account:</h4>
                    <ul>
                        <li>Copy the verification code shown above</li>
                        <li>Return to the login page in your browser</li>
                        <li>Enter the code to complete your verification</li>
                        <li>Once verified, you'll have full access to your account</li>
                    </ul>
                </div>

                <!-- Security Notice -->
                <div class="security-notice">
                    <strong>🔒 Security Notice</strong>
                    Please keep this code private. Our team will never ask you to share your verification code or password via email or phone. If you didn't request this code, you can safely ignore this message.
                </div>

                <p class="message">
                    If you have any questions or need assistance, please don't hesitate to reach out to our support team.
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>This email was sent to <strong>{{ $user->email }}</strong></p>
                <p>© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
                <p>
                    Need help? <a href="mailto:support@{{ config('app.name') }}">Contact Support</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

<x-slot:header>
    <style>
        .otp-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 12px;
        }

        .otp-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px 12px 0 0;
            text-align: center;
            margin-bottom: 20px;
        }

        .otp-header h1 {
            margin: 0 0 20px 0;
            font-size: 28px;
            font-weight: bold;
        }

        .otp-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .otp-code {
            background: #ffffff;
            color: #667eea;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .instructions {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            color: #4b5563;
        }

        .instructions h3 {
            margin: 0 0 15px 0;
            color: #1f2937;
            font-size: 18px;
        }

        .instructions ul {
            margin: 0;
            padding-left: 20px;
        }

        .instructions li {
            margin-bottom: 12px;
            line-height: 1.6;
            color: #6b7280;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #9ca3af;
            font-size: 12px;
        }

        .footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .security-notice {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
            font-size: 13px;
            color: #92400e;
        }

        .footer-info {
            text-align: center;
            margin-top: 30px;
            color: #d1d5db;
            font-size: 11px;
        }
    </style>

    <div class="otp-container">
        <!-- Header Section -->
        <div class="otp-header">
            <h1>🔐 Verification Code</h1>
            <p>Hello, {{ $user->name }}!</p>
        </div>

        <!-- OTP Code Display -->
        <div class="otp-code">
            {{ $otp }}
        </div>

        <!-- Instructions -->
        <div class="instructions">
            <h3>📋 Next Steps:</h3>
            <ul>
                <li>Copy the verification code above</li>
                <li>Go back to the {{ $appName }} login page</li>
                <li>Enter the code to complete your login</li>
                <li>This code will expire in 10 minutes</li>
            </ul>
        </div>

        <!-- Security Notice -->
        <div class="security-notice">
            <strong>🔒 Security Notice:</strong>
            <ul style="margin-top: 10px; padding-left: 20px;">
                <li>Never share your verification code with anyone</li>
                <li>Our team will never ask for your password or OTP code</li>
                <li>If you didn't request this code, please ignore this email</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Need help? Contact us at <a href="mailto:admin@{{ config('app.name') }}">admin@{{ config('app.name') }}</a></p>
        </div>

        <!-- Footer Info -->
        <div class="footer-info">
            <p>This email was sent to {{ $user->email }} from {{ config('app.name') }}</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</x-slot:header>

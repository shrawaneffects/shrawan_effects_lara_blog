<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
            color: #334155;
        }
        .email-container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h2 {
            margin: 0 0 6px 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 0;
            opacity: 0.92;
            font-size: 14px;
        }
        .content {
            padding: 32px 28px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .text {
            font-size: 15px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }
        .otp-box {
            background: #f1f5f9;
            border: 2px dashed #6366f1;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            margin: 28px 0;
        }
        .otp-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6366f1;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }
        .otp-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #1e1b4b;
            user-select: all;
        }
        .security-notice {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 13px;
            color: #991b1b;
            line-height: 1.5;
            margin-top: 24px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2>{{ \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects')) }}</h2>
            <p>Welcome to our community!</p>
        </div>

        <div class="content">
            <div class="greeting">Hello {{ $name }},</div>
            <div class="text">
                Thank you for starting your registration. To verify your email address and activate your account, please enter the following verification code on the registration page:
            </div>

            <div class="otp-box">
                <div class="otp-label">Registration Verification Code</div>
                <div class="otp-code">{{ $code }}</div>
            </div>

            <div class="text" style="font-size: 14px; text-align: center; color: #64748b;">
                This OTP code is valid for <strong>{{ $expiryMinutes }} minutes</strong>.
            </div>

            <div class="security-notice">
                <strong>Security Reminder:</strong> Never share this OTP with anyone. Our support team will never ask for your verification code. If you did not attempt to register, please ignore this email.
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects')) }}. Sent automatically from {{ config('mail.from.address', 'shrawaneffects@gmail.com') }}
        </div>
    </div>
</body>
</html>

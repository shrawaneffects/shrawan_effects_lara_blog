<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Magic Sign-in Link</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 24px;
            color: #334155;
        }
        .email-container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #15803d 0%, #0369a1 100%);
            padding: 34px 24px;
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
            padding: 34px 28px;
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
        .btn-magic {
            display: inline-block;
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 34px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35);
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px;
            margin: 24px 0;
            font-size: 13px;
            color: #64748b;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2>Shrawan Effects</h2>
            <p>Passwordless Authentication Portal</p>
        </div>

        <div class="content">
            <div class="greeting">Hello {{ $user->name }},</div>
            <p class="text">
                We received a request to sign in to your <strong>Shrawan Effects</strong> account without typing a password. Click the button below to sign in instantly:
            </p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $loginUrl }}" class="btn-magic" target="_blank">
                    ✨ Sign In to Shrawan Effects
                </a>
            </div>

            <p class="text" style="font-size: 13px; color: #64748b; margin-bottom: 16px;">
                This single-use magic sign-in link will expire in <strong>{{ $expiryMinutes }} minutes</strong>. If the button above does not work, copy and paste this URL into your browser:
            </p>

            <p style="word-break: break-all; font-family: monospace; font-size: 12px; background: #f1f5f9; padding: 12px; border-radius: 8px; color: #0284c7;">
                {{ $loginUrl }}
            </p>

            <div class="meta-box">
                <strong>Request Context:</strong><br>
                IP Address: {{ $ipAddress }}<br>
                Device / Browser: {{ request()->userAgent() }}<br>
                Time: {{ now()->toDayDateTimeString() }}
            </div>

            <p class="text" style="font-size: 13px; color: #94a3b8; margin-bottom: 0;">
                If you did not request this sign-in link, you can safely ignore this email. No changes were made to your account.
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Shrawan Effects. House No. 1049, Jeevan Nagar, Gounchhi, FARIDABAD, Haryana 121004.
        </div>
    </div>
</body>
</html>

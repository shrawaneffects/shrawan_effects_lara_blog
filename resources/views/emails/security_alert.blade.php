<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Alert</title>
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
            background: linear-gradient(135deg, #ef4444 0%, #f43f5e 50%, #fb7185 100%);
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
        .alert-card {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .alert-title {
            color: #991b1b;
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 8px;
        }
        .alert-desc {
            color: #7f1d1d;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }
        .details-box {
            background-color: #f8fafc;
            border-radius: 10px;
            padding: 16px;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 24px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .detail-row:last-child {
            margin-bottom: 0;
        }
        .action-link {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 14px;
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
            <p>Important Account Security Notification</p>
        </div>

        <div class="content">
            <div class="greeting">Hello {{ $user->name }},</div>
            <p style="font-size: 15px; color: #475569; line-height: 1.6;">
                This is an automated security alert regarding your account (<strong>{{ $user->email }}</strong>).
            </p>

            <div class="alert-card">
                <div class="alert-title">{{ $actionTitle }}</div>
                <p class="alert-desc">{{ $actionDescription }}</p>
            </div>

            <div class="details-box">
                <div class="detail-row">
                    <span>Time of Activity:</span>
                    <strong>{{ $timestamp }}</strong>
                </div>
                <div class="detail-row">
                    <span>IP Address:</span>
                    <strong>{{ $ipAddress }}</strong>
                </div>
            </div>

            <p style="font-size: 14px; color: #64748b; line-height: 1.5;">
                If you initiated this change, you can safely ignore this notification.<br>
                <strong>If you did NOT perform this action</strong>, someone may have compromised your account. Please reset your password immediately:
            </p>

            <div style="text-align: center; margin: 25px 0;">
                <a href="{{ route('password.request') }}" class="action-link">
                    Secure Account / Reset Password
                </a>
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects')) }}. Sent automatically from {{ config('mail.from.address', 'shrawaneffects@gmail.com') }}
        </div>
    </div>
</body>
</html>

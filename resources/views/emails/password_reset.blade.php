<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
            color: #334155;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h2 {
            margin: 0 0 6px 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 15px;
        }
        .text {
            font-size: 15px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 25px;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
        }
        .security-notice {
            background-color: #f1f5f9;
            border-radius: 12px;
            padding: 16px;
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
            margin-top: 25px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h2>{{ \App\Models\Setting::get('site_name', config('app.name', 'LaravelBlog')) }}</h2>
            <p>Password Reset Request</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">Hello {{ $user->name }},</div>
            <div class="text">
                You are receiving this email because we received a password reset request for your account. Click the button below to set a new password:
            </div>

            <div class="btn-container">
                <a href="{{ $resetUrl }}" class="btn">
                    Reset Password
                </a>
            </div>

            <div class="security-notice">
                <strong>Important:</strong> This password reset link will expire in <strong>60 minutes</strong>.<br>
                If you did not request a password reset, no further action is required and your account remains secure.
            </div>

            <div class="text" style="margin-top: 20px; font-size: 13px; color: #94a3b8; word-break: break-all;">
                If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br>
                <a href="{{ $resetUrl }}" style="color: #6366f1;">{{ $resetUrl }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_name', config('app.name', 'LaravelBlog')) }}. All rights reserved.
        </div>
    </div>
</body>
</html>


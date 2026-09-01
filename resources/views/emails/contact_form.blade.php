<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
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
        .badge {
            display: inline-block;
            background-color: #e0e7ff;
            color: #4338ca;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .field-group {
            margin-bottom: 18px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 14px;
        }
        .field-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .field-value {
            font-size: 15px;
            font-weight: 500;
            color: #0f172a;
        }
        .message-box {
            background-color: #f8fafc;
            border-left: 4px solid #6366f1;
            padding: 16px 20px;
            border-radius: 0 12px 12px 0;
            font-size: 15px;
            line-height: 1.6;
            color: #1e293b;
            white-space: pre-wrap;
            margin-top: 10px;
        }
        .actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 14px;
            margin: 5px;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
        .btn-outline {
            display: inline-block;
            background-color: #ffffff;
            color: #6366f1 !important;
            border: 1px solid #cbd5e1;
            text-decoration: none;
            padding: 11px 24px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 14px;
            margin: 5px;
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
            <h2>{{ \App\Models\Setting::get('site_name', 'LaravelBlog') }}</h2>
            <p>New Contact Form Submission Notification</p>
        </div>

        <!-- Content -->
        <div class="content">
            <span class="badge">Inquiry Received</span>

            <div class="field-group">
                <div class="field-label">Sender Name</div>
                <div class="field-value">{{ $contact->name }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">Email Address</div>
                <div class="field-value">
                    <a href="mailto:{{ $contact->email }}" style="color: #6366f1; text-decoration: none;">
                        {{ $contact->email }}
                    </a>
                </div>
            </div>

            @if($contact->subject)
                <div class="field-group">
                    <div class="field-label">Subject</div>
                    <div class="field-value">{{ $contact->subject }}</div>
                </div>
            @endif

            <div class="field-group">
                <div class="field-label">Submitted On</div>
                <div class="field-value">{{ $contact->created_at ? $contact->created_at->format('d M Y, h:i A') : now()->format('d M Y, h:i A') }}</div>
            </div>

            <div style="margin-top: 20px;">
                <div class="field-label">Message</div>
                <div class="message-box">{{ $contact->message }}</div>
            </div>

            <!-- Action buttons -->
            <div class="actions">
                <a href="mailto:{{ $contact->email }}?subject=Re: {{ rawurlencode($contact->subject ?: 'Your inquiry') }}" class="btn">
                    Reply to {{ $contact->name }}
                </a>
                <a href="{{ route('admin.contacts.index') }}" class="btn-outline">
                    View in Admin Panel
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            This email was sent automatically from the contact form on <a href="{{ url('/') }}" style="color: #6366f1;">{{ \App\Models\Setting::get('site_name', 'LaravelBlog') }}</a>.
        </div>
    </div>
</body>
</html>


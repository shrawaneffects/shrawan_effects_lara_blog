<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\AuditLog;
use App\Models\Contact;
use App\Models\Setting;
use App\Services\Security\CaptchaService;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('frontend.contact');
    }

    public function store(Request $request)
    {
        // Honeypot & speed-trap spam check
        if (SecurityService::isBotSubmission($request)) {
            AuditLog::record(
                'contact_bot_blocked',
                'Blocked bot contact form submission via honeypot/time-trap',
                null,
                ['ip' => $request->ip()],
                'warning',
                $request
            );
            return redirect()->back()->with('success', 'Thank you for reaching out! We will get back to you shortly.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|min:10|max:5000',
            'captcha_answer' => 'required',
        ], [
            'captcha_answer.required' => 'Please solve the security verification challenge before submitting.',
        ]);

        // Enforce Visual SVG CAPTCHA verification
        if (!CaptchaService::verify(
            $request->input('captcha_answer'),
            $request->input('captcha_token'),
            $request->input('captcha_timestamp'),
            $request->input('captcha_nonce')
        )) {
            return redirect()->back()
                ->withErrors(['captcha_answer' => 'Security CAPTCHA verification failed. Please solve the new challenge and try again.'])
                ->withInput($request->except('captcha_answer'));
        }

        $validated['name'] = strip_tags($validated['name']);
        $validated['subject'] = isset($validated['subject']) ? strip_tags($validated['subject']) : null;
        $cleanMessage = SecurityService::sanitizeHtml($validated['message']);

        $extra = [];
        if ($request->filled('phone')) {
            $extra[] = "Phone/WhatsApp: " . strip_tags($request->input('phone'));
        }
        if ($request->filled('service')) {
            $extra[] = "Service: " . strip_tags($request->input('service'));
        }
        if (!empty($extra)) {
            $cleanMessage = implode(" | ", $extra) . "\n\n" . $cleanMessage;
        }
        $validated['message'] = $cleanMessage;

        $contact = Contact::create($validated);

        // Send email notification to admin
        $recipient = Setting::get('contact_email', 'shrawaneffects@gmail.com') ?: 'shrawaneffects@gmail.com';

        try {
            Mail::to($recipient)->send(new ContactFormMail($contact));
        } catch (\Throwable $e) {
            Log::error("Failed to send contact notification email to {$recipient}: " . $e->getMessage());
        }

        return redirect()->route('contact.index')->with('success', 'Thank you for reaching out! We will get back to you shortly.');
    }
}

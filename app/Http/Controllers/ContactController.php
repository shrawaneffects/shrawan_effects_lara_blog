<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\Contact;
use App\Models\Setting;
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
        // Honeypot check
        if (SecurityService::isBotSubmission($request)) {
            return redirect()->back()->with('success', 'Thank you for reaching out! We will get back to you shortly.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|min:10|max:5000',
        ]);

        $validated['name'] = strip_tags($validated['name']);
        $validated['subject'] = isset($validated['subject']) ? strip_tags($validated['subject']) : null;
        $validated['message'] = SecurityService::sanitizeHtml($validated['message']);

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

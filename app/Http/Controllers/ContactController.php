<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\SecurityService;
use Illuminate\Http\Request;

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

        Contact::create($validated);

        return redirect()->back()->with('success', 'Thank you for reaching out! We will get back to you shortly.');
    }
}

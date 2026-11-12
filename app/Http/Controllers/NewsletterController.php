<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\NewsletterSubscriber;
use App\Services\Security\BotDetectionService;
use App\Services\SecurityService;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        if (SecurityService::isBotSubmission($request) || BotDetectionService::isBot($request)) {
            AuditLog::record(
                'newsletter_bot_blocked',
                'Blocked automated newsletter subscription spam',
                null,
                ['ip' => $request->ip(), 'email' => $request->input('email')],
                'warning',
                $request
            );
            return redirect()->back()->with('success', 'Thank you for subscribing to our newsletter!');
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => strtolower($validated['email'])],
            ['is_active' => true]
        );

        if (!$subscriber->wasRecentlyCreated && !$subscriber->is_active) {
            $subscriber->update(['is_active' => true]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing to our newsletter!'
            ]);
        }

        return redirect()->back()->with('success', 'Thank you for subscribing to our newsletter!');
    }
}

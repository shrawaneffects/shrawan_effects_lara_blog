<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        // Security CAPTCHA verification
        if (!app()->environment('testing')) {
            if (!\App\Services\Security\CaptchaService::verify(
                $request->input('captcha_answer'),
                $request->input('captcha_token'),
                $request->input('captcha_timestamp')
            )) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Security verification failed. Please enter the correct captcha answer.'
                    ], 422);
                }
                return redirect()->back()->withInput()->withErrors(['captcha_answer' => 'Security verification failed. Please solve the math problem correctly.']);
            }
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

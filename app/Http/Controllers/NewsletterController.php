<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
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

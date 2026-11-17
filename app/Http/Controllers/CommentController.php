<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Services\SecurityService;
use App\Services\Security\CaptchaService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        // Honeypot spam protection
        if (SecurityService::isBotSubmission($request)) {
            return redirect()->back()->with('success', 'Your comment has been posted successfully!');
        }

        $post = Post::published()->findOrFail($postId);

        $rules = [
            'content' => 'required|string|min:3|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
            'captcha_answer' => 'required',
        ];

        if (!auth()->check()) {
            $rules['guest_name'] = 'required|string|max:100';
            $rules['guest_email'] = 'required|email|max:150';
        }

        $validated = $request->validate($rules, [
            'captcha_answer.required' => 'Please solve the security captcha question before submitting your comment.',
        ]);

        if (!CaptchaService::verify(
            $request->input('captcha_answer'),
            $request->input('captcha_token'),
            $request->input('captcha_timestamp')
        )) {
            return redirect()->back()->withErrors([
                'captcha_answer' => 'Security Captcha verification failed. Please try again.',
            ])->withInput();
        }

        $comment = new Comment();
        $comment->post_id = $post->id;
        $comment->content = SecurityService::sanitizeHtml($validated['content']);
        $comment->parent_id = $validated['parent_id'] ?? null;
        $comment->ip_address = $request->ip();
        $comment->status = 'approved';

        if (auth()->check()) {
            $comment->user_id = auth()->id();
        } else {
            $comment->guest_name = strip_tags($validated['guest_name']);
            $comment->guest_email = strip_tags($validated['guest_email']);
        }

        $comment->save();

        return redirect()->back()->with('success', 'Your comment has been posted successfully!');
    }
}

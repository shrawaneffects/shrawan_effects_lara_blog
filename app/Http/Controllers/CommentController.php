<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Comment;
use App\Models\Post;
use App\Services\Security\CaptchaService;
use App\Services\SecurityService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        // Honeypot & speed-trap spam protection
        if (SecurityService::isBotSubmission($request)) {
            AuditLog::record(
                'comment_bot_blocked',
                "Blocked automated comment spam attempt on post #{$postId}",
                auth()->id(),
                ['ip' => $request->ip(), 'post_id' => $postId],
                'warning',
                $request
            );
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
            'captcha_answer.required' => 'Please solve the security verification challenge to post your comment.',
        ]);

        // Enforce Visual SVG CAPTCHA verification
        if (!CaptchaService::verify(
            $request->input('captcha_answer'),
            $request->input('captcha_token'),
            $request->input('captcha_timestamp'),
            $request->input('captcha_nonce')
        )) {
            return redirect(url()->previous() . '#commentsSection')
                ->withErrors(['captcha_answer' => 'Security CAPTCHA verification failed. Please solve the new challenge to submit your comment.'])
                ->withInput($request->except('captcha_answer'));
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

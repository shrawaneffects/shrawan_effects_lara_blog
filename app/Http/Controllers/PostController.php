<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Services\Security\BotDetectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function show($slug, Request $request)
    {
        $postQuery = Post::with(['author', 'category', 'tags']);

        // Check if draft preview is allowed for author/admin
        if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isAuthor())) {
            $post = $postQuery->where('slug', $slug)->firstOrFail();
        } else {
            $post = $postQuery->published()->where('slug', $slug)->firstOrFail();
        }

        // Only issue signed human view challenge to non-bot browser requests
        $viewTokenData = !BotDetectionService::isBot($request)
            ? BotDetectionService::generateViewToken($post->id, $request)
            : null;

        // Approved comments with user and nested replies
        $comments = $post->comments()
            ->where('status', 'approved')
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        // Related posts (from same category or matching tags)
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->where(function ($q) use ($post) {
                if ($post->category_id) {
                    $q->where('category_id', $post->category_id);
                }
            })
            ->take(3)
            ->get();

        if ($relatedPosts->count() < 3) {
            $morePosts = Post::published()
                ->where('id', '!=', $post->id)
                ->whereNotIn('id', $relatedPosts->pluck('id'))
                ->take(3 - $relatedPosts->count())
                ->get();
            $relatedPosts = $relatedPosts->merge($morePosts);
        }

        // Previous and Next Post
        $previousPost = Post::published()
            ->where('id', '<', $post->id)
            ->orderBy('id', 'desc')
            ->first();

        $nextPost = Post::published()
            ->where('id', '>', $post->id)
            ->orderBy('id', 'asc')
            ->first();

        // Popular posts for sidebar
        $popularPosts = Post::published()->orderByDesc('views_count')->take(4)->get();
        $categories = Category::where('is_active', true)->withCount('publishedPosts')->get();

        return view('frontend.blog.show', compact(
            'post',
            'comments',
            'relatedPosts',
            'previousPost',
            'nextPost',
            'popularPosts',
            'categories',
            'viewTokenData'
        ));
    }

    /**
     * Record a verified human article view via signed JS beacon (blocks crawlers/scrapers/robots).
     */
    public function trackView(Request $request, int $postId): JsonResponse
    {
        $post = Post::published()->find($postId);
        if (!$post) {
            return response()->json(['tracked' => false], 404);
        }

        $tracked = BotDetectionService::recordHumanView(
            $post,
            $request,
            $request->input('token'),
            $request->input('timestamp'),
            $request->input('nonce')
        );

        return response()->json([
            'tracked' => $tracked,
            'views_count' => $post->fresh()->views_count ?? $post->views_count,
        ]);
    }
}

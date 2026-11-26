<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Setting;
use App\Models\User;
use App\Models\Contact;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicApiController extends Controller
{
    /**
     * Get global settings for the frontend.
     */
    public function settings()
    {
        $settings = [
            'site_name' => Setting::get('site_name', 'Shrawan Effects'),
            'site_tagline' => Setting::get('site_tagline', 'Fullstack Web Development, AI Automation & Tech Insights'),
            'site_description' => Setting::get('homepage_meta_description', 'Explore cutting-edge fullstack web development, Laravel & React engineering, AI automation solutions, and expert technology insights at Shrawan Effects.'),
            'site_keywords' => Setting::get('homepage_meta_keywords', 'fullstack web development, laravel 12, nextjs, react, php, ai automation, openseo suite, tech tutorials, shrawan effects'),
            'site_logo' => Setting::getLogoUrl() ?: 'https://shrawaneffects.com/storage/settings/FmPxHvRuuMFNwblYELBqVPuJIK61rsCHrWlSSSTE.png',
            'site_favicon' => Setting::getFaviconUrl() ?: 'https://shrawaneffects.com/storage/settings/nS0WZEtEZ8c1ivBACBfE2uHexzYBllzdrzIQS8MV.png',
            'contact_email' => Setting::get('contact_email', 'info@shrawaneffects.com'),
            'contact_phone' => Setting::get('contact_phone', '+91 99999 99999'),
            'contact_address' => Setting::get('contact_address', 'House No. 1049, Jeevan Nagar, Gounchhi, FARIDABAD, Haryana 121004'),
            'footer_text' => Setting::get('footer_text', 'Shrawan Effects is an agency where you can ask anything and that will make your business grow and profitable.'),
            'facebook_url' => Setting::get('facebook_url', ''),
            'twitter_url' => Setting::get('twitter_url', 'https://twitter.com/shrawaneffects'),
            'instagram_url' => Setting::get('instagram_url', ''),
            'linkedin_url' => Setting::get('linkedin_url', 'https://www.linkedin.com/company/shrawan-effects/'),
            'youtube_url' => Setting::get('youtube_url', ''),
            'github_url' => Setting::get('github_url', 'https://github.com/shrawaneffects'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }

    /**
     * Data bundle for Homepage
     */
    public function home()
    {
        $featuredPosts = Post::with(['author', 'category'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        if ($featuredPosts->isEmpty()) {
            $featuredPosts = Post::with(['author', 'category'])
                ->where('status', 'published')
                ->orderByDesc('views_count')
                ->take(3)
                ->get();
        }

        $trendingPosts = Post::with(['author', 'category'])
            ->where('status', 'published')
            ->where('is_trending', true)
            ->orderByDesc('views_count')
            ->take(4)
            ->get();

        if ($trendingPosts->count() < 4) {
            $trendingPosts = Post::with(['author', 'category'])
                ->where('status', 'published')
                ->orderByDesc('views_count')
                ->take(4)
                ->get();
        }

        $categories = Category::withCount(['posts' => function ($q) {
                $q->where('status', 'published');
            }])
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->take(8)
            ->get();

        $latestPosts = Post::with(['author', 'category', 'tags'])
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->take(6)
            ->get();

        $popularTags = Tag::withCount(['posts' => function ($q) {
                $q->where('status', 'published');
            }])
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->take(15)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'featured_lead' => $featuredPosts->first(),
                'featured_secondary' => $featuredPosts->skip(1)->values(),
                'trending_posts' => $trendingPosts,
                'categories' => $categories,
                'latest_posts' => $latestPosts,
                'popular_tags' => $popularTags,
            ]
        ]);
    }

    /**
     * Search and filter posts archive
     */
    public function posts(Request $request)
    {
        $query = Post::with(['author', 'category', 'tags'])
            ->where('status', 'published');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($request->filled('tag')) {
            $tagSlug = $request->input('tag');
            $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        if ($request->filled('author')) {
            $authorId = $request->input('author');
            $query->where('user_id', $authorId);
        }

        $sort = $request->input('sort', 'latest');
        if ($sort === 'popular') {
            $query->orderByDesc('views_count');
        } elseif ($sort === 'oldest') {
            $query->orderBy('published_at', 'asc');
        } else {
            $query->orderByDesc('published_at');
        }

        $perPage = (int) $request->input('per_page', 9);
        $posts = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $posts
        ]);
    }

    /**
     * Single post detail
     */
    public function post($slug)
    {
        $post = Post::with(['author', 'category', 'tags', 'approvedComments'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Increment view count atomically
        $post->increment('views_count');

        // Related posts
        $relatedPosts = Post::with(['category', 'author'])
            ->where('status', 'published')
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        if ($relatedPosts->isEmpty()) {
            $relatedPosts = Post::with(['category', 'author'])
                ->where('status', 'published')
                ->where('id', '!=', $post->id)
                ->orderByDesc('published_at')
                ->take(3)
                ->get();
        }

        // Prev & Next
        $prevPost = Post::where('status', 'published')
            ->where('id', '<', $post->id)
            ->orderBy('id', 'desc')
            ->select('id', 'title', 'slug')
            ->first();

        $nextPost = Post::where('status', 'published')
            ->where('id', '>', $post->id)
            ->orderBy('id', 'asc')
            ->select('id', 'title', 'slug')
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'post' => $post,
                'related_posts' => $relatedPosts,
                'prev_post' => $prevPost,
                'next_post' => $nextPost,
            ]
        ]);
    }

    /**
     * Categories list with post count
     */
    public function categories()
    {
        $categories = Category::withCount(['posts' => function ($q) {
                $q->where('status', 'published');
            }])
            ->orderByDesc('posts_count')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Tags list with post count
     */
    public function tags()
    {
        $tags = Tag::withCount(['posts' => function ($q) {
                $q->where('status', 'published');
            }])
            ->orderByDesc('posts_count')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $tags
        ]);
    }

    /**
     * Author Profile
     */
    public function author($id)
    {
        $user = User::select('id', 'name', 'avatar', 'bio', 'role', 'created_at')
            ->findOrFail($id);

        $posts = Post::with(['category', 'tags'])
            ->where('user_id', $id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(9);

        return response()->json([
            'status' => 'success',
            'data' => [
                'author' => $user,
                'posts' => $posts,
            ]
        ]);
    }

    /**
     * Submit Contact Form
     */
    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:3000',
        ]);

        Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'] ?? 'New Contact Inquiry',
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Your transmission has been logged. Our neural protocol team will contact you shortly.'
        ]);
    }

    /**
     * Submit Newsletter
     */
    public function newsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:150',
        ]);

        DB::table('newsletter_subscribers')->updateOrInsert(
            ['email' => $validated['email']],
            ['created_at' => now(), 'updated_at' => now()]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Telemetry subscription initiated. Weekly cipher updates activated.'
        ]);
    }

    /**
     * Submit Comment
     */
    public function comment(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'email' => 'required|email|max:120',
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
            'status' => 'approved', // Auto-approved or moderation per settings
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment dispatched successfully to the discussion datastream.',
            'data' => $comment
        ]);
    }
}

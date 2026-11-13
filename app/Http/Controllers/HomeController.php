<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $homepageType = Setting::get('homepage_type', 'default');

        // 1. All Blog Posts Feed / Archive
        if ($homepageType === 'blogs') {
            return app(BlogController::class)->index($request);
        }

        // 2. Specific Category Archive Page
        if ($homepageType === 'category') {
            $catId = Setting::get('homepage_category_id');
            if ($catId) {
                $category = Category::where('id', $catId)->where('is_active', true)->first();
                if ($category) {
                    $posts = Post::with(['author', 'category', 'tags'])
                        ->published()
                        ->where('category_id', $category->id)
                        ->latest('published_at')
                        ->paginate(9);

                    $categories = Category::where('is_active', true)->withCount('publishedPosts')->get();
                    $popularPosts = Post::published()->orderByDesc('views_count')->take(4)->get();

                    return view('frontend.blog.category', compact('category', 'posts', 'categories', 'popularPosts'));
                }
            }
        }

        // 3. Specific Single Blog Article
        if ($homepageType === 'post') {
            $postId = Setting::get('homepage_post_id');
            if ($postId) {
                $post = Post::with(['author', 'category', 'tags'])->published()->find($postId);
                if ($post) {
                    // Increment views with session throttle
                    $sessionKey = 'viewed_post_' . $post->id;
                    if (!$request->session()->has($sessionKey)) {
                        $post->increment('views_count');
                        $request->session()->put($sessionKey, true);
                    }

                    $comments = $post->comments()
                        ->where('status', 'approved')
                        ->whereNull('parent_id')
                        ->with(['user', 'replies.user'])
                        ->latest()
                        ->get();

                    $relatedPosts = Post::published()
                        ->where('id', '!=', $post->id)
                        ->where(function ($q) use ($post) {
                            if ($post->category_id) {
                                $q->where('category_id', $post->category_id);
                            }
                        })
                        ->take(3)
                        ->get();

                    $previousPost = Post::published()
                        ->where('id', '<', $post->id)
                        ->orderBy('id', 'desc')
                        ->first();

                    $nextPost = Post::published()
                        ->where('id', '>', $post->id)
                        ->orderBy('id', 'asc')
                        ->first();

                    $popularPosts = Post::published()->orderByDesc('views_count')->take(4)->get();

                    return view('frontend.blog.show', compact(
                        'post',
                        'comments',
                        'relatedPosts',
                        'previousPost',
                        'nextPost',
                        'popularPosts'
                    ));
                }
            }
        }

        // 4. Static Custom Page (e.g. About, Landing, Contact)
        if ($homepageType === 'page') {
            $pageId = Setting::get('homepage_page_id');
            if ($pageId) {
                $page = Page::published()->find($pageId);
                if ($page) {
                    return view('frontend.pages.show', compact('page'));
                }
            }
        }

        // 5. Default Dynamic Homepage (Hero Banner, Featured, Trending, Recent, Categories)
        $featuredPosts = Post::with(['author', 'category', 'tags'])
            ->published()
            ->featured()
            ->latest('published_at')
            ->take(3)
            ->get();

        $trendingPosts = Post::with(['author', 'category'])
            ->published()
            ->trending()
            ->latest('published_at')
            ->take(4)
            ->get();

        $recentPosts = Post::with(['author', 'category', 'tags'])
            ->published()
            ->latest('published_at')
            ->take(6)
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount('publishedPosts')
            ->having('published_posts_count', '>', 0)
            ->orderBy('name')
            ->take(8)
            ->get();

        $popularTags = Tag::withCount('publishedPosts')
            ->having('published_posts_count', '>', 0)
            ->orderByDesc('published_posts_count')
            ->take(12)
            ->get();

        return view('frontend.home', compact(
            'featuredPosts',
            'trendingPosts',
            'recentPosts',
            'categories',
            'popularTags'
        ));
    }

    public function faq()
    {
        $categories = Category::where('is_active', true)->get();
        return view('frontend.faq', compact('categories'));
    }
}

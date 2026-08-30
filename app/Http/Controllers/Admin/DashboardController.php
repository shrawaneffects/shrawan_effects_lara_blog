<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use App\Models\NewsletterSubscriber;
use App\Models\Contact;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $seoCount = \App\Models\PostSeo::count();
        $avgSeoScore = $seoCount > 0 ? round(\App\Models\PostSeo::avg('seo_score')) : 0;

        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'draft_posts' => Post::where('status', 'draft')->count(),
            'total_views' => Post::sum('views_count'),
            'total_comments' => Comment::count(),
            'pending_comments' => Comment::where('status', 'pending')->count(),
            'total_users' => User::count(),
            'total_subscribers' => NewsletterSubscriber::count(),
            'unread_contacts' => Contact::where('is_read', false)->count(),
            'total_pages' => \App\Models\Page::count(),
            'published_pages' => \App\Models\Page::where('status', 'published')->count(),
            'avg_seo_score' => $avgSeoScore,
            'audited_posts' => $seoCount,
            'site_logo_set' => !empty(\App\Models\Setting::getLogoUrl()),
            'site_favicon_set' => !empty(\App\Models\Setting::getFaviconUrl()),
            'robots_exists' => file_exists(public_path('robots.txt')),
            'htaccess_exists' => file_exists(public_path('.htaccess')),
        ];

        $recentPosts = Post::with(['author', 'category'])->latest()->take(5)->get();
        $recentComments = Comment::with(['post', 'user'])->latest()->take(5)->get();
        $popularPosts = Post::orderByDesc('views_count')->take(5)->get();
        $categories = Category::withCount('posts')->orderByDesc('posts_count')->take(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentPosts',
            'recentComments',
            'popularPosts',
            'categories'
        ));
    }
}

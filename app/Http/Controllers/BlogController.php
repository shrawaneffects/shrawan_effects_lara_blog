<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['author', 'category', 'tags'])->published();

        // Search
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // Tag filter
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->input('tag'));
            });
        }

        // Author filter
        if ($request->filled('author')) {
            $query->where('user_id', $request->input('author'));
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'popular') {
            $query->orderByDesc('views_count');
        } elseif ($sort === 'oldest') {
            $query->orderBy('published_at');
        } else {
            $query->latest('published_at');
        }

        $posts = $query->paginate(9)->withQueryString();

        $categories = Category::where('is_active', true)
            ->withCount('publishedPosts')
            ->having('published_posts_count', '>', 0)
            ->orderBy('name')
            ->get();

        $tags = Tag::withCount('publishedPosts')
            ->having('published_posts_count', '>', 0)
            ->orderByDesc('published_posts_count')
            ->take(15)
            ->get();

        $popularPosts = Post::published()->orderByDesc('views_count')->take(4)->get();

        return view('frontend.blog.index', compact(
            'posts',
            'categories',
            'tags',
            'popularPosts',
            'sort'
        ));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $posts = Post::with(['author', 'category', 'tags'])
            ->published()
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(9);

        $categories = Category::where('is_active', true)->withCount('publishedPosts')->get();
        $popularPosts = Post::published()->orderByDesc('views_count')->take(4)->get();

        return view('frontend.blog.category', compact('category', 'posts', 'categories', 'popularPosts'));
    }

    public function tag($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = Post::with(['author', 'category', 'tags'])
            ->published()
            ->whereHas('tags', function ($q) use ($tag) {
                $q->where('tags.id', $tag->id);
            })
            ->latest('published_at')
            ->paginate(9);

        $tags = Tag::withCount('publishedPosts')->orderByDesc('published_posts_count')->take(20)->get();
        $popularPosts = Post::published()->orderByDesc('views_count')->take(4)->get();

        return view('frontend.blog.tag', compact('tag', 'posts', 'tags', 'popularPosts'));
    }

    public function author($id)
    {
        $author = User::where('id', $id)->where('is_active', true)->firstOrFail();

        $posts = Post::with(['author', 'category', 'tags'])
            ->published()
            ->where('user_id', $author->id)
            ->latest('published_at')
            ->paginate(9);

        $categories = Category::where('is_active', true)->withCount('publishedPosts')->get();
        $popularPosts = Post::published()->orderByDesc('views_count')->take(4)->get();

        return view('frontend.blog.author', compact('author', 'posts', 'categories', 'popularPosts'));
    }
}

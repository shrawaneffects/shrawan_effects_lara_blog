<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $pages = $query->orderBy('order')->latest()->paginate(10)->withQueryString();

        $totalPages = Page::count();
        $publishedPages = Page::where('status', 'published')->count();
        $draftPages = Page::where('status', 'draft')->count();

        return view('admin.pages.index', compact(
            'pages',
            'totalPages',
            'publishedPages',
            'draftPages'
        ));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_url' => 'nullable|url|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:published,draft',
            'show_in_navbar' => 'nullable|boolean',
            'show_in_footer' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Page::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter++;
        }

        $page = new Page();
        $page->title = $validated['title'];
        $page->slug = $uniqueSlug;
        $page->content = $validated['content'];
        $page->meta_title = $validated['meta_title'] ?? $validated['title'];
        $page->meta_description = $validated['meta_description'] ?? null;
        $page->meta_keywords = $validated['meta_keywords'] ?? null;
        $page->status = $validated['status'];
        $page->show_in_navbar = $request->boolean('show_in_navbar');
        $page->show_in_footer = $request->boolean('show_in_footer');
        $page->order = $validated['order'] ?? 0;

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('pages', 'public');
            $page->featured_image = $path;
        } elseif (!empty($validated['image_url'])) {
            $page->featured_image = $validated['image_url'];
        }

        $page->save();

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully!');
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('pages', 'slug')->ignore($page->id)],
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_url' => 'nullable|url|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:published,draft',
            'show_in_navbar' => 'nullable|boolean',
            'show_in_footer' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Page::where('slug', $uniqueSlug)->where('id', '!=', $page->id)->exists()) {
            $uniqueSlug = $slug . '-' . $counter++;
        }

        $page->title = $validated['title'];
        $page->slug = $uniqueSlug;
        $page->content = $validated['content'];
        $page->meta_title = $validated['meta_title'] ?? $validated['title'];
        $page->meta_description = $validated['meta_description'] ?? null;
        $page->meta_keywords = $validated['meta_keywords'] ?? null;
        $page->status = $validated['status'];
        $page->show_in_navbar = $request->boolean('show_in_navbar');
        $page->show_in_footer = $request->boolean('show_in_footer');
        $page->order = $validated['order'] ?? 0;

        if ($request->hasFile('featured_image')) {
            if ($page->featured_image && !str_starts_with($page->featured_image, 'http') && Storage::disk('public')->exists($page->featured_image)) {
                Storage::disk('public')->delete($page->featured_image);
            }
            $path = $request->file('featured_image')->store('pages', 'public');
            $page->featured_image = $path;
        } elseif (!empty($validated['image_url'])) {
            $page->featured_image = $validated['image_url'];
        }

        $page->save();

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully!');
    }

    public function destroy($id)
    {
        $page = Page::findOrFail($id);

        if ($page->featured_image && !str_starts_with($page->featured_image, 'http') && Storage::disk('public')->exists($page->featured_image)) {
            Storage::disk('public')->delete($page->featured_image);
        }

        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $page = Page::findOrFail($id);
        $page->status = $page->status === 'published' ? 'draft' : 'published';
        $page->save();

        $statusText = ucfirst($page->status);
        return back()->with('success', "Page status updated to {$statusText}!");
    }
}

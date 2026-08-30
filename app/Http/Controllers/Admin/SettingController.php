<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name' => Setting::get('site_name', 'LaravelBlog'),
            'site_tagline' => Setting::get('site_tagline', 'Modern Tech Publications & Guides'),
            'site_logo' => Setting::getLogoUrl(),
            'site_favicon' => Setting::getFaviconUrl(),
            'contact_email' => Setting::get('contact_email', 'contact@laravelblog.example'),
            'footer_text' => Setting::get('footer_text', 'A modern, high-performance blog platform crafted with Laravel 12 and Bootstrap 5.'),
            'twitter_url' => Setting::get('twitter_url', 'https://twitter.com'),
            'github_url' => Setting::get('github_url', 'https://github.com'),
            'linkedin_url' => Setting::get('linkedin_url', 'https://linkedin.com'),
            'timezone' => Setting::get('timezone', config('app.timezone', 'Asia/Kolkata')),
            'disable_right_click' => Setting::get('disable_right_click', '1') === '1',
            'disable_inspect' => Setting::get('disable_inspect', '1') === '1',
            'disable_screenshot' => Setting::get('disable_screenshot', '1') === '1',
            'homepage_type' => Setting::get('homepage_type', 'default'),
            'homepage_category_id' => Setting::get('homepage_category_id'),
            'homepage_post_id' => Setting::get('homepage_post_id'),
            'homepage_page_id' => Setting::get('homepage_page_id'),
            'ads_enabled' => Setting::get('ads_enabled', '1') === '1',
            'ads_placeholders' => Setting::get('ads_placeholders', '1') === '1',
            'adsense_publisher_id' => Setting::get('adsense_publisher_id', ''),
            'ad_slot_header' => Setting::get('ad_slot_header', ''),
            'ad_slot_home_middle' => Setting::get('ad_slot_home_middle', ''),
            'ad_slot_post_top' => Setting::get('ad_slot_post_top', ''),
            'ad_slot_post_middle' => Setting::get('ad_slot_post_middle', ''),
            'ad_slot_post_bottom' => Setting::get('ad_slot_post_bottom', ''),
            'ad_slot_sidebar' => Setting::get('ad_slot_sidebar', ''),
            'ad_slot_feed' => Setting::get('ad_slot_feed', ''),
        ];

        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        $posts = \App\Models\Post::published()->orderBy('title')->get(['id', 'title', 'slug', 'published_at']);
        $pages = \App\Models\Page::published()->orderBy('title')->get(['id', 'title', 'slug']);

        return view('admin.settings.index', compact('settings', 'categories', 'posts', 'pages'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:100',
            'site_tagline' => 'nullable|string|max:200',
            'timezone' => 'nullable|string|max:100',
            'homepage_type' => 'nullable|in:default,blogs,category,post,page',
            'homepage_category_id' => 'nullable|exists:categories,id',
            'homepage_post_id' => 'nullable|exists:posts,id',
            'homepage_page_id' => 'nullable|exists:pages,id',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:3072',
            'site_favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg,svg,webp|max:1024',
            'contact_email' => 'nullable|email|max:150',
            'footer_text' => 'nullable|string|max:500',
            'twitter_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'delete_logo' => 'nullable|boolean',
            'delete_favicon' => 'nullable|boolean',
            'disable_right_click' => 'nullable|boolean',
            'disable_inspect' => 'nullable|boolean',
            'disable_screenshot' => 'nullable|boolean',
            'ads_enabled' => 'nullable|boolean',
            'ads_placeholders' => 'nullable|boolean',
            'adsense_publisher_id' => 'nullable|string|max:100',
            'ad_slot_header' => 'nullable|string',
            'ad_slot_home_middle' => 'nullable|string',
            'ad_slot_post_top' => 'nullable|string',
            'ad_slot_post_middle' => 'nullable|string',
            'ad_slot_post_bottom' => 'nullable|string',
            'ad_slot_sidebar' => 'nullable|string',
            'ad_slot_feed' => 'nullable|string',
        ]);

        Setting::set('site_name', $validated['site_name']);
        Setting::set('site_tagline', $validated['site_tagline'] ?? '');
        Setting::set('timezone', $validated['timezone'] ?? 'Asia/Kolkata');
        Setting::set('homepage_type', $validated['homepage_type'] ?? Setting::get('homepage_type', 'default'));
        Setting::set('homepage_category_id', $validated['homepage_category_id'] ?? null);
        Setting::set('homepage_post_id', $validated['homepage_post_id'] ?? null);
        Setting::set('homepage_page_id', $validated['homepage_page_id'] ?? null);
        Setting::set('contact_email', $validated['contact_email'] ?? '');
        Setting::set('footer_text', $validated['footer_text'] ?? '');
        Setting::set('twitter_url', $validated['twitter_url'] ?? '');
        Setting::set('github_url', $validated['github_url'] ?? '');
        Setting::set('linkedin_url', $validated['linkedin_url'] ?? '');
        Setting::set('disable_right_click', $request->has('disable_right_click') ? '1' : '0');
        Setting::set('disable_inspect', $request->has('disable_inspect') ? '1' : '0');
        Setting::set('disable_screenshot', $request->has('disable_screenshot') ? '1' : '0');
        Setting::set('ads_enabled', $request->has('ads_enabled') ? '1' : '0');
        Setting::set('ads_placeholders', $request->has('ads_placeholders') ? '1' : '0');
        Setting::set('adsense_publisher_id', $validated['adsense_publisher_id'] ?? '');
        Setting::set('ad_slot_header', $request->input('ad_slot_header') ?? '');
        Setting::set('ad_slot_home_middle', $request->input('ad_slot_home_middle') ?? '');
        Setting::set('ad_slot_post_top', $request->input('ad_slot_post_top') ?? '');
        Setting::set('ad_slot_post_middle', $request->input('ad_slot_post_middle') ?? '');
        Setting::set('ad_slot_post_bottom', $request->input('ad_slot_post_bottom') ?? '');
        Setting::set('ad_slot_sidebar', $request->input('ad_slot_sidebar') ?? '');
        Setting::set('ad_slot_feed', $request->input('ad_slot_feed') ?? '');

        // Handle Delete Logo
        if ($request->boolean('delete_logo')) {
            $oldLogo = Setting::get('site_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::set('site_logo', null);
        }

        // Handle Delete Favicon
        if ($request->boolean('delete_favicon')) {
            $oldFavicon = Setting::get('site_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            Setting::set('site_favicon', null);
        }

        // Handle Logo Upload
        if ($request->hasFile('site_logo')) {
            $oldLogo = Setting::get('site_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $logoPath = $request->file('site_logo')->store('settings', 'public');
            Setting::set('site_logo', $logoPath);
        }

        // Handle Favicon Upload
        if ($request->hasFile('site_favicon')) {
            $oldFavicon = Setting::get('site_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            $favPath = $request->file('site_favicon')->store('settings', 'public');
            Setting::set('site_favicon', $favPath);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Website settings, logo, and favicon updated successfully!');
    }
}

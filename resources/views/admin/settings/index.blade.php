@extends('layouts.admin')

@section('title', 'Website Settings & Logo')
@section('page_title', 'Website Settings')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-1">General Settings & Branding</h4>
                    <p class="text-body-secondary small mb-0">Upload custom logo, favicon, site titles, and contact information.</p>
                </div>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Branding & Logo Card -->
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-palette me-2"></i> Logo & Visual Identity</h5>

                    <div class="row g-4">
                        <!-- Logo Upload Box -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Website Header Logo</label>
                            <div class="p-3 border rounded-3 text-center bg-body-tertiary mb-2">
                                @if($settings['site_logo'])
                                    <div class="mb-3 p-3 bg-body rounded border d-inline-block">
                                        <img src="{{ $settings['site_logo'] }}" alt="Site Logo" id="logoPreview" class="img-fluid" style="max-height: 70px; object-fit: contain;">
                                    </div>
                                    <div class="form-check d-flex justify-content-center gap-2 mb-2">
                                        <input class="form-check-input" type="checkbox" name="delete_logo" value="1" id="deleteLogoCheck">
                                        <label class="form-check-label text-danger small fw-semibold" for="deleteLogoCheck">
                                            Remove current logo
                                        </label>
                                    </div>
                                @else
                                    <div class="mb-3 p-3 bg-body rounded border d-inline-block">
                                        <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='50' viewBox='0 0 120 50'><text x='10' y='32' font-family='Arial' font-size='18' fill='%236c757d'>No Logo</text></svg>" id="logoPreview" class="img-fluid" style="max-height: 70px; object-fit: contain;">
                                    </div>
                                    <p class="text-muted small mb-1">No custom logo uploaded yet. Default text title is active.</p>
                                @endif
                            </div>
                            <input type="file" name="site_logo" id="logoInput" class="form-control form-control-sm" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                            <small class="text-muted">Recommended: PNG or SVG with transparent background (Max 2MB)</small>
                        </div>

                        <!-- Favicon Upload Box -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Browser Favicon</label>
                            <div class="p-3 border rounded-3 text-center bg-body-tertiary mb-2">
                                @if($settings['site_favicon'])
                                    <div class="mb-3 p-3 bg-body rounded border d-inline-block">
                                        <img src="{{ $settings['site_favicon'] }}" alt="Favicon" id="faviconPreview" class="img-fluid" style="width: 36px; height: 36px; object-fit: contain;">
                                    </div>
                                    <div class="form-check d-flex justify-content-center gap-2 mb-2">
                                        <input class="form-check-input" type="checkbox" name="delete_favicon" value="1" id="deleteFaviconCheck">
                                        <label class="form-check-label text-danger small fw-semibold" for="deleteFaviconCheck">
                                            Remove current favicon
                                        </label>
                                    </div>
                                @else
                                    <div class="mb-3 p-3 bg-body rounded border d-inline-block">
                                        <i class="bi bi-browser-chrome fs-1 text-muted" id="faviconPlaceholderIcon"></i>
                                        <img src="" alt="Favicon Preview" id="faviconPreview" class="img-fluid d-none" style="width: 36px; height: 36px; object-fit: contain;">
                                    </div>
                                    <p class="text-muted small mb-1">Favicon displayed on browser tab.</p>
                                @endif
                            </div>
                            <input type="file" name="site_favicon" id="faviconInput" class="form-control form-control-sm" accept="image/x-icon,image/png,image/svg+xml,image/jpeg,image/webp">
                            <small class="text-muted">Recommended: 32x32 or 64x64 px (ICO, PNG, SVG, WEBP)</small>
                        </div>
                    </div>
                </div>

                <!-- Homepage Display & Routing Card -->
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-body mb-4 border-start border-4 border-info">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0 text-info">
                            <i class="bi bi-house-door-fill me-2"></i> Homepage Display Configuration
                        </h5>
                        <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1.5 small">
                            Front Page Router
                        </span>
                    </div>
                    <p class="text-body-secondary small mb-4">
                        Choose what content is served when visitors navigate to the homepage root URL (<code>/</code>).
                    </p>

                    @php
                        $currentHomepageType = old('homepage_type', $settings['homepage_type'] ?? 'default');
                        $currentCatId = old('homepage_category_id', $settings['homepage_category_id'] ?? '');
                        $currentPostId = old('homepage_post_id', $settings['homepage_post_id'] ?? '');
                        $currentPageId = old('homepage_page_id', $settings['homepage_page_id'] ?? '');
                    @endphp

                    <div class="d-flex flex-column gap-3 mb-2">
                        <!-- Option 1: Standard Dynamic Homepage -->
                        <div class="p-3 border rounded-3 bg-body-tertiary">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="homepage_type" id="hpTypeDefault" value="default" {{ $currentHomepageType === 'default' ? 'checked' : '' }} onchange="toggleHomepageDropdowns()">
                                <label class="form-check-label fw-bold d-flex flex-wrap align-items-center gap-2" for="hpTypeDefault">
                                    <span>🌟 Standard Dynamic Homepage</span>
                                    <span class="badge bg-primary-subtle text-primary small">Default</span>
                                </label>
                                <p class="text-muted small mb-0 mt-1 ms-1">
                                    Hero spotlight banner, featured stories slider, trending articles, latest grid, category cards & newsletter CTA.
                                </p>
                            </div>
                        </div>

                        <!-- Option 2: All Blog Posts Feed -->
                        <div class="p-3 border rounded-3 bg-body-tertiary">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="homepage_type" id="hpTypeBlogs" value="blogs" {{ $currentHomepageType === 'blogs' ? 'checked' : '' }} onchange="toggleHomepageDropdowns()">
                                <label class="form-check-label fw-bold d-flex flex-wrap align-items-center gap-2" for="hpTypeBlogs">
                                    <span>📰 All Blog Posts Feed (Blog Index)</span>
                                    <span class="badge bg-success-subtle text-success small">Blog Stream</span>
                                </label>
                                <p class="text-muted small mb-0 mt-1 ms-1">
                                    Directly displays the full paginated article directory with live search bar, category filtering, and tag filters.
                                </p>
                            </div>
                        </div>

                        <!-- Option 3: Specific Category Archive -->
                        <div class="p-3 border rounded-3 bg-body-tertiary">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="homepage_type" id="hpTypeCategory" value="category" {{ $currentHomepageType === 'category' ? 'checked' : '' }} onchange="toggleHomepageDropdowns()">
                                <label class="form-check-label fw-bold d-flex flex-wrap align-items-center gap-2" for="hpTypeCategory">
                                    <span>📁 A Specific Category Archive</span>
                                    <span class="badge bg-info-subtle text-info small">Category Focus</span>
                                </label>
                                <p class="text-muted small mb-2 mt-1 ms-1">
                                    Feature a selected category and its filtered stream as the homepage.
                                </p>
                            </div>

                            <div id="categorySelectBox" class="mt-3 ms-4 ps-1 {{ $currentHomepageType === 'category' ? '' : 'd-none' }}">
                                <label class="form-label fw-semibold small">Select Active Category:</label>
                                <select name="homepage_category_id" class="form-select">
                                    <option value="">-- Choose a Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ (string)$currentCatId === (string)$cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }} ({{ $cat->slug }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Option 4: Specific Single Blog Post -->
                        <div class="p-3 border rounded-3 bg-body-tertiary">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="homepage_type" id="hpTypePost" value="post" {{ $currentHomepageType === 'post' ? 'checked' : '' }} onchange="toggleHomepageDropdowns()">
                                <label class="form-check-label fw-bold d-flex flex-wrap align-items-center gap-2" for="hpTypePost">
                                    <span>📄 A Specific Single Blog Article (Lead Post)</span>
                                    <span class="badge bg-warning-subtle text-warning small">Featured Article</span>
                                </label>
                                <p class="text-muted small mb-2 mt-1 ms-1">
                                    Renders a specific published article, complete with table of contents, comments, and related reading on the root URL.
                                </p>
                            </div>

                            <div id="postSelectBox" class="mt-3 ms-4 ps-1 {{ $currentHomepageType === 'post' ? '' : 'd-none' }}">
                                <label class="form-label fw-semibold small">Select Published Article:</label>
                                <select name="homepage_post_id" class="form-select">
                                    <option value="">-- Choose an Article --</option>
                                    @foreach($posts as $p)
                                        <option value="{{ $p->id }}" {{ (string)$currentPostId === (string)$p->id ? 'selected' : '' }}>
                                            {{ $p->title }} ({{ $p->slug }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Option 5: Static Custom Page -->
                        <div class="p-3 border rounded-3 bg-body-tertiary">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="homepage_type" id="hpTypePage" value="page" {{ $currentHomepageType === 'page' ? 'checked' : '' }} onchange="toggleHomepageDropdowns()">
                                <label class="form-check-label fw-bold d-flex flex-wrap align-items-center gap-2" for="hpTypePage">
                                    <span>📑 A Static Custom Page (e.g. Landing Page / About)</span>
                                    <span class="badge bg-secondary-subtle text-secondary small">Static Page</span>
                                </label>
                                <p class="text-muted small mb-2 mt-1 ms-1">
                                    Renders a custom static page designed in the Pages Builder as your main website front page.
                                </p>
                            </div>

                            <div id="pageSelectBox" class="mt-3 ms-4 ps-1 {{ $currentHomepageType === 'page' ? '' : 'd-none' }}">
                                <label class="form-label fw-semibold small">Select Published Static Page:</label>
                                <select name="homepage_page_id" class="form-select">
                                    <option value="">-- Choose a Static Page --</option>
                                    @foreach($pages as $page)
                                        <option value="{{ $page->id }}" {{ (string)$currentPageId === (string)$page->id ? 'selected' : '' }}>
                                            {{ $page->title }} (/{{ $page->slug }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Homepage SEO & Metadata Card -->
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-body mb-4 border-start border-4 border-primary">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0 text-primary">
                            <i class="bi bi-search me-2"></i> Homepage SEO & Search Metadata
                        </h5>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 small">
                            <i class="bi bi-google me-1"></i> Google SERP & Social Sharing
                        </span>
                    </div>
                    <p class="text-body-secondary small mb-4">
                        Optimize how your homepage appears on Google Search results, Bing, social media previews (OpenGraph & Twitter Cards), and browser title bar.
                    </p>

                    <div class="row g-4 mb-4">
                        <!-- Homepage Meta Title -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold small mb-0" for="hpMetaTitleInput">
                                    Homepage Meta Title (SEO Title)
                                </label>
                                <span class="badge bg-secondary-subtle text-secondary small" id="hpTitleCounter">
                                    <span id="hpTitleCount">0</span>/60 chars (Recommended: 50–60)
                                </span>
                            </div>
                            <input type="text" name="homepage_meta_title" id="hpMetaTitleInput" class="form-control" 
                                   value="{{ old('homepage_meta_title', $settings['homepage_meta_title'] ?? '') }}" 
                                   placeholder="e.g. {{ $settings['site_name'] }} - {{ $settings['site_tagline'] ?: 'Modern Tech Publications & Guides' }}">
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-info-circle me-1"></i> Replaces the <kbd>&lt;title&gt;</kbd> and <kbd>og:title</kbd> on the homepage. If left empty, system defaults to <strong>{{ $settings['site_name'] }} - {{ $settings['site_tagline'] }}</strong>.
                            </small>
                        </div>

                        <!-- Homepage Meta Description -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold small mb-0" for="hpMetaDescInput">
                                    Homepage Meta Description
                                </label>
                                <span class="badge bg-secondary-subtle text-secondary small" id="hpDescCounter">
                                    <span id="hpDescCount">0</span>/160 chars (Recommended: 150–160)
                                </span>
                            </div>
                            <textarea name="homepage_meta_description" id="hpMetaDescInput" class="form-control" rows="3" 
                                      placeholder="e.g. Discover in-depth programming tutorials, clean architecture guides, Laravel tips, and high-performance engineering insights.">{{ old('homepage_meta_description', $settings['homepage_meta_description'] ?? '') }}</textarea>
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-info-circle me-1"></i> Appears in search engine result snippets and social shares. If left empty, system uses site tagline/footer description.
                            </small>
                        </div>

                        <!-- Homepage Meta Keywords -->
                        <div class="col-12">
                            <label class="form-label fw-bold small mb-1" for="hpMetaKeywordsInput">
                                Homepage Meta Keywords (Optional)
                            </label>
                            <input type="text" name="homepage_meta_keywords" id="hpMetaKeywordsInput" class="form-control" 
                                   value="{{ old('homepage_meta_keywords', $settings['homepage_meta_keywords'] ?? '') }}" 
                                   placeholder="e.g. laravel, php, web development, bootstrap 5, tech blog, tutorials">
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-tags me-1"></i> Comma-separated list of target topics for meta keywords header tag.
                            </small>
                        </div>
                    </div>

                    <!-- Live Google Search SERP Preview Box -->
                    <div class="p-3.5 p-md-4 rounded-4 bg-body-tertiary border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-bold small text-body d-flex align-items-center gap-1.5">
                                <i class="bi bi-google text-primary"></i> Live Google Search Result (SERP) Preview
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 small">
                                Live Simulation
                            </span>
                        </div>
                        <p class="text-muted small mb-3">This is an approximate preview of how your homepage snippet will render in Google search results.</p>

                        <div class="p-3 bg-body rounded-3 border shadow-xs" style="max-width: 650px;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border" style="width: 26px; height: 26px; font-size: 13px;">
                                    @if($settings['site_favicon'])
                                        <img src="{{ $settings['site_favicon'] }}" alt="Favicon" style="width: 16px; height: 16px; object-fit: contain;">
                                    @else
                                        <i class="bi bi-globe text-primary"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-body fw-semibold text-truncate" id="serpPreviewSiteName" style="font-size: 0.85rem; line-height: 1.1;">{{ $settings['site_name'] }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.72rem; line-height: 1.1;">{{ url('/') }}</div>
                                </div>
                            </div>
                            <h6 class="mb-1 fw-medium" style="color: #1a0dab; font-size: 1.15rem; line-height: 1.3;">
                                <span id="serpPreviewTitle" class="text-decoration-underline-hover cursor-pointer">{{ $settings['homepage_meta_title'] ?: ($settings['site_name'] . ' - ' . ($settings['site_tagline'] ?: 'Modern Tech Publications')) }}</span>
                            </h6>
                            <p class="mb-0 text-secondary" id="serpPreviewDesc" style="font-size: 0.86rem; line-height: 1.45;">
                                {{ $settings['homepage_meta_description'] ?: ($settings['footer_text'] ?: 'A modern, high-performance blog platform crafted with Laravel 12 and Bootstrap 5.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Site Information Card -->
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-info-circle me-2"></i> Website Information</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Site Name *</label>
                            <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings['site_name']) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Contact Email</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $settings['contact_email']) }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Site Tagline</label>
                            <input type="text" name="site_tagline" class="form-control" value="{{ old('site_tagline', $settings['site_tagline']) }}" placeholder="Modern Tech Publications & Guides">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">System Timezone</label>
                            <select name="timezone" class="form-select">
                                @php
                                    $currentTimezone = old('timezone', $settings['timezone'] ?? 'Asia/Kolkata');
                                    $timezones = [
                                        'Asia/Kolkata' => 'Asia/Kolkata (IST +05:30) - India',
                                        'Asia/Dubai' => 'Asia/Dubai (GST +04:00) - UAE',
                                        'Asia/Singapore' => 'Asia/Singapore (SGT +08:00)',
                                        'Asia/Tokyo' => 'Asia/Tokyo (JST +09:00) - Japan',
                                        'Europe/London' => 'Europe/London (GMT/BST)',
                                        'Europe/Paris' => 'Europe/Paris (CET/CEST)',
                                        'America/New_York' => 'America/New_York (EST/EDT)',
                                        'America/Chicago' => 'America/Chicago (CST/CDT)',
                                        'America/Los_Angeles' => 'America/Los_Angeles (PST/PDT)',
                                        'UTC' => 'UTC (Coordinated Universal Time)',
                                    ];
                                @endphp
                                @foreach($timezones as $tzKey => $tzLabel)
                                    <option value="{{ $tzKey }}" {{ $currentTimezone === $tzKey ? 'selected' : '' }}>{{ $tzLabel }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Current Server Time: <strong>{{ now()->format('d M Y, h:i A') }}</strong></small>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Footer Description</label>
                        <textarea name="footer_text" class="form-control" rows="3">{{ old('footer_text', $settings['footer_text']) }}</textarea>
                    </div>
                </div>

                <!-- Social Links Card -->
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-share me-2"></i> Social Profiles</h5>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><i class="bi bi-twitter-x me-1"></i> Twitter / X URL</label>
                            <input type="url" name="twitter_url" class="form-control" value="{{ old('twitter_url', $settings['twitter_url']) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><i class="bi bi-github me-1"></i> GitHub URL</label>
                            <input type="url" name="github_url" class="form-control" value="{{ old('github_url', $settings['github_url']) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small"><i class="bi bi-linkedin me-1"></i> LinkedIn URL</label>
                            <input type="url" name="linkedin_url" class="form-control" value="{{ old('linkedin_url', $settings['linkedin_url']) }}">
                        </div>
                    </div>
                </div>

                <!-- Content Protection & Anti-Inspect Card -->
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-4 text-warning"><i class="bi bi-shield-slash-fill me-2"></i> Content Protection & Security</h5>
                    <p class="text-body-secondary small mb-3">Protect your articles, media, and code from unauthorized copying, right-clicking, screen capture / screenshots, and developer tools.</p>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0">
                                    <label class="form-check-label fw-semibold small" for="disableRightClick">
                                        <i class="bi bi-mouse me-1 text-primary"></i> Disable Right-Click
                                    </label>
                                    <input class="form-check-input ms-0" type="checkbox" name="disable_right_click" value="1" id="disableRightClick" {{ $settings['disable_right_click'] ? 'checked' : '' }}>
                                </div>
                                <small class="text-muted d-block mt-2">Blocks context menu and right-clicking on pages & media.</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0">
                                    <label class="form-check-label fw-semibold small" for="disableInspect">
                                        <i class="bi bi-code-slash me-1 text-primary"></i> Disable Inspect & Source
                                    </label>
                                    <input class="form-check-input ms-0" type="checkbox" name="disable_inspect" value="1" id="disableInspect" {{ $settings['disable_inspect'] ? 'checked' : '' }}>
                                </div>
                                <small class="text-muted d-block mt-2">Blocks F12, Ctrl+U (View Source), Ctrl+Shift+I, and DevTools.</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0">
                                    <label class="form-check-label fw-semibold small" for="disableScreenshot">
                                        <i class="bi bi-camera-fill me-1 text-danger"></i> Anti-Screenshot Shield
                                    </label>
                                    <input class="form-check-input ms-0" type="checkbox" name="disable_screenshot" value="1" id="disableScreenshot" {{ $settings['disable_screenshot'] ? 'checked' : '' }}>
                                </div>
                                <small class="text-muted d-block mt-2">Blocks PrintScreen, Windows Snipping Tool (Win+Shift+S), Mac shortcuts, Ctrl+P, and blurs on capture.</small>
                            </div>
                        </div>
                    </div>
                <!-- Google AdSense & Monetization Card -->
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-body mb-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        <div>
                            <h5 class="fw-bold mb-1 text-success"><i class="bi bi-badge-ad me-2"></i> Google AdSense & Monetization Hub</h5>
                            <p class="text-body-secondary small mb-0">Manage Google AdSense client ID, Auto Ads, and custom ad placements across Header, Articles, Sidebar, and Feeds.</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="ads_enabled" value="1" id="adsEnabled" {{ $settings['ads_enabled'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold small" for="adsEnabled">Ads Enabled Site-wide</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="ads_placeholders" value="1" id="adsPlaceholders" {{ $settings['ads_placeholders'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold small text-muted" for="adsPlaceholders">Show Preview Placeholders</label>
                            </div>
                        </div>
                    </div>

                    <!-- AdSense Auto Ads / Client ID -->
                    <div class="p-3.5 rounded-4 bg-body-tertiary border mb-4">
                        <label class="form-label fw-bold small text-body d-flex align-items-center gap-1">
                            <i class="bi bi-google text-primary"></i> Google AdSense Publisher Client ID
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2" style="font-size: 0.68rem;">Auto Ads</span>
                        </label>
                        <input type="text" name="adsense_publisher_id" class="form-control font-monospace" value="{{ old('adsense_publisher_id', $settings['adsense_publisher_id']) }}" placeholder="ca-pub-1234567890123456">
                        <small class="text-muted d-block mt-1">If provided, the official Google AdSense Auto Ads script will be automatically included in the <kbd>&lt;head&gt;</kbd> of every public page.</small>
                    </div>

                    <h6 class="fw-bold text-body mb-3"><i class="bi bi-grid-1x2 me-1 text-primary"></i> Targeted Ad Unit Placements</h6>
                    
                    <div class="row g-4">
                        <!-- Header Leaderboard Ad -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <label class="form-label fw-bold small d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-layout-top me-1 text-primary"></i> Header Leaderboard Ad</span>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">728x90 / Responsive</span>
                                </label>
                                <textarea name="ad_slot_header" class="form-control font-monospace small" rows="3" placeholder="<!-- Paste AdSense / HTML ad unit code here -->">{{ old('ad_slot_header', $settings['ad_slot_header']) }}</textarea>
                                <small class="text-muted d-block mt-1">Displayed below the top navigation bar across all pages.</small>
                            </div>
                        </div>

                        <!-- Homepage Middle Ad -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <label class="form-label fw-bold small d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-house me-1 text-primary"></i> Homepage Mid-Section Ad</span>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Leaderboard / Responsive</span>
                                </label>
                                <textarea name="ad_slot_home_middle" class="form-control font-monospace small" rows="3" placeholder="<!-- Paste AdSense / HTML ad unit code here -->">{{ old('ad_slot_home_middle', $settings['ad_slot_home_middle']) }}</textarea>
                                <small class="text-muted d-block mt-1">Displayed between Featured Spotlight and Recent Posts on Homepage.</small>
                            </div>
                        </div>

                        <!-- Post Top Ad -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <label class="form-label fw-bold small d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-arrow-up-square me-1 text-primary"></i> Article Top Ad</span>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Above Content</span>
                                </label>
                                <textarea name="ad_slot_post_top" class="form-control font-monospace small" rows="3" placeholder="<!-- Paste AdSense / HTML ad unit code here -->">{{ old('ad_slot_post_top', $settings['ad_slot_post_top']) }}</textarea>
                                <small class="text-muted d-block mt-1">Displayed directly above the article text & reading area.</small>
                            </div>
                        </div>

                        <!-- Post Middle Ad -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <label class="form-label fw-bold small d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-card-text me-1 text-primary"></i> Article In-Content Ad</span>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Mid-Paragraph</span>
                                </label>
                                <textarea name="ad_slot_post_middle" class="form-control font-monospace small" rows="3" placeholder="<!-- Paste AdSense / HTML ad unit code here -->">{{ old('ad_slot_post_middle', $settings['ad_slot_post_middle']) }}</textarea>
                                <small class="text-muted d-block mt-1">Displayed in the middle of long articles between paragraphs.</small>
                            </div>
                        </div>

                        <!-- Post Bottom Ad -->
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <label class="form-label fw-bold small d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-arrow-down-square me-1 text-primary"></i> Article Bottom Ad</span>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">End of Post</span>
                                </label>
                                <textarea name="ad_slot_post_bottom" class="form-control font-monospace small" rows="3" placeholder="<!-- Paste AdSense / HTML code -->">{{ old('ad_slot_post_bottom', $settings['ad_slot_post_bottom']) }}</textarea>
                                <small class="text-muted d-block mt-1">Displayed before comments and author bio.</small>
                            </div>
                        </div>

                        <!-- Sidebar Sticky Ad -->
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <label class="form-label fw-bold small d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-layout-sidebar me-1 text-primary"></i> Sidebar Sticky Ad</span>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">300x250 / 300x600</span>
                                </label>
                                <textarea name="ad_slot_sidebar" class="form-control font-monospace small" rows="3" placeholder="<!-- Paste AdSense / HTML code -->">{{ old('ad_slot_sidebar', $settings['ad_slot_sidebar']) }}</textarea>
                                <small class="text-muted d-block mt-1">Displayed on the sidebar of Article, Category, and Blog Index pages.</small>
                            </div>
                        </div>

                        <!-- Feed In-Between Ad -->
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <label class="form-label fw-bold small d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-collection me-1 text-primary"></i> Feed In-Between Ad</span>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Native In-Feed</span>
                                </label>
                                <textarea name="ad_slot_feed" class="form-control font-monospace small" rows="3" placeholder="<!-- Paste AdSense / HTML code -->">{{ old('ad_slot_feed', $settings['ad_slot_feed']) }}</textarea>
                                <small class="text-muted d-block mt-1">Displayed between article cards in Blog Index & Category streams.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5 fw-semibold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Save All Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleHomepageDropdowns() {
            const selected = document.querySelector('input[name="homepage_type"]:checked')?.value;
            const catBox = document.getElementById('categorySelectBox');
            const postBox = document.getElementById('postSelectBox');
            const pageBox = document.getElementById('pageSelectBox');

            if (catBox) catBox.classList.toggle('d-none', selected !== 'category');
            if (postBox) postBox.classList.toggle('d-none', selected !== 'post');
            if (pageBox) pageBox.classList.toggle('d-none', selected !== 'page');
        }

        document.getElementById('logoInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const preview = document.getElementById('logoPreview');
                    if (preview) {
                        preview.src = evt.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('faviconInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const preview = document.getElementById('faviconPreview');
                    const icon = document.getElementById('faviconPlaceholderIcon');
                    if (icon) icon.classList.add('d-none');
                    if (preview) {
                        preview.src = evt.target.result;
                        preview.classList.remove('d-none');
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Homepage SEO Live Character Counters & Google SERP Preview
        (function() {
            const titleInput = document.getElementById('hpMetaTitleInput');
            const descInput = document.getElementById('hpMetaDescInput');
            const siteNameInput = document.querySelector('input[name="site_name"]');
            const taglineInput = document.querySelector('input[name="site_tagline"]');

            const titleCount = document.getElementById('hpTitleCount');
            const descCount = document.getElementById('hpDescCount');
            const titleCounterBadge = document.getElementById('hpTitleCounter');
            const descCounterBadge = document.getElementById('hpDescCounter');

            const serpTitle = document.getElementById('serpPreviewTitle');
            const serpDesc = document.getElementById('serpPreviewDesc');
            const serpSiteName = document.getElementById('serpPreviewSiteName');

            function updateHpSeoPreview() {
                const siteNameVal = siteNameInput?.value.trim() || 'LaravelBlog';
                const taglineVal = taglineInput?.value.trim() || '';
                const fallbackTitle = taglineVal ? `${siteNameVal} - ${taglineVal}` : siteNameVal;

                const customTitle = titleInput?.value.trim();
                const customDesc = descInput?.value.trim();

                const tLen = customTitle ? customTitle.length : 0;
                const dLen = customDesc ? customDesc.length : 0;

                if (titleCount) titleCount.textContent = tLen;
                if (descCount) descCount.textContent = dLen;

                // Title Counter Badge styling
                if (titleCounterBadge) {
                    if (tLen === 0) {
                        titleCounterBadge.className = 'badge bg-secondary-subtle text-secondary small';
                    } else if (tLen >= 45 && tLen <= 65) {
                        titleCounterBadge.className = 'badge bg-success-subtle text-success small';
                    } else if (tLen > 65) {
                        titleCounterBadge.className = 'badge bg-warning-subtle text-warning small';
                    } else {
                        titleCounterBadge.className = 'badge bg-info-subtle text-info small';
                    }
                }

                // Desc Counter Badge styling
                if (descCounterBadge) {
                    if (dLen === 0) {
                        descCounterBadge.className = 'badge bg-secondary-subtle text-secondary small';
                    } else if (dLen >= 130 && dLen <= 165) {
                        descCounterBadge.className = 'badge bg-success-subtle text-success small';
                    } else if (dLen > 165) {
                        descCounterBadge.className = 'badge bg-warning-subtle text-warning small';
                    } else {
                        descCounterBadge.className = 'badge bg-info-subtle text-info small';
                    }
                }

                // Update SERP preview elements
                if (serpSiteName) serpSiteName.textContent = siteNameVal;
                if (serpTitle) serpTitle.textContent = customTitle || fallbackTitle;
                if (serpDesc) {
                    serpDesc.textContent = customDesc || (taglineVal || 'A modern, high-performance blog platform crafted with Laravel 12 and Bootstrap 5.');
                }
            }

            titleInput?.addEventListener('input', updateHpSeoPreview);
            descInput?.addEventListener('input', updateHpSeoPreview);
            siteNameInput?.addEventListener('input', updateHpSeoPreview);
            taglineInput?.addEventListener('input', updateHpSeoPreview);

            // Initial trigger
            updateHpSeoPreview();
        })();
    </script>
    @endpush
@endsection

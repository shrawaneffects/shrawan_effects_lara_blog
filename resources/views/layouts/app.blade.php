@php
    $siteName = \App\Models\Setting::get('site_name', 'LaravelBlog');
    $siteLogo = \App\Models\Setting::getLogoUrl();
    $siteFavicon = \App\Models\Setting::getFaviconUrl();
    $contactEmail = \App\Models\Setting::get('contact_email', 'contact@laravelblog.example');
    $footerText = \App\Models\Setting::get('footer_text', 'A modern, high-performance blog platform crafted with Laravel 12 and Bootstrap 5.');
    $twitterUrl = \App\Models\Setting::get('twitter_url', 'https://twitter.com');
    $githubUrl = \App\Models\Setting::get('github_url', 'https://github.com');
    $linkedinUrl = \App\Models\Setting::get('linkedin_url', 'https://linkedin.com');
    $disableRightClick = \App\Models\Setting::get('disable_right_click', '1') === '1';
    $disableInspect = \App\Models\Setting::get('disable_inspect', '1') === '1';
    $disableScreenshot = \App\Models\Setting::get('disable_screenshot', '1') === '1';
@endphp
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $siteName)</title>
    @if($siteFavicon)
        <link rel="icon" href="{{ $siteFavicon }}">
        <link rel="shortcut icon" href="{{ $siteFavicon }}">
        <link rel="apple-touch-icon" href="{{ $siteFavicon }}">
    @endif
    <meta name="description" content="@yield('meta_description', 'A modern tech publication built on Laravel 12 & Bootstrap 5')">
    <meta name="keywords" content="@yield('meta_keywords', 'laravel, php, web development, bootstrap 5, tech')">
    <meta name="robots" content="@yield('robots_meta', 'index, follow')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    <!-- OpenGraph Social Metadata -->
    <meta property="og:title" content="@yield('og_title', $siteName)">
    <meta property="og:description" content="@yield('og_description', 'A modern tech publication built on Laravel 12 & Bootstrap 5')">
    <meta property="og:image" content="@yield('og_image', asset('storage/default.jpg'))">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $siteName }}">

    <!-- Twitter / X Card Metadata -->
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('twitter_title', View::getSection('og_title', $siteName))">
    <meta name="twitter:description" content="@yield('twitter_description', View::getSection('og_description', ''))">
    <meta name="twitter:image" content="@yield('twitter_image', View::getSection('og_image', asset('storage/default.jpg')))">

    <!-- Schema.org JSON-LD Structured Data -->
    @stack('schema')

    @php
        $adsEnabled = \App\Models\Setting::get('ads_enabled', '1') === '1';
        $adsensePublisherId = \App\Models\Setting::get('adsense_publisher_id');
    @endphp
    @if($adsEnabled && !empty($adsensePublisherId))
        <!-- Google AdSense Auto Ads Script -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsensePublisherId }}" crossorigin="anonymous"></script>
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --gradient-primary: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            --gradient-secondary: linear-gradient(135deg, #06b6d4 0%, #3b82f6 50%, #6366f1 100%);
            --gradient-sunset: linear-gradient(135deg, #f43f5e 0%, #fb7185 45%, #fb923c 100%);
            --gradient-emerald: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #06b6d4 100%);
            --gradient-mesh-light: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.12) 0px, transparent 50%), radial-gradient(at 100% 0%, rgba(217, 70, 239, 0.12) 0px, transparent 50%), radial-gradient(at 50% 100%, rgba(6, 182, 212, 0.10) 0px, transparent 50%);
            --gradient-mesh-dark: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.22) 0px, transparent 50%), radial-gradient(at 100% 0%, rgba(217, 70, 239, 0.18) 0px, transparent 50%), radial-gradient(at 50% 100%, rgba(6, 182, 212, 0.15) 0px, transparent 50%);
        }

        body {
            font-family: var(--font-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            background-color: var(--bs-body-bg);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        [data-bs-theme="dark"] body {
            background-image: var(--gradient-mesh-dark);
            background-attachment: fixed;
        }

        [data-bs-theme="light"] body {
            background-image: var(--gradient-mesh-light);
            background-attachment: fixed;
        }

        /* Top Rainbow Accent Line */
        .top-gradient-bar {
            height: 4px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6, #ec4899, #f43f5e, #fb923c, #10b981, #06b6d4, #6366f1);
            background-size: 200% 100%;
            animation: gradientMove 6s linear infinite;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }

        /* Text Gradients */
        .text-gradient {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        .text-gradient-fire {
            background: var(--gradient-sunset);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        .text-gradient-cyan {
            background: var(--gradient-secondary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        .text-gradient-emerald {
            background: var(--gradient-emerald);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        /* Navbar & Glassmorphism */
        .navbar-glass {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            background-color: rgba(var(--bs-body-bg-rgb), 0.85) !important;
            border-bottom: 1px solid rgba(99, 102, 241, 0.12);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.5px;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.04);
        }

        .brand-badge {
            background: var(--gradient-primary);
            color: #fff;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-left: 6px;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
            transition: all 0.3s ease;
        }

        .brand-badge:hover {
            transform: translateY(-2px) rotate(-3deg);
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.5);
        }

        /* Nav links hover glow */
        .nav-link {
            position: relative;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--gradient-primary);
            transition: width 0.3s ease, left 0.3s ease;
            border-radius: 2px;
        }

        .nav-link:hover::after, .nav-link.active::after {
            width: 100%;
            left: 0;
        }

        /* Hero Banner with Aurora Mesh */
        .hero-banner {
            position: relative;
            padding: 3.5rem 0;
            overflow: hidden;
            border-bottom: 1px solid var(--bs-border-color-translucent);
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 140%;
            height: 200%;
            background: radial-gradient(circle at 30% 30%, rgba(99, 102, 241, 0.15) 0%, transparent 60%),
                        radial-gradient(circle at 70% 60%, rgba(217, 70, 239, 0.12) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
            animation: pulseAura 12s ease-in-out infinite alternate;
        }

        @keyframes pulseAura {
            0% { transform: scale(1) rotate(0deg); }
            100% { transform: scale(1.1) rotate(4deg); }
        }

        /* Post Cards & 3D Mouse Hover Effects */
        .card-post {
            border: 1px solid var(--bs-border-color-translucent);
            border-radius: 20px;
            overflow: hidden;
            background-color: var(--bs-body-bg);
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), border-color 0.4s ease;
            position: relative;
            z-index: 1;
        }

        .card-post::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 20px;
            padding: 1.5px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.6), rgba(217, 70, 239, 0.6), rgba(6, 182, 212, 0.6));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .card-post:hover {
            transform: translateY(-8px) scale(1.015);
            box-shadow: 0 20px 40px -15px rgba(99, 102, 241, 0.25), 0 0 25px rgba(217, 70, 239, 0.15);
            border-color: transparent;
        }

        .card-post:hover::before {
            opacity: 1;
        }

        .card-post .post-thumb-container {
            overflow: hidden;
            position: relative;
        }

        .card-post .post-thumb {
            height: 220px;
            object-fit: cover;
            width: 100%;
            transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1), filter 0.5s ease;
        }

        .card-post:hover .post-thumb {
            transform: scale(1.08) rotate(0.4deg);
            filter: brightness(1.05);
        }

        .featured-thumb {
            height: 380px;
            object-fit: cover;
            width: 100%;
            border-radius: 16px;
            transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1), filter 0.5s ease;
        }

        .card-post:hover .featured-thumb {
            transform: scale(1.05);
            filter: brightness(1.05);
        }

        .card-post .card-title a {
            transition: color 0.3s ease;
        }

        .card-post:hover .card-title a {
            color: #6366f1 !important;
        }

        /* Gradient Animated Buttons */
        .btn-gradient {
            background: var(--gradient-primary);
            background-size: 200% auto;
            color: #fff !important;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 0.6rem 1.4rem;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-gradient:hover {
            background-position: right center;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5);
            color: #fff !important;
        }

        .btn-gradient:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-gradient-sunset {
            background: var(--gradient-sunset);
            background-size: 200% auto;
            color: #fff !important;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 0.6rem 1.4rem;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(244, 63, 94, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-gradient-sunset:hover {
            background-position: right center;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 25px -5px rgba(244, 63, 94, 0.5);
            color: #fff !important;
        }

        .btn-gradient-cyan {
            background: var(--gradient-secondary);
            background-size: 200% auto;
            color: #fff !important;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            padding: 0.6rem 1.4rem;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-gradient-cyan:hover {
            background-position: right center;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 25px -5px rgba(6, 182, 212, 0.5);
            color: #fff !important;
        }

        /* Hover Lift Utility */
        .hover-lift {
            transition: transform 0.35s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.35s ease, border-color 0.35s ease;
        }

        .hover-lift:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12) !important;
        }

        /* Category Card Hover & Gradient */
        .category-card {
            border: 1px solid var(--bs-border-color-translucent);
            border-radius: 16px;
            background: var(--bs-body-bg);
            transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            overflow: hidden;
        }

        .category-card:hover {
            transform: translateY(-8px) scale(1.04);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.2);
            border-color: rgba(99, 102, 241, 0.4);
        }

        .category-card .category-icon {
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .category-card:hover .category-icon {
            transform: scale(1.2) rotate(8deg);
        }

        /* Trending Cards Hover */
        .trending-card {
            padding: 1rem;
            border-radius: 16px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .trending-card:hover {
            background: var(--bs-tertiary-bg);
            border-color: var(--bs-border-color-translucent);
            transform: translateX(6px);
        }

        .trending-card .trend-number {
            background: var(--gradient-sunset);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: transform 0.3s ease;
            display: inline-block;
        }

        .trending-card:hover .trend-number {
            transform: scale(1.15) rotate(-5deg);
        }

        /* Tag Pills */
        .tag-pill {
            font-size: 0.82rem;
            padding: 0.4rem 0.9rem;
            border-radius: 24px;
            text-decoration: none;
            background-color: var(--bs-tertiary-bg);
            color: var(--bs-body-color);
            border: 1px solid var(--bs-border-color-translucent);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: inline-flex;
            align-items: center;
            margin: 3px;
        }

        .tag-pill:hover {
            background: var(--gradient-primary);
            color: #fff !important;
            border-color: transparent;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.35);
        }

        .tag-pill:hover .badge {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #fff !important;
        }

        /* Avatars with Gradient Ring on Hover */
        .author-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .author-avatar:hover {
            transform: scale(1.15);
            box-shadow: 0 0 0 3px #6366f1, 0 0 15px rgba(99, 102, 241, 0.5);
        }

        .author-avatar-lg {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.3);
            transition: all 0.3s ease;
        }

        .author-avatar-lg:hover {
            transform: scale(1.08) rotate(3deg);
            box-shadow: 0 0 0 5px #8b5cf6, 0 10px 25px rgba(139, 92, 246, 0.4);
        }

        /* Newsletter Gradient Card */
        .card-newsletter-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%);
            position: relative;
            overflow: hidden;
            border-radius: 24px;
        }

        .card-newsletter-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
            animation: pulseAura 8s infinite alternate;
        }

        /* Badges */
        .badge-category {
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.5px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .badge-category:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
            color: #fff !important;
        }

        /* Post Content Typography */
        .post-content {
            font-size: 1.15rem;
            line-height: 1.85;
        }

        .post-content img {
            max-width: 100%;
            height: auto;
            border-radius: 16px;
            margin: 1.75rem 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .post-content img:hover {
            transform: scale(1.01);
        }

        .post-content pre {
            background: #181825;
            color: #cdd6f4;
            padding: 1.4rem;
            border-radius: 14px;
            overflow-x: auto;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .post-content blockquote {
            border-left: 4px solid #8b5cf6;
            padding: 1.2rem 1.8rem;
            background: var(--bs-tertiary-bg);
            border-radius: 0 14px 14px 0;
            font-style: italic;
            position: relative;
        }

        .footer {
            margin-top: auto;
            border-top: 1px solid var(--bs-border-color);
            background-color: var(--bs-tertiary-bg);
        }

        .btn-theme-toggle {
            width: 40px;
            height: 40px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-theme-toggle:hover {
            transform: rotate(30deg) scale(1.1);
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
        }

        .sticky-top-widget {
            position: sticky;
            top: 5.5rem;
        }

        /* Spotlight Mouse Glow for cards */
        .spotlight-card {
            position: relative;
            overflow: hidden;
        }

        .spotlight-card::after {
            content: '';
            position: absolute;
            top: var(--mouse-y, -1000px);
            left: var(--mouse-x, -1000px);
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 2;
        }

        .spotlight-card:hover::after {
            opacity: 1;
        }

        @if($disableScreenshot)
        /* Anti-Screenshot & Screen Capture Protection CSS */
        @media print {
            html, body {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                overflow: hidden !important;
            }
        }
        body.screenshot-blur-shield {
            filter: blur(40px) brightness(0.2) !important;
            user-select: none !important;
            -webkit-user-select: none !important;
            pointer-events: none !important;
            transition: filter 0.15s ease !important;
        }
        #screenshotPrivacyOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.96);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        #screenshotPrivacyOverlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        @endif
    </style>
    @stack('styles')
</head>
<body>

    <!-- Top Animated Rainbow Line -->
    <div class="top-gradient-bar"></div>

    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top navbar-glass py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                @if($siteLogo)
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }}" style="max-height: 40px; width: auto; object-fit: contain;">
                @else
                    <i class="bi bi-journal-richtext fs-3 me-2 text-gradient"></i>
                    <span class="text-gradient">{{ $siteName }}</span>
                    <span class="brand-badge">v12</span>
                @endif
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    @php
                        $headerMenuItems = \App\Models\MenuItem::getMenuTree('header');
                    @endphp
                    @if($headerMenuItems->count() > 0)
                        @foreach($headerMenuItems as $mItem)
                            @if($mItem->children->count() > 0)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ request()->is(ltrim($mItem->url, '/')) ? 'active text-gradient fw-bold' : '' }}" href="{{ $mItem->url }}" role="button" data-bs-toggle="dropdown" aria-expanded="false" target="{{ $mItem->target }}">
                                        @if($mItem->icon) <i class="{{ $mItem->icon }} me-1"></i> @endif {{ $mItem->title }}
                                    </a>
                                    <ul class="dropdown-menu shadow border-0 rounded-3">
                                        @foreach($mItem->children as $child)
                                            <li>
                                                <a class="dropdown-item {{ request()->is(ltrim($child->url, '/')) ? 'active' : '' }}" href="{{ $child->url }}" target="{{ $child->target }}">
                                                    @if($child->icon) <i class="{{ $child->icon }} me-1"></i> @endif {{ $child->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link {{ (request()->is(ltrim($mItem->url, '/')) || (request()->routeIs('home') && $mItem->url === '/')) ? 'active text-gradient fw-bold' : '' }}" href="{{ $mItem->url }}" target="{{ $mItem->target }}">
                                        @if($mItem->icon) <i class="{{ $mItem->icon }} me-1"></i> @endif {{ $mItem->title }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active text-gradient fw-bold' : '' }}" href="{{ route('home') }}">
                                <i class="bi bi-house-door me-1"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('blog.*') ? 'active text-gradient fw-bold' : '' }}" href="{{ route('blog.index') }}">
                                <i class="bi bi-grid me-1"></i> Articles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contact.*') ? 'active text-gradient fw-bold' : '' }}" href="{{ route('contact.index') }}">
                                <i class="bi bi-envelope me-1"></i> Contact
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('faq') ? 'active text-gradient fw-bold' : '' }}" href="{{ route('faq') }}">
                                <i class="bi bi-question-circle me-1"></i> FAQ
                            </a>
                        </li>
                    @endif
                </ul>

                <!-- Search form in navbar -->
                <form class="d-flex me-3 mb-2 mb-lg-0" action="{{ route('blog.index') }}" method="GET">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input class="form-control bg-body-tertiary border-start-0" type="search" name="search" placeholder="Search posts..." value="{{ request('search') }}" aria-label="Search">
                    </div>
                </form>

                <!-- Action items -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Dark/Light Theme Toggle -->
                    <button class="btn btn-outline-secondary btn-theme-toggle" id="themeToggleBtn" type="button" title="Toggle theme">
                        <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                    </button>

                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-gradient btn-sm px-3 rounded-pill">
                            <i class="bi bi-person-plus me-1"></i> Register
                        </a>
                    @else
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle" width="24" height="24">
                                <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                                @if(auth()->user()->isAdmin())
                                    <span class="badge bg-danger ms-1">Admin</span>
                                @elseif(auth()->user()->isAuthor())
                                    <span class="badge bg-info text-dark ms-1">Author</span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                @if(auth()->user()->isAdmin() || auth()->user()->isAuthor())
                                    <li>
                                        <a class="dropdown-item fw-semibold text-primary" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-speedometer2 me-2"></i> Admin Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.posts.create') }}">
                                            <i class="bi bi-pencil-square me-2"></i> Write Article
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person-gear me-2"></i> Profile Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Header Leaderboard Ad Banner -->
    <div class="container my-2">
        @include('components.ad-slot', ['slotName' => 'header', 'label' => 'Header Leaderboard (728x90 / Responsive)', 'minHeight' => '90px', 'class' => 'my-2'])
    </div>

    <!-- Global Toast/Alert Notifications -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm rounded-4 border-0" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm rounded-4 border-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 border-0" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-x-circle me-1"></i> Please check the following errors:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Main Content Slot -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <a class="d-flex align-items-center text-decoration-none mb-3" href="{{ route('home') }}">
                        @if($siteLogo)
                            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" style="max-height: 38px; width: auto; object-fit: contain;">
                        @else
                            <i class="bi bi-journal-richtext fs-3 me-2 text-gradient"></i>
                            <span class="fs-4 fw-bold text-gradient">{{ $siteName }}</span>
                        @endif
                    </a>
                    <p class="text-body-secondary small">
                        {{ $footerText }}
                    </p>
                    <div class="d-flex gap-3 fs-5 mt-3">
                        @if($githubUrl)
                            <a href="{{ $githubUrl }}" target="_blank" class="text-body-secondary hover-lift"><i class="bi bi-github"></i></a>
                        @endif
                        @if($twitterUrl)
                            <a href="{{ $twitterUrl }}" target="_blank" class="text-body-secondary hover-lift"><i class="bi bi-twitter-x"></i></a>
                        @endif
                        @if($linkedinUrl)
                            <a href="{{ $linkedinUrl }}" target="_blank" class="text-body-secondary hover-lift"><i class="bi bi-linkedin"></i></a>
                        @endif
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="fw-bold mb-3 text-gradient">Navigation</h6>
                    <ul class="list-unstyled text-small small">
                        @php
                            $footerMenuItems = \App\Models\MenuItem::getMenuTree('footer');
                        @endphp
                        @if($footerMenuItems->count() > 0)
                            @foreach($footerMenuItems as $fItem)
                                <li class="mb-2">
                                    <a class="link-secondary text-decoration-none" href="{{ $fItem->url }}" target="{{ $fItem->target }}">
                                        @if($fItem->icon) <i class="{{ $fItem->icon }} me-1"></i> @endif {{ $fItem->title }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('home') }}">Home</a></li>
                            <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('blog.index') }}">All Articles</a></li>
                            <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('faq') }}">FAQ</a></li>
                            <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('contact.index') }}">Contact Us</a></li>
                            <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('login') }}">Author Portal</a></li>
                        @endif
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="fw-bold mb-3 text-gradient">Popular Topics</h6>
                    <ul class="list-unstyled text-small small">
                        <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('blog.category', 'laravel-php') }}">Laravel & PHP</a></li>
                        <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('blog.category', 'web-development') }}">Web Development</a></li>
                        <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('blog.category', 'ui-ux-design') }}">UI/UX Design</a></li>
                        <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('blog.category', 'ai-machine-learning') }}">AI & Agents</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-bold mb-3 text-gradient">Subscribe to Newsletter</h6>
                    <p class="text-body-secondary small mb-3">Receive weekly curations on architecture, Laravel tips, and frontend technologies.</p>
                    <form action="{{ route('newsletter.store') }}" method="POST" id="footerNewsletterForm">
                        @csrf
                        <div class="input-group">
                            <input type="email" name="email" class="form-control rounded-start-pill ps-3" placeholder="Enter your email" required>
                            <button class="btn btn-gradient rounded-end-pill px-4" type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>

            <hr class="my-4 border-secondary opacity-25">

            <div class="d-flex flex-wrap justify-content-between align-items-center small text-body-secondary">
                <p class="mb-0">&copy; {{ date('Y') }} Copyright by Shrawan Effects</p>
                <p class="mb-0">All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Switcher & Interactive Mouse Spotlight Script -->
    <script>
        (function() {
            const htmlElement = document.documentElement;
            const themeBtn = document.getElementById('themeToggleBtn');
            const themeIcon = document.getElementById('themeIcon');

            function getPreferredTheme() {
                const stored = localStorage.getItem('theme');
                if (stored) return stored;
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            function setTheme(theme) {
                htmlElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('theme', theme);
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun-fill text-warning';
                } else {
                    themeIcon.className = 'bi bi-moon-stars-fill text-dark';
                }
            }

            setTheme(getPreferredTheme());

            if (themeBtn) {
                themeBtn.addEventListener('click', () => {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    setTheme(newTheme);
                });
            }

            // Interactive Spotlight Mouse Follower on Cards
            document.querySelectorAll('.card-post, .spotlight-card').forEach(card => {
                card.classList.add('spotlight-card');
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    card.style.setProperty('--mouse-x', `${x}px`);
                    card.style.setProperty('--mouse-y', `${y}px`);
                });
            });
        })();
    </script>

    <!-- Floating Anti-Inspect & Content Protection Alert Toast -->
    <div id="securityShieldToast" class="position-fixed bottom-0 start-50 translate-middle-x mb-4 px-4 py-3 bg-dark text-white rounded-pill shadow-lg border border-secondary border-opacity-50 d-none" style="z-index: 99999; backdrop-filter: blur(12px); font-size: 0.9rem;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shield-slash-fill text-warning fs-5"></i>
            <span id="securityShieldToastMsg" class="fw-semibold">Action disabled to protect website content.</span>
        </div>
    </div>

    @if($disableScreenshot)
    <!-- Anti-Screenshot Privacy Overlay -->
    <div id="screenshotPrivacyOverlay" class="d-none">
        <div class="text-center p-4 p-md-5" style="max-width: 550px;">
            <div class="d-inline-flex p-3 rounded-circle bg-danger-subtle text-danger mb-3 shadow-lg border border-danger-subtle">
                <i class="bi bi-camera-video-off-fill fs-1"></i>
            </div>
            <h3 class="fw-bold text-white mb-2">Screen Capture Restricted</h3>
            <p class="text-white-50 mb-0 small">Screenshots, screen recording, and snippet tools are restricted on this website to protect intellectual property.</p>
        </div>
    </div>
    @endif

    <!-- Cyber Security & Anti-Inspect / Anti-Screenshot Script -->
    <script>
        (function() {
            const disableRightClick = {{ $disableRightClick ? 'true' : 'false' }};
            const disableInspect = {{ $disableInspect ? 'true' : 'false' }};
            const disableScreenshot = {{ $disableScreenshot ? 'true' : 'false' }};

            const toast = document.getElementById('securityShieldToast');
            const toastMsg = document.getElementById('securityShieldToastMsg');
            const overlay = document.getElementById('screenshotPrivacyOverlay');
            let toastTimer = null;

            function showSecurityWarning(message) {
                if (!toast) return;
                if (toastMsg) toastMsg.textContent = message;
                toast.classList.remove('d-none');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => {
                    toast.classList.add('d-none');
                }, 3000);
            }

            // 1. Block Context Menu (Right Click)
            if (disableRightClick) {
                document.addEventListener('contextmenu', function(e) {
                    const tag = e.target.tagName;
                    // Allow right click inside interactive text inputs so user can paste/edit
                    if (['INPUT', 'TEXTAREA'].includes(tag) || e.target.isContentEditable) {
                        return;
                    }
                    e.preventDefault();
                    showSecurityWarning('Right-click is disabled to protect website content.');
                    return false;
                }, false);
            }

            // 2. Block Inspect Element, DevTools, View-Source, and Save Hotkeys
            if (disableInspect) {
                document.addEventListener('keydown', function(e) {
                    const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
                    const ctrlOrCmd = isMac ? e.metaKey : e.ctrlKey;

                    // F12 -> DevTools
                    if (e.key === 'F12' || e.keyCode === 123) {
                        e.preventDefault();
                        e.stopPropagation();
                        showSecurityWarning('Developer Tools (F12) are disabled.');
                        return false;
                    }

                    // Ctrl+U / Cmd+Option+U -> View Source
                    if ((ctrlOrCmd && (e.key === 'u' || e.key === 'U' || e.keyCode === 85)) ||
                        (e.altKey && ctrlOrCmd && (e.key === 'u' || e.key === 'U'))) {
                        e.preventDefault();
                        e.stopPropagation();
                        showSecurityWarning('Viewing page source code is disabled.');
                        return false;
                    }

                    // Ctrl+Shift+I / Cmd+Option+I -> Inspect Element
                    // Ctrl+Shift+J / Cmd+Option+J -> Console
                    // Ctrl+Shift+C / Cmd+Option+C -> Element Selector
                    // Ctrl+Shift+K -> Firefox Console
                    if (ctrlOrCmd && e.shiftKey && (
                        ['I', 'i', 'J', 'j', 'C', 'c', 'K', 'k'].includes(e.key) ||
                        [73, 74, 67, 75].includes(e.keyCode)
                    )) {
                        e.preventDefault();
                        e.stopPropagation();
                        showSecurityWarning('Inspect Element and Developer Console are disabled.');
                        return false;
                    }

                    // Ctrl+S / Cmd+S -> Save Webpage
                    if (ctrlOrCmd && (e.key === 's' || e.key === 'S' || e.keyCode === 83)) {
                        if (!['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
                            e.preventDefault();
                            e.stopPropagation();
                            showSecurityWarning('Saving web page is disabled.');
                            return false;
                        }
                    }
                }, true);

                // Disable Image Dragging
                document.addEventListener('dragstart', function(e) {
                    if (e.target.tagName === 'IMG') {
                        e.preventDefault();
                        return false;
                    }
                });

                // Console Warning Shield
                try {
                    console.log('%cSTOP!', 'color: #ef4444; font-family: sans-serif; font-size: 38px; font-weight: 900; -webkit-text-stroke: 1px black;');
                    console.log('%cThis website is protected. Inspecting source code, scraping, or reverse-engineering is prohibited.', 'font-size: 14px; color: #6366f1; font-weight: 600;');
                } catch(err) {}
            }

            // 3. Anti-Screenshot & Screen Capture Protection Suite
            if (disableScreenshot) {
                let obfuscateTimeout = null;

                function triggerScreenshotObfuscation(reason) {
                    document.body.classList.add('screenshot-blur-shield');
                    if (overlay) {
                        overlay.classList.remove('d-none');
                        overlay.classList.add('active');
                    }
                    showSecurityWarning(reason || 'Screenshots and screen capture are disabled.');

                    // Clear clipboard if possible to prevent paste
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        try {
                            navigator.clipboard.writeText('Screenshots & screen recordings are restricted on this website.');
                        } catch(err) {}
                    }

                    clearTimeout(obfuscateTimeout);
                    obfuscateTimeout = setTimeout(() => {
                        document.body.classList.remove('screenshot-blur-shield');
                        if (overlay) {
                            overlay.classList.remove('active');
                            overlay.classList.add('d-none');
                        }
                    }, 2500);
                }

                // Intercept PrintScreen and Snipping Shortcuts
                window.addEventListener('keydown', function(e) {
                    const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
                    const ctrlOrCmd = isMac ? e.metaKey : e.ctrlKey;

                    // PrintScreen Key
                    if (e.key === 'PrintScreen' || e.keyCode === 44 || e.code === 'PrintScreen') {
                        e.preventDefault();
                        e.stopPropagation();
                        triggerScreenshotObfuscation('PrintScreen is disabled on this website.');
                        return false;
                    }

                    // Windows Snipping Tool (Win+Shift+S) / Mac (Cmd+Shift+3/4/5) / Ctrl+Shift+S
                    if (e.shiftKey && (
                        e.key === 's' || e.key === 'S' || e.keyCode === 83 ||
                        e.key === '3' || e.key === '4' || e.key === '5' ||
                        e.keyCode === 51 || e.keyCode === 52 || e.keyCode === 53
                    ) && (ctrlOrCmd || e.metaKey || e.altKey)) {
                        e.preventDefault();
                        e.stopPropagation();
                        triggerScreenshotObfuscation('Screen capture shortcut is disabled.');
                        return false;
                    }

                    // Ctrl+P / Cmd+P (Print to PDF / Print Dialog)
                    if (ctrlOrCmd && (e.key === 'p' || e.key === 'P' || e.keyCode === 80)) {
                        e.preventDefault();
                        e.stopPropagation();
                        triggerScreenshotObfuscation('Printing and PDF export are disabled.');
                        return false;
                    }
                }, true);

                // Keyup listener for PrintScreen (OS capture)
                window.addEventListener('keyup', function(e) {
                    if (e.key === 'PrintScreen' || e.keyCode === 44 || e.code === 'PrintScreen') {
                        triggerScreenshotObfuscation('Screen capture restricted.');
                    }
                }, true);

                // Anti-Snip Window Blur Protection: When Snipping Tool/Lightshot captures screen, browser window loses focus
                let blurTimer = null;
                window.addEventListener('blur', function() {
                    blurTimer = setTimeout(() => {
                        document.body.classList.add('screenshot-blur-shield');
                    }, 80);
                });

                window.addEventListener('focus', function() {
                    clearTimeout(blurTimer);
                    document.body.classList.remove('screenshot-blur-shield');
                    if (overlay) {
                        overlay.classList.remove('active');
                        overlay.classList.add('d-none');
                    }
                });

                // Tab/Window Visibility Change
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        document.body.classList.add('screenshot-blur-shield');
                    } else {
                        document.body.classList.remove('screenshot-blur-shield');
                    }
                });
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>

@php
    $siteName = \App\Models\Setting::get('site_name', 'LaravelBlog');
    $siteLogo = \App\Models\Setting::getLogoUrl();
    $siteFavicon = \App\Models\Setting::getFaviconUrl();
    $contactEmail = \App\Models\Setting::get('contact_email', 'shrawaneffects@gmail.com');
    $footerText = \App\Models\Setting::get('footer_text', 'A modern, high-performance blog platform crafted with Laravel 12 and Bootstrap 5.');
    $facebookUrl = \App\Models\Setting::get('facebook_url', '');
    $twitterUrl = \App\Models\Setting::get('twitter_url', '');
    $instagramUrl = \App\Models\Setting::get('instagram_url', '');
    $linkedinUrl = \App\Models\Setting::get('linkedin_url', '');
    $youtubeUrl = \App\Models\Setting::get('youtube_url', '');
    $pinterestUrl = \App\Models\Setting::get('pinterest_url', '');
    $whatsappUrl = \App\Models\Setting::get('whatsapp_url', '');
    $githubUrl = \App\Models\Setting::get('github_url', '');
    $disableRightClick = \App\Models\Setting::get('disable_right_click', '1') === '1';
    $disableInspect = \App\Models\Setting::get('disable_inspect', '1') === '1';
    $disableScreenshot = \App\Models\Setting::get('disable_screenshot', '1') === '1';
    $rawHpDesc = \App\Models\Setting::get('homepage_meta_description');
    $defaultMetaDesc = (!empty($rawHpDesc) && !str_starts_with($rawHpDesc, 'Shrawan Effects is a website where you can always find tech-relevant content'))
        ? $rawHpDesc
        : 'Explore cutting-edge fullstack web development, Laravel & React engineering, AI automation solutions, and expert technology insights at Shrawan Effects.';
    $defaultMetaKeywords = \App\Models\Setting::get('homepage_meta_keywords', 'fullstack web development, laravel 12, react, php, ai automation, openseo suite, tech tutorials, shrawan effects');
@endphp
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <!-- Google Tag Manager (Optimized Lazy Loading for High Performance & 0 TBT) -->
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      (function(){
        var gtmLoaded = false;
        function initGTM(){
          if (gtmLoaded) return;
          gtmLoaded = true;
          var s = document.createElement('script');
          s.async = true;
          s.src = 'https://www.googletagmanager.com/gtm.js?id=GTM-5W9N6Z6K';
          document.head.appendChild(s);
        }
        if ('requestIdleCallback' in window) {
          window.addEventListener('load', function(){ requestIdleCallback(initGTM, {timeout: 3000}); });
        } else {
          window.addEventListener('load', function(){ setTimeout(initGTM, 1500); });
        }
        ['scroll', 'keydown', 'click', 'touchstart'].forEach(function(e){
          window.addEventListener(e, initGTM, {once: true, passive: true});
        });
      })();
    </script>
    <!-- End Google Tag Manager -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $siteName)</title>
    @if($siteFavicon)
        <link rel="icon" href="{{ $siteFavicon }}">
        <link rel="shortcut icon" href="{{ $siteFavicon }}">
        <link rel="apple-touch-icon" href="{{ $siteFavicon }}">
    @endif
    <meta name="description" content="@yield('meta_description', $defaultMetaDesc)">
    <meta name="keywords" content="@yield('meta_keywords', $defaultMetaKeywords)">
    <meta name="robots" content="@yield('robots_meta', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')">

    <!-- Canonical URL Optimization (Strip query strings & enforce HTTPS) -->
    @php
        $rawCanonical = View::getSection('canonical_url') ?: url()->current();
        $cleanCanonical = preg_replace('/^http:/i', 'https:', strtok($rawCanonical, '?'));
        if (strlen($cleanCanonical) > 8) {
            $cleanCanonical = rtrim($cleanCanonical, '/');
        }
        $appTitle = View::getSection('title', $siteName);
        $appDesc = View::getSection('meta_description', $defaultMetaDesc);
    @endphp
    <link rel="canonical" href="{{ $cleanCanonical }}">
    @yield('lcp_preload')

    <!-- OpenGraph Social Metadata -->
    <meta property="og:title" content="@yield('og_title', $appTitle)">
    <meta property="og:description" content="@yield('og_description', $appDesc)">
    <meta property="og:image" content="@yield('og_image', asset('storage/default.jpg'))">
    <meta property="og:url" content="{{ $cleanCanonical }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $siteName }}">

    <!-- Google SERP Thumbnails & Rich Media Discovery -->
    @php
        $metaThumbImage = View::getSection('og_image') ?: asset('storage/default.jpg');
    @endphp
    <meta name="thumbnail" content="{{ $metaThumbImage }}">
    <link rel="image_src" href="{{ $metaThumbImage }}">
    <meta property="og:image:secure_url" content="{{ $metaThumbImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="675">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:alt" content="@yield('og_title', $appTitle)">

    <!-- Twitter / X Card Metadata -->
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('twitter_title', View::getSection('og_title', $appTitle))">
    <meta name="twitter:description" content="@yield('twitter_description', View::getSection('og_description', $appDesc))">
    <meta name="twitter:image" content="@yield('twitter_image', View::getSection('og_image', asset('storage/default.jpg')))">
    <meta name="twitter:image:alt" content="@yield('twitter_title', View::getSection('og_title', $appTitle))">

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

    <!-- Resource Hints & Preconnects for Core Web Vitals -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://www.googletagmanager.com">

    <!-- Fonts: Preload critical Geist Font directly to prevent layout shift & FOIT -->
    <link rel="preload" href="https://fonts.gstatic.com/s/geist/v5/gyByhwUxId8gMEwcGFWNOITd.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600;700&family=Geist:wght@400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600;700&family=Geist:wght@400;500;600;700&display=swap"></noscript>

    <!-- Bootstrap 5.3 CSS (Locally hosted, 1-Year Immutable Cached, Zero 3rd Party Handshake) -->
    <link rel="preload" href="{{ asset('css/bootstrap.min.css') }}" as="style">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Bootstrap Icons (Non-blocking Preload) -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></noscript>

    <!-- Cyberpunk HUD Theme Stylesheet (Cached 1 Year) -->
    <link rel="preload" href="{{ asset('css/cyberpunk-hud.min.css') }}" as="style">
    <link rel="stylesheet" href="{{ asset('css/cyberpunk-hud.min.css') }}">

    <style>
        :root{--font-main:'Geist',-apple-system,BlinkMacSystemFont,sans-serif;--neon-green:#00ff66;--cyber-dark:#050804}
        body{font-family:var(--font-main);min-height:100vh;background-color:#050804;color:#e4f5e7}
    </style>
    @stack('styles')
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5W9N6Z6K"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Skip to main content for Accessibility -->
    <a href="#main-content" class="visually-hidden-focusable btn btn-success position-absolute top-0 start-0 m-2 px-3 py-2" style="z-index: 9999;">Skip to main content</a>

    <!-- Top Animated Rainbow Line -->
    <div class="top-gradient-bar"></div>

    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top navbar-glass py-3" role="navigation" aria-label="Main Navigation">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                @if($siteLogo)
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }}" width="160" height="40" class="me-2" style="max-height: 40px; width: 160px; object-fit: contain; aspect-ratio: 160 / 40;" fetchpriority="high">
                @else
                    <i class="bi bi-cpu fs-3 me-2 text-gradient"></i>
                    <span class="text-gradient font-cyber">{{ $siteName }}</span>
                    <span class="brand-badge"><span class="cyber-beacon me-1"></span>SYS ONLINE</span>
                @endif
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-success"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    @php
                        $headerMenuItems = \App\Models\MenuItem::getMenuTree('header');
                        $bulkIpCheckerUrl = \Illuminate\Support\Facades\Route::has('services.bulk-ip-checker')
                            ? route('services.bulk-ip-checker')
                            : url('/services/bulk-website-ip-checker');
                        $isBulkIpCheckerActive = request()->routeIs('services.bulk-ip-checker')
                            || request()->is('services/bulk-website-ip-checker');
                    @endphp
                    @if($headerMenuItems->count() > 0)
                        @foreach($headerMenuItems as $mItem)
                            @php
                                $mItemUrlLower = strtolower(trim($mItem->url ?? ''));
                                $isServicesMenuItem = str_contains($mItemUrlLower, '/services')
                                    || strtolower(trim($mItem->title ?? '')) === 'services';
                                $hasAllServicesChild = $mItem->children->contains(function ($c) {
                                    $u = rtrim(strtolower(trim($c->url ?? '')), '/');
                                    return $u === '/services' || str_ends_with($u, '/services');
                                });
                                $hasBulkIpChild = $mItem->children->contains(function ($c) {
                                    return str_contains(strtolower($c->url ?? ''), 'bulk-website-ip-checker');
                                });
                            @endphp
                            @if($mItem->children->count() > 0 || $isServicesMenuItem)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {{ (request()->is(ltrim($mItem->url, '/')) || ($isServicesMenuItem && (request()->routeIs('services.*') || $isBulkIpCheckerActive))) ? 'active text-gradient fw-bold' : '' }}" href="{{ $mItem->url }}" role="button" data-bs-toggle="dropdown" aria-expanded="false" target="{{ $mItem->target }}">
                                        @if($mItem->icon) <i class="{{ $mItem->icon }} me-1"></i> @endif {{ $mItem->title }}
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-dark shadow border border-success border-opacity-25 rounded-3 py-2">
                                        @if($isServicesMenuItem)
                                            @if(!$hasAllServicesChild)
                                                <li>
                                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('services.index') ? 'active text-success fw-bold' : '' }}" href="{{ route('services.index') }}">
                                                        <i class="bi bi-grid-3x3-gap-fill text-success me-2"></i> All Services
                                                    </a>
                                                </li>
                                            @endif
                                            @if(!$hasBulkIpChild)
                                                <li>
                                                    <a class="dropdown-item font-mono py-2 {{ $isBulkIpCheckerActive ? 'active text-success fw-bold' : '' }}" href="{{ $bulkIpCheckerUrl }}">
                                                        <i class="bi bi-hdd-network text-info me-2"></i> Bulk Website IP Checker
                                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 font-mono ms-1" style="font-size: 0.62rem;">TOOL</span>
                                                    </a>
                                                </li>
                                            @endif
                                            @if($mItem->children->count() > 0)
                                                <li><hr class="dropdown-divider border-secondary border-opacity-50"></li>
                                            @endif
                                        @endif
                                        @foreach($mItem->children as $child)
                                            @if($child->children->count() > 0)
                                                <li class="dropdown-submenu dropend">
                                                    <a class="dropdown-item dropdown-toggle d-flex align-items-center justify-content-between {{ request()->is(ltrim($child->url, '/')) ? 'active text-success fw-bold' : '' }}" href="{{ $child->url }}" data-bs-toggle="dropdown" aria-expanded="false" target="{{ $child->target }}">
                                                        <span>@if($child->icon) <i class="{{ $child->icon }} me-2"></i> @endif {{ $child->title }}</span>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-dark shadow border border-success border-opacity-25 rounded-3 py-2">
                                                        @foreach($child->children as $subChild)
                                                            @if($subChild->children->count() > 0)
                                                                <li class="dropdown-submenu dropend">
                                                                    <a class="dropdown-item dropdown-toggle d-flex align-items-center justify-content-between {{ request()->is(ltrim($subChild->url, '/')) ? 'active text-success fw-bold' : '' }}" href="{{ $subChild->url }}" data-bs-toggle="dropdown" aria-expanded="false" target="{{ $subChild->target }}">
                                                                        <span>@if($subChild->icon) <i class="{{ $subChild->icon }} me-2"></i> @endif {{ $subChild->title }}</span>
                                                                    </a>
                                                                    <ul class="dropdown-menu dropdown-menu-dark shadow border border-success border-opacity-25 rounded-3 py-2">
                                                                        @foreach($subChild->children as $tier3)
                                                                            <li>
                                                                                <a class="dropdown-item {{ request()->is(ltrim($tier3->url, '/')) ? 'active text-success fw-bold' : '' }}" href="{{ $tier3->url }}" target="{{ $tier3->target }}">
                                                                                    @if($tier3->icon) <i class="{{ $tier3->icon }} me-2"></i> @endif {{ $tier3->title }}
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a class="dropdown-item {{ request()->is(ltrim($subChild->url, '/')) ? 'active text-success fw-bold' : '' }}" href="{{ $subChild->url }}" target="{{ $subChild->target }}">
                                                                        @if($subChild->icon) <i class="{{ $subChild->icon }} me-2"></i> @endif {{ $subChild->title }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item {{ request()->is(ltrim($child->url, '/')) ? 'active text-success fw-bold' : '' }}" href="{{ $child->url }}" target="{{ $child->target }}">
                                                        @if($child->icon) <i class="{{ $child->icon }} me-2"></i> @endif {{ $child->title }}
                                                    </a>
                                                </li>
                                            @endif
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
                        <!-- Services Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ (request()->routeIs('services.*') || $isBulkIpCheckerActive) ? 'active text-gradient fw-bold' : '' }}" href="{{ route('services.index') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-briefcase me-1"></i> Services
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark shadow border border-success border-opacity-25 rounded-3 py-2">
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('services.index') ? 'active text-success fw-bold' : '' }}" href="{{ route('services.index') }}">
                                        <i class="bi bi-grid-3x3-gap-fill text-success me-2"></i> All Services
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ $isBulkIpCheckerActive ? 'active text-success fw-bold' : '' }}" href="{{ $bulkIpCheckerUrl }}">
                                        <i class="bi bi-hdd-network text-info me-2"></i> Bulk Website IP Checker
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 font-mono ms-1" style="font-size: 0.62rem;">TOOL</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider border-secondary border-opacity-50"></li>
                                <li>
                                    <a class="dropdown-item font-mono py-2" href="{{ route('services.index') }}#web-development">
                                        <i class="bi bi-code-slash text-primary me-2"></i> Web Development
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- OpenSEO Suite Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('openseo.*') ? 'active text-gradient fw-bold' : '' }}" href="{{ route('openseo.domain') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-radar me-1 text-success"></i> OpenSEO <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 font-mono ms-1" style="font-size: 0.65rem;">SUITE</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark shadow border border-success border-opacity-25 rounded-3 py-2">
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('openseo.domain') ? 'active text-success fw-bold' : '' }}" href="{{ route('openseo.domain') }}">
                                        <i class="bi bi-speedometer2 text-success me-2"></i> Domain Overview
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('openseo.keywords') ? 'active text-success fw-bold' : '' }}" href="{{ route('openseo.keywords') }}">
                                        <i class="bi bi-key text-info me-2"></i> Keyword Research
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('openseo.rank-tracking') ? 'active text-success fw-bold' : '' }}" href="{{ route('openseo.rank-tracking') }}">
                                        <i class="bi bi-graph-up-arrow text-success me-2"></i> Rank Tracker
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('openseo.competitors') ? 'active text-success fw-bold' : '' }}" href="{{ route('openseo.competitors') }}">
                                        <i class="bi bi-crosshair text-warning me-2"></i> Competitor Insights
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('openseo.backlinks') ? 'active text-success fw-bold' : '' }}" href="{{ route('openseo.backlinks') }}">
                                        <i class="bi bi-link-45deg text-primary me-2"></i> Backlinks Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('openseo.audit') ? 'active text-success fw-bold' : '' }}" href="{{ route('openseo.audit') }}">
                                        <i class="bi bi-shield-check text-success me-2"></i> Technical Site Audit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('openseo.ai-visibility') ? 'active text-success fw-bold' : '' }}" href="{{ route('openseo.ai-visibility') }}">
                                        <i class="bi bi-robot text-info me-2"></i> AI Visibility (LLMs)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('openseo.local-seo') ? 'active text-success fw-bold' : '' }}" href="{{ route('openseo.local-seo') }}">
                                        <i class="bi bi-geo-alt text-danger me-2"></i> Google Maps Geo-Grid
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('openseo.search-performance') ? 'active text-success fw-bold' : '' }}" href="{{ route('openseo.search-performance') }}">
                                        <i class="bi bi-google text-white me-2"></i> Search Console
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider border-secondary border-opacity-50"></li>
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('openseo.reports') ? 'active text-success fw-bold' : '' }}" href="{{ route('openseo.reports') }}">
                                        <i class="bi bi-file-earmark-bar-graph text-info me-2"></i> Reports & Exports
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item font-mono py-2 {{ request()->routeIs('openseo.settings') ? 'active text-success fw-bold' : '' }}" href="{{ route('openseo.settings') }}">
                                        <i class="bi bi-sliders text-secondary me-2"></i> Settings & API Keys
                                    </a>
                                </li>
                            </ul>
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

                <!-- Search form in navbar with telemetry styling -->
                <form class="d-flex me-3 mb-2 mb-lg-0" action="{{ route('blog.index') }}" method="GET" role="search">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text border-end-0 nav-search-addon"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input class="form-control border-start-0 nav-search-input" type="search" name="search" placeholder="John 3:16" value="{{ request('search') }}" aria-label="Search">
                    </div>
                </form>

                <!-- Action items -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Dark/Light Theme Toggle -->
                    <button class="btn btn-outline-secondary btn-theme-toggle" id="themeToggleBtn" type="button" title="Toggle theme" aria-label="Toggle dark/light theme">
                        <i class="bi bi-moon-stars-fill" id="themeIcon" aria-hidden="true"></i>
                    </button>

                    @guest
                        <a href="{{ route('login') }}" class="btn btn-tactical btn-sm px-3 rounded-pill">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-gradient btn-sm px-3 rounded-pill">
                            <i class="bi bi-shield-lock me-1"></i> Register
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

        @if(isset($errors) && $errors->any())
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
    <main id="main-content" role="main">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer py-5 mt-5" role="contentinfo">
        <div class="container">
            <div class="row g-4">
                <!-- Section 1: Brand & Telemetry -->
                <div class="col-lg-6 col-md-12">
                    <a class="d-flex align-items-center text-decoration-none mb-3" href="{{ route('home') }}">
                        @if($siteLogo)
                            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" width="160" height="38" class="me-2" style="max-height: 38px; width: 160px; object-fit: contain; aspect-ratio: 160 / 38;">
                        @else
                            <i class="bi bi-cpu fs-3 me-2 text-gradient"></i>
                            <span class="fs-4 fw-bold text-gradient font-cyber">{{ $siteName }}</span>
                        @endif
                    </a>
                    <p class="text-body-secondary small mb-3">
                        {{ $footerText }}
                    </p>
                    <div class="mb-3 small text-body-secondary font-mono">
                        <div class="d-flex align-items-start gap-2 mb-1">
                            <i class="bi bi-geo-alt-fill text-success flex-shrink-0 mt-1" aria-hidden="true"></i>
                            <span>House No. 1049, Jeevan Nagar, Gounchhi, FARIDABAD, Haryana 121004</span>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3 fs-5 mt-2">
                        @if(!empty($facebookUrl))
                            <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" class="text-body-secondary hover-lift" title="Facebook" aria-label="Visit Shrawan Effects on Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                        @endif
                        @if(!empty($twitterUrl))
                            <a href="{{ $twitterUrl }}" target="_blank" rel="noopener noreferrer" class="text-body-secondary hover-lift" title="X (Twitter)" aria-label="Visit Shrawan Effects on X (Twitter)"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
                        @endif
                        @if(!empty($instagramUrl))
                            <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="text-body-secondary hover-lift" title="Instagram" aria-label="Visit Shrawan Effects on Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                        @endif
                        @if(!empty($linkedinUrl))
                            <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener noreferrer" class="text-body-secondary hover-lift" title="LinkedIn" aria-label="Visit Shrawan Effects on LinkedIn"><i class="bi bi-linkedin" aria-hidden="true"></i></a>
                        @endif
                        @if(!empty($youtubeUrl))
                            <a href="{{ $youtubeUrl }}" target="_blank" rel="noopener noreferrer" class="text-body-secondary hover-lift" title="YouTube" aria-label="Visit Shrawan Effects on YouTube"><i class="bi bi-youtube" aria-hidden="true"></i></a>
                        @endif
                        @if(!empty($pinterestUrl))
                            <a href="{{ $pinterestUrl }}" target="_blank" rel="noopener noreferrer" class="text-body-secondary hover-lift" title="Pinterest" aria-label="Visit Shrawan Effects on Pinterest"><i class="bi bi-pinterest" aria-hidden="true"></i></a>
                        @endif
                        @if(!empty($whatsappUrl))
                            @php
                                $waCleanNumber = preg_replace('/[^0-9]/', '', $whatsappUrl);
                                $waLink = str_starts_with($whatsappUrl, 'http') ? $whatsappUrl : 'https://wa.me/' . $waCleanNumber;
                            @endphp
                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="text-body-secondary hover-lift text-success-hover" title="WhatsApp" aria-label="Chat with Shrawan Effects on WhatsApp"><i class="bi bi-whatsapp" aria-hidden="true"></i></a>
                        @endif
                        @if(!empty($githubUrl))
                            <a href="{{ $githubUrl }}" target="_blank" rel="noopener noreferrer" class="text-body-secondary hover-lift" title="GitHub" aria-label="Visit Shrawan Effects on GitHub"><i class="bi bi-github" aria-hidden="true"></i></a>
                        @endif
                    </div>
                </div>

                <!-- Section 2: Quick Protocols & Sectors -->
                <div class="col-lg-6 col-md-12">
                    <h6 class="fw-bold mb-3 text-gradient font-cyber">DIRECT PROTOCOLS</h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <ul class="list-unstyled text-small small mb-0">
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
                                    <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('home') }}"><i class="bi bi-chevron-right me-1 text-success small"></i>Home</a></li>
                                    <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('services.index') }}"><i class="bi bi-chevron-right me-1 text-success small"></i>Our Services</a></li>
                                    <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('blog.index') }}"><i class="bi bi-chevron-right me-1 text-success small"></i>All Articles</a></li>
                                    <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('faq') }}"><i class="bi bi-chevron-right me-1 text-success small"></i>FAQ</a></li>
                                    <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('contact.index') }}"><i class="bi bi-chevron-right me-1 text-success small"></i>Contact Us</a></li>
                                    <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('login') }}"><i class="bi bi-chevron-right me-1 text-success small"></i>Author Portal</a></li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="list-unstyled text-small small mb-0">
                                <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('blog.category', 'laravel-php') }}"><i class="bi bi-terminal me-1 text-success small"></i>Laravel & PHP</a></li>
                                <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('blog.category', 'web-development') }}"><i class="bi bi-code-slash me-1 text-success small"></i>Web Dev</a></li>
                                <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('blog.category', 'ui-ux-design') }}"><i class="bi bi-palette me-1 text-success small"></i>UI/UX Design</a></li>
                                <li class="mb-2"><a class="link-secondary text-decoration-none" href="{{ route('blog.category', 'ai-machine-learning') }}"><i class="bi bi-robot me-1 text-success small"></i>AI & Agents</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4 border-secondary opacity-25">

            <div class="d-flex flex-wrap justify-content-between align-items-center small text-body-secondary font-mono">
                <p class="mb-0">&copy; {{ date('Y') }} Shrawan Effects - All Rights Reserved.</p>
                <p class="mb-0 text-success"><i class="bi bi-shield-check me-1"></i> Colossians 1:16</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS (Deferred for PageSpeed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

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

    <!-- Multi-level Nested Submenus Mobile/Touch Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.dropdown-submenu > a.dropdown-toggle').forEach(function(toggleLink) {
                toggleLink.addEventListener('click', function(e) {
                    if (window.innerWidth < 992 || ('ontouchstart' in window)) {
                        e.preventDefault();
                        e.stopPropagation();
                        const currentSubmenu = this.nextElementSibling;
                        if (currentSubmenu && currentSubmenu.classList.contains('dropdown-menu')) {
                            const parentUl = this.closest('.dropdown-menu');
                            if (parentUl) {
                                parentUl.querySelectorAll('.dropdown-menu.show').forEach(function(openMenu) {
                                    if (openMenu !== currentSubmenu && !currentSubmenu.contains(openMenu)) {
                                        openMenu.classList.remove('show');
                                    }
                                });
                            }
                            currentSubmenu.classList.toggle('show');
                        }
                    }
                });
            });
        });
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

@php
    $siteName = \App\Models\Setting::get('site_name', 'LaravelBlog');
    $siteLogo = \App\Models\Setting::getLogoUrl();
    $siteFavicon = \App\Models\Setting::getFaviconUrl();
    $contactEmail = \App\Models\Setting::get('contact_email', 'shrawaneffects@gmail.com');
    $footerText = \App\Models\Setting::get('footer_text', 'A modern, high-performance blog platform crafted with Laravel 12 and Bootstrap 5.');
    $twitterUrl = \App\Models\Setting::get('twitter_url', 'https://twitter.com');
    $githubUrl = \App\Models\Setting::get('github_url', 'https://github.com');
    $linkedinUrl = \App\Models\Setting::get('linkedin_url', 'https://linkedin.com');
    $disableRightClick = \App\Models\Setting::get('disable_right_click', '1') === '1';
    $disableInspect = \App\Models\Setting::get('disable_inspect', '1') === '1';
    $disableScreenshot = \App\Models\Setting::get('disable_screenshot', '1') === '1';
    $defaultMetaDesc = \App\Models\Setting::get('homepage_meta_description') ?: \App\Models\Setting::get('footer_text', 'A modern tech publication built on Laravel 12 & Bootstrap 5');
    $defaultMetaKeywords = \App\Models\Setting::get('homepage_meta_keywords', 'laravel, php, web development, bootstrap 5, tech');
@endphp
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
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
    <meta name="description" content="@yield('meta_description', $defaultMetaDesc)">
    <meta name="keywords" content="@yield('meta_keywords', $defaultMetaKeywords)">
    <meta name="robots" content="@yield('robots_meta', 'index, follow')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    <!-- OpenGraph Social Metadata -->
    <meta property="og:title" content="@yield('og_title', View::getSection('title', $siteName))">
    <meta property="og:description" content="@yield('og_description', View::getSection('meta_description', $defaultMetaDesc))">
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

    <!-- Fonts: Geist & Geist Mono by Vercel -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@100..900&family=Geist:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/geist@1.3.1/dist/core.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/geist@1.3.1/dist/mono.css">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --font-main: 'Geist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-cyber: 'Geist', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-mono: 'Geist Mono', monospace;

            /* Futuristic HUD Color System (Neon Lime Green, Tactical Olive, Pitch Black, Holographic White) */
            --neon-green: #00ff66;
            --neon-green-dim: #00cc52;
            --neon-green-glow: rgba(0, 255, 102, 0.45);
            --neon-green-subtle: rgba(0, 255, 102, 0.12);

            --neon-cyan: #00f0ff;
            --neon-cyan-glow: rgba(0, 240, 255, 0.45);

            --tactical-olive: #213608;
            --tactical-olive-dark: #121e05;
            --tactical-olive-border: rgba(0, 255, 102, 0.28);
            --tactical-olive-glow: rgba(33, 54, 8, 0.6);

            --cyber-dark: #050804;
            --cyber-dark-card: #091206;
            --cyber-dark-elevated: #0f1c09;

            --gradient-primary: linear-gradient(135deg, #00ff66 0%, #00f0ff 100%);
            --gradient-secondary: linear-gradient(135deg, #00f0ff 0%, #3b82f6 100%);
            --gradient-sunset: linear-gradient(135deg, #ff3366 0%, #ff9900 100%);
            --gradient-emerald: linear-gradient(135deg, #00ff66 0%, #10b981 100%);
            --gradient-tactical: linear-gradient(135deg, #213608 0%, #0e1a05 100%);
        }

        /* Custom Futuristic Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #050804;
        }
        ::-webkit-scrollbar-thumb {
            background: #213608;
            border-radius: 4px;
            border: 1px solid rgba(0, 255, 102, 0.3);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #00ff66;
            box-shadow: 0 0 10px #00ff66;
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

        /* Dark Mode: Futuristic Cyberpunk HUD */
        [data-bs-theme="dark"] body {
            background-color: #050804;
            background-image: 
                radial-gradient(circle at 15% 10%, rgba(0, 255, 102, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(0, 240, 255, 0.05) 0%, transparent 45%),
                linear-gradient(rgba(0, 255, 102, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 102, 0.035) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 36px 36px, 36px 36px;
            background-attachment: fixed;
            color: #e4f5e7;
        }

        /* Light Mode: High-Tech Tactical Tech Lab */
        [data-bs-theme="light"] body {
            background-color: #f6faf5;
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(33, 54, 8, 0.04) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(0, 255, 102, 0.04) 0%, transparent 50%),
                linear-gradient(rgba(33, 54, 8, 0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(33, 54, 8, 0.025) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 36px 36px, 36px 36px;
            background-attachment: fixed;
            color: #0d1a08;
        }

        /* Top High-Tech Laser Beam Accent Line */
        .top-gradient-bar {
            height: 3px;
            background: linear-gradient(90deg, #00ff66, #00f0ff, #213608, #00ff66);
            background-size: 200% 100%;
            animation: laserScan 4s linear infinite;
            box-shadow: 0 0 12px rgba(0, 255, 102, 0.6);
            position: relative;
            z-index: 1050;
        }

        @keyframes laserScan {
            0% { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }

        /* Cyberpunk Font Classes */
        .font-cyber {
            font-family: var(--font-cyber);
            letter-spacing: 0.5px;
        }

        .font-mono {
            font-family: var(--font-mono);
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

        /* Cyber Beacon Pulse Dot */
        .cyber-beacon {
            width: 8px;
            height: 8px;
            background-color: #00ff66;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #00ff66;
            animation: beaconPulse 2s infinite ease-in-out;
            vertical-align: middle;
        }

        @keyframes beaconPulse {
            0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 10px #00ff66; }
            50% { opacity: 0.35; transform: scale(0.75); box-shadow: 0 0 3px #00ff66; }
        }

        /* Telemetry Chip & Monospace Metadata */
        .telemetry-tag {
            font-family: var(--font-mono);
            font-size: 0.73rem;
            letter-spacing: 0.6px;
            padding: 3px 8px;
            border-radius: 4px;
            background: rgba(33, 54, 8, 0.45);
            border: 1px solid rgba(0, 255, 102, 0.35);
            color: #00ff66;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Navbar & Glassmorphic HUD Bar */
        .navbar-glass {
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            background-color: rgba(5, 8, 4, 0.88) !important;
            border-bottom: 1px solid rgba(0, 255, 102, 0.22);
            transition: all 0.3s ease;
        }

        [data-bs-theme="light"] .navbar-glass {
            background-color: rgba(255, 255, 255, 0.92) !important;
            border-bottom: 1px solid rgba(33, 54, 8, 0.15);
        }

        .navbar-brand {
            font-family: var(--font-cyber);
            font-weight: 800;
            letter-spacing: 0.5px;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.03);
        }

        .brand-badge {
            background: rgba(33, 54, 8, 0.85);
            border: 1px solid rgba(0, 255, 102, 0.4);
            color: #00ff66;
            font-family: var(--font-mono);
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 0.74rem;
            letter-spacing: 0.8px;
            margin-left: 8px;
            box-shadow: 0 0 10px rgba(0, 255, 102, 0.2);
            transition: all 0.3s ease;
        }

        .brand-badge:hover {
            border-color: #00ff66;
            box-shadow: 0 0 15px rgba(0, 255, 102, 0.5);
        }

        /* Nav links hover cyber glow */
        .nav-link {
            position: relative;
            font-weight: 600;
            font-size: 0.92rem;
            letter-spacing: 0.3px;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--gradient-primary);
            box-shadow: 0 0 8px #00ff66;
            transition: width 0.3s ease, left 0.3s ease;
            border-radius: 2px;
        }

        .nav-link:hover::after, .nav-link.active::after {
            width: 100%;
            left: 0;
        }

        /* Hero Banner with Futuristic Cyber Mesh */
        .hero-banner {
            position: relative;
            padding: 3.5rem 0;
            overflow: hidden;
            border-bottom: 1px solid rgba(0, 255, 102, 0.15);
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 140%;
            height: 200%;
            background: radial-gradient(circle at 30% 30%, rgba(0, 255, 102, 0.12) 0%, transparent 60%),
                        radial-gradient(circle at 75% 65%, rgba(0, 240, 255, 0.08) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
            animation: pulseAura 12s ease-in-out infinite alternate;
        }

        @keyframes pulseAura {
            0% { transform: scale(1) rotate(0deg); }
            100% { transform: scale(1.08) rotate(3deg); }
        }

        /* Cyber Cards & High-Tech HUD Container */
        .card-post {
            border: 1px solid rgba(0, 255, 102, 0.16) !important;
            border-radius: 18px;
            overflow: hidden;
            background: rgba(9, 18, 6, 0.85);
            backdrop-filter: blur(10px);
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), border-color 0.4s ease;
            position: relative;
            z-index: 1;
        }

        [data-bs-theme="light"] .card-post {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(33, 54, 8, 0.15) !important;
        }

        .card-post::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 18px;
            padding: 1.5px;
            background: linear-gradient(135deg, rgba(0, 255, 102, 0.6), rgba(0, 240, 255, 0.6), rgba(33, 54, 8, 0.4));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .card-post:hover {
            transform: translateY(-7px) scale(1.012);
            box-shadow: 0 18px 36px -12px rgba(0, 255, 102, 0.22), 0 0 22px rgba(0, 240, 255, 0.12);
            border-color: rgba(0, 255, 102, 0.5) !important;
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
            transform: scale(1.07);
            filter: brightness(1.08) contrast(1.05);
        }

        .featured-thumb {
            height: 380px;
            object-fit: cover;
            width: 100%;
            border-radius: 16px;
            transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1), filter 0.5s ease;
        }

        .card-post:hover .featured-thumb {
            transform: scale(1.04);
            filter: brightness(1.08);
        }

        .card-post .card-title a {
            transition: color 0.3s ease, text-shadow 0.3s ease;
        }

        .card-post:hover .card-title a {
            color: var(--neon-green) !important;
            text-shadow: 0 0 10px rgba(0, 255, 102, 0.35);
        }

        /* Neon & Cyber Buttons */
        .btn-gradient {
            background: linear-gradient(135deg, #00ff66 0%, #00d255 100%);
            color: #050804 !important;
            font-family: var(--font-cyber);
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            border: 1px solid #00ff66;
            border-radius: 10px;
            padding: 0.6rem 1.4rem;
            transition: all 0.35s ease;
            box-shadow: 0 0 16px rgba(0, 255, 102, 0.35);
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-gradient:hover {
            box-shadow: 0 0 28px rgba(0, 255, 102, 0.65);
            transform: translateY(-2px) scale(1.02);
            color: #050804 !important;
        }

        .btn-gradient:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-tactical {
            background: rgba(33, 54, 8, 0.75);
            color: #00ff66 !important;
            border: 1px solid rgba(0, 255, 102, 0.4);
            font-family: var(--font-cyber);
            font-size: 0.82rem;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            border-radius: 10px;
            padding: 0.6rem 1.4rem;
            transition: all 0.35s ease;
            box-shadow: inset 0 0 10px rgba(0, 255, 102, 0.12);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-tactical:hover {
            background: #213608;
            border-color: #00ff66;
            box-shadow: 0 0 18px rgba(0, 255, 102, 0.35), inset 0 0 12px rgba(0, 255, 102, 0.2);
            color: #ffffff !important;
            transform: translateY(-2px);
        }

        .btn-gradient-sunset {
            background: var(--gradient-sunset);
            color: #fff !important;
            border: none;
            border-radius: 10px;
            font-family: var(--font-cyber);
            font-size: 0.82rem;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 0.6rem 1.4rem;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(244, 63, 94, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-gradient-sunset:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 0 25px rgba(244, 63, 94, 0.55);
            color: #fff !important;
        }

        .btn-gradient-cyan {
            background: linear-gradient(135deg, #00f0ff 0%, #0099ff 100%);
            color: #050804 !important;
            border: 1px solid #00f0ff;
            border-radius: 10px;
            font-family: var(--font-cyber);
            font-size: 0.82rem;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 0.6rem 1.4rem;
            transition: all 0.4s ease;
            box-shadow: 0 0 16px rgba(0, 240, 255, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-gradient-cyan:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 0 25px rgba(0, 240, 255, 0.65);
            color: #050804 !important;
        }

        /* Hover Lift Utility */
        .hover-lift {
            transition: transform 0.35s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.35s ease, border-color 0.35s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px) scale(1.015);
            box-shadow: 0 12px 28px rgba(0, 255, 102, 0.15) !important;
        }

        /* Category Tactical Cyber Cards */
        .category-card {
            border: 1px solid rgba(0, 255, 102, 0.18);
            border-radius: 14px;
            background: rgba(9, 18, 6, 0.7);
            backdrop-filter: blur(8px);
            transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            overflow: hidden;
        }

        [data-bs-theme="light"] .category-card {
            background: #ffffff;
            border-color: rgba(33, 54, 8, 0.12);
        }

        .category-card:hover {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 12px 24px rgba(0, 255, 102, 0.2);
            border-color: rgba(0, 255, 102, 0.55);
        }

        .category-card .category-icon {
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .category-card:hover .category-icon {
            transform: scale(1.18) rotate(6deg);
        }

        /* Trending Cards Hover */
        .trending-card {
            padding: 1.1rem;
            border-radius: 16px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 255, 102, 0.12);
            background: rgba(9, 18, 6, 0.55);
        }

        [data-bs-theme="light"] .trending-card {
            background: #ffffff;
            border-color: rgba(33, 54, 8, 0.1);
        }

        .trending-card:hover {
            background: rgba(33, 54, 8, 0.35);
            border-color: rgba(0, 255, 102, 0.45);
            transform: translateX(6px);
            box-shadow: 0 0 20px rgba(0, 255, 102, 0.15);
        }

        .trending-card .trend-number {
            font-family: var(--font-cyber);
            color: #00ff66;
            text-shadow: 0 0 10px rgba(0, 255, 102, 0.4);
            transition: transform 0.3s ease;
            display: inline-block;
        }

        .trending-card:hover .trend-number {
            transform: scale(1.15);
        }

        /* Tag Pills */
        .tag-pill {
            font-family: var(--font-mono);
            font-size: 0.8rem;
            padding: 0.38rem 0.85rem;
            border-radius: 6px;
            text-decoration: none;
            background-color: rgba(33, 54, 8, 0.4);
            color: #00ff66;
            border: 1px solid rgba(0, 255, 102, 0.25);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: inline-flex;
            align-items: center;
            margin: 3px;
        }

        .tag-pill:hover {
            background: #00ff66;
            color: #050804 !important;
            border-color: #00ff66;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 0 15px rgba(0, 255, 102, 0.5);
        }

        .tag-pill:hover .badge {
            background-color: rgba(5, 8, 4, 0.35) !important;
            color: #050804 !important;
        }

        /* Avatars with Neon Ring on Hover */
        .author-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(0, 255, 102, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .author-avatar:hover {
            transform: scale(1.12);
            box-shadow: 0 0 0 2px #00ff66, 0 0 15px rgba(0, 255, 102, 0.5);
        }

        .author-avatar-lg {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 0 3px rgba(0, 255, 102, 0.35);
            transition: all 0.3s ease;
        }

        .author-avatar-lg:hover {
            transform: scale(1.06) rotate(3deg);
            box-shadow: 0 0 0 4px #00ff66, 0 0 25px rgba(0, 255, 102, 0.45);
        }

        /* Newsletter Cyber Gradient Card */
        .card-newsletter-gradient {
            background: linear-gradient(135deg, #142807 0%, #213608 50%, #081404 100%);
            border: 1px solid rgba(0, 255, 102, 0.3) !important;
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 0 25px rgba(0, 255, 102, 0.15);
        }

        .card-newsletter-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 255, 102, 0.15) 0%, transparent 70%);
            animation: pulseAura 8s infinite alternate;
        }

        /* Badges */
        .badge-category {
            font-family: var(--font-cyber);
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.6px;
            font-weight: 700;
            padding: 5px 11px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .badge-category:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 0 14px rgba(0, 255, 102, 0.4);
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
            border-radius: 14px;
            margin: 1.75rem 0;
            border: 1px solid rgba(0, 255, 102, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            transition: transform 0.3s ease;
        }

        .post-content img:hover {
            transform: scale(1.01);
        }

        .post-content pre {
            background: #080d05;
            color: #00ff66;
            font-family: var(--font-mono);
            padding: 1.4rem;
            border-radius: 12px;
            overflow-x: auto;
            border: 1px solid rgba(0, 255, 102, 0.25);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4), inset 0 0 15px rgba(0, 255, 102, 0.05);
        }

        .post-content blockquote {
            border-left: 4px solid #00ff66;
            padding: 1.2rem 1.8rem;
            background: rgba(33, 54, 8, 0.35);
            border-radius: 0 12px 12px 0;
            font-style: italic;
            position: relative;
        }

        .footer {
            margin-top: auto;
            border-top: 1px solid rgba(0, 255, 102, 0.2);
            background-color: #050804;
        }

        [data-bs-theme="light"] .footer {
            background-color: #f1f7f0;
            border-top-color: rgba(33, 54, 8, 0.15);
        }

        .btn-theme-toggle {
            width: 40px;
            height: 40px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid rgba(0, 255, 102, 0.3);
            background: rgba(33, 54, 8, 0.35);
            color: #00ff66;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-theme-toggle:hover {
            transform: rotate(30deg) scale(1.1);
            border-color: #00ff66;
            box-shadow: 0 0 15px rgba(0, 255, 102, 0.5);
        }

        .sticky-top-widget {
            position: sticky;
            top: 5.5rem;
        }

        /* High-Tech Spotlight Mouse Glow for cards */
        .spotlight-card {
            position: relative;
            overflow: hidden;
        }

        .spotlight-card::after {
            content: '';
            position: absolute;
            top: var(--mouse-y, -1000px);
            left: var(--mouse-x, -1000px);
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(0, 255, 102, 0.12) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 2;
        }

        .spotlight-card:hover::after {
            opacity: 1;
        }

        /* Navbar & Global Search inputs */
        .nav-search-addon {
            background: rgba(33, 54, 8, 0.45);
            border-color: rgba(0, 255, 102, 0.25);
            color: #00ff66;
        }

        .nav-search-input {
            background: rgba(33, 54, 8, 0.25);
            border-color: rgba(0, 255, 102, 0.25);
            font-family: var(--font-mono);
            font-size: 0.8rem;
            color: inherit;
        }

        .captcha-question-badge {
            background: rgba(33, 54, 8, 0.55);
            border: 1px solid rgba(0, 255, 102, 0.35);
            color: #00ff66;
        }

        .newsletter-input {
            background: rgba(5, 8, 4, 0.7);
            color: #00ff66;
            border: 1px solid rgba(0, 255, 102, 0.3) !important;
        }

        .empty-state-box {
            background: rgba(9, 18, 6, 0.5);
            border: 1px dashed rgba(0, 255, 102, 0.3);
        }

        .bi-spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        /* =========================================================
           LIGHT MODE: CRYSTAL-CLEAR HIGH CONTRAST & ZERO NEON TEXT
           ========================================================= */
        [data-bs-theme="light"] {
            --neon-green: #15803d;
            --neon-green-glow: rgba(21, 128, 61, 0.15);
            --neon-cyan: #0284c7;
            --neon-cyan-glow: rgba(2, 132, 199, 0.15);

            --tactical-olive: #f0fdf4;
            --tactical-olive-dark: #dcfce7;
            --tactical-olive-border: rgba(21, 128, 61, 0.25);
            --tactical-olive-glow: rgba(21, 128, 61, 0.08);

            --cyber-dark: #ffffff;
            --cyber-dark-card: #ffffff;
            --cyber-dark-elevated: #f8fafc;

            --gradient-primary: linear-gradient(135deg, #15803d 0%, #0369a1 100%);
            --gradient-secondary: linear-gradient(135deg, #0284c7 0%, #1d4ed8 100%);
            --gradient-sunset: linear-gradient(135deg, #dc2626 0%, #ea580c 100%);
            --gradient-emerald: linear-gradient(135deg, #15803d 0%, #047857 100%);
            --gradient-tactical: linear-gradient(135deg, #f0fdf4 0%, #e2e8f0 100%);
        }

        /* Light view text reset: Completely removes all glowing neon shadows for 100% crisp legibility */
        [data-bs-theme="light"] h1,
        [data-bs-theme="light"] h2,
        [data-bs-theme="light"] h3,
        [data-bs-theme="light"] h4,
        [data-bs-theme="light"] h5,
        [data-bs-theme="light"] h6,
        [data-bs-theme="light"] p,
        [data-bs-theme="light"] span,
        [data-bs-theme="light"] a,
        [data-bs-theme="light"] div,
        [data-bs-theme="light"] label,
        [data-bs-theme="light"] input,
        [data-bs-theme="light"] button,
        [data-bs-theme="light"] .font-cyber,
        [data-bs-theme="light"] .font-mono,
        [data-bs-theme="light"] .trend-number {
            text-shadow: none !important;
            filter: none !important;
        }

        /* High-contrast base typography */
        [data-bs-theme="light"] body {
            background-color: #f8fafc;
            color: #1e293b !important;
        }

        [data-bs-theme="light"] h1,
        [data-bs-theme="light"] h2,
        [data-bs-theme="light"] h3,
        [data-bs-theme="light"] h4,
        [data-bs-theme="light"] h5,
        [data-bs-theme="light"] h6 {
            color: #0f172a !important;
            font-weight: 700;
        }

        [data-bs-theme="light"] .text-body {
            color: #0f172a !important;
        }

        [data-bs-theme="light"] .text-body-secondary {
            color: #475569 !important;
        }

        [data-bs-theme="light"] .text-muted {
            color: #64748b !important;
        }

        [data-bs-theme="light"] .text-success {
            color: #15803d !important;
        }

        /* High-contrast gradient headings in light mode */
        [data-bs-theme="light"] .text-gradient {
            background: linear-gradient(135deg, #15803d 0%, #0369a1 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            font-weight: 800;
        }

        [data-bs-theme="light"] .text-gradient-cyan {
            background: linear-gradient(135deg, #0284c7 0%, #1d4ed8 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            font-weight: 800;
        }

        [data-bs-theme="light"] .text-gradient-emerald {
            background: linear-gradient(135deg, #15803d 0%, #047857 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            font-weight: 800;
        }

        [data-bs-theme="light"] .text-gradient-fire {
            background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            font-weight: 800;
        }

        /* Light mode telemetry tags & badges */
        [data-bs-theme="light"] .telemetry-tag {
            background: #f0fdf4 !important;
            border: 1px solid #86efac !important;
            color: #15803d !important;
            font-weight: 700;
            box-shadow: none !important;
        }

        [data-bs-theme="light"] .brand-badge {
            background: #dcfce7 !important;
            border: 1px solid #86efac !important;
            color: #15803d !important;
            font-weight: 700;
            box-shadow: none !important;
        }

        [data-bs-theme="light"] .brand-badge:hover {
            background: #bbf7d0 !important;
            border-color: #15803d !important;
            color: #14532d !important;
        }

        [data-bs-theme="light"] .cyber-beacon {
            background-color: #16a34a !important;
            box-shadow: 0 0 4px rgba(22, 163, 74, 0.4) !important;
        }

        [data-bs-theme="light"] .top-gradient-bar {
            background: linear-gradient(90deg, #15803d, #0284c7, #166534, #15803d) !important;
            box-shadow: 0 0 6px rgba(21, 128, 61, 0.25) !important;
        }

        [data-bs-theme="light"] .hud-ticker {
            background: #f0fdf4 !important;
            border: 1px solid #bbf7d0 !important;
            color: #1e293b !important;
        }

        [data-bs-theme="light"] .hud-ticker .text-body-secondary {
            color: #475569 !important;
        }

        [data-bs-theme="light"] .priority-intel-badge {
            background: #ffffff !important;
            border: 1px solid #16a34a !important;
            color: #15803d !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08) !important;
        }

        [data-bs-theme="light"] .category-docs-count {
            background: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
            font-weight: 600;
        }

        /* Light mode cards & spotlight */
        [data-bs-theme="light"] .card-post {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04) !important;
        }

        [data-bs-theme="light"] .card-post:hover {
            border-color: #15803d !important;
            box-shadow: 0 12px 28px rgba(21, 128, 61, 0.12) !important;
        }

        [data-bs-theme="light"] .card-post:hover .card-title a {
            color: #15803d !important;
            text-shadow: none !important;
        }

        [data-bs-theme="light"] .card-post::before,
        [data-bs-theme="light"] .spotlight-card::after {
            display: none !important;
        }

        [data-bs-theme="light"] .category-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
        }

        [data-bs-theme="light"] .category-card:hover {
            border-color: #15803d !important;
            box-shadow: 0 8px 20px rgba(21, 128, 61, 0.12) !important;
        }

        [data-bs-theme="light"] .trending-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03) !important;
        }

        [data-bs-theme="light"] .trending-card:hover {
            background: #f8fafc !important;
            border-color: #15803d !important;
            box-shadow: 0 4px 16px rgba(21, 128, 61, 0.1) !important;
        }

        [data-bs-theme="light"] .trending-card .trend-number {
            color: #15803d !important;
            text-shadow: none !important;
        }

        [data-bs-theme="light"] .trending-card:hover .trend-number {
            color: #166534 !important;
        }

        [data-bs-theme="light"] .tag-pill {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
        }

        [data-bs-theme="light"] .tag-pill:hover {
            background: #15803d !important;
            color: #ffffff !important;
            border-color: #15803d !important;
            box-shadow: 0 4px 12px rgba(21, 128, 61, 0.2) !important;
        }

        [data-bs-theme="light"] .tag-pill .badge {
            background: #e2e8f0 !important;
            color: #334155 !important;
        }

        [data-bs-theme="light"] .tag-pill:hover .badge {
            background: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }

        /* Light mode buttons */
        [data-bs-theme="light"] .btn-gradient {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%) !important;
            color: #ffffff !important;
            border-color: #15803d !important;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25) !important;
            text-shadow: none !important;
        }

        [data-bs-theme="light"] .btn-gradient:hover {
            background: linear-gradient(135deg, #15803d 0%, #14532d 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 6px 18px rgba(22, 163, 74, 0.35) !important;
        }

        [data-bs-theme="light"] .btn-tactical {
            background: #f0fdf4 !important;
            color: #15803d !important;
            border: 1px solid #86efac !important;
            box-shadow: none !important;
            font-weight: 700;
        }

        [data-bs-theme="light"] .btn-tactical:hover {
            background: #15803d !important;
            color: #ffffff !important;
            border-color: #15803d !important;
            box-shadow: 0 4px 14px rgba(21, 128, 61, 0.25) !important;
        }

        [data-bs-theme="light"] .btn-gradient-cyan {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
            color: #ffffff !important;
            border-color: #0284c7 !important;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25) !important;
        }

        [data-bs-theme="light"] .btn-theme-toggle {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #334155;
            box-shadow: none;
        }

        [data-bs-theme="light"] .btn-theme-toggle:hover {
            background: #e2e8f0;
            border-color: #94a3b8;
            color: #0f172a;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        /* Light mode inputs, search, captcha & newsletter */
        [data-bs-theme="light"] .nav-search-addon {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #475569 !important;
        }

        [data-bs-theme="light"] .nav-search-input {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        [data-bs-theme="light"] .captcha-question-badge {
            background: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
        }

        [data-bs-theme="light"] .card-newsletter-gradient {
            background: linear-gradient(135deg, #15803d 0%, #0d5c2b 100%) !important;
            border: 1px solid #15803d !important;
            box-shadow: 0 10px 25px rgba(21, 128, 61, 0.2) !important;
        }

        [data-bs-theme="light"] .card-newsletter-gradient input {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
        }

        [data-bs-theme="light"] .footer-email-input {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        [data-bs-theme="light"] .newsletter-input {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
        }

        [data-bs-theme="light"] .empty-state-box {
            background: #ffffff !important;
            border: 1px dashed #cbd5e1 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        [data-bs-theme="light"] .auth-card {
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06) !important;
        }

        /* Light mode code blocks & blockquotes */
        [data-bs-theme="light"] .post-content pre {
            background: #0f172a !important;
            color: #38bdf8 !important;
            border: 1px solid #334155 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }

        [data-bs-theme="light"] .post-content blockquote {
            border-left: 4px solid #15803d !important;
            background: #f0fdf4 !important;
            color: #1e293b !important;
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
                            <a class="nav-link {{ request()->routeIs('services.*') ? 'active text-gradient fw-bold' : '' }}" href="{{ route('services.index') }}">
                                <i class="bi bi-briefcase me-1"></i> Services
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

                <!-- Search form in navbar with telemetry styling -->
                <form class="d-flex me-3 mb-2 mb-lg-0" action="{{ route('blog.index') }}" method="GET">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text border-end-0 nav-search-addon"><i class="bi bi-search"></i></span>
                        <input class="form-control border-start-0 nav-search-input" type="search" name="search" placeholder="Search telemetry..." value="{{ request('search') }}" aria-label="Search">
                    </div>
                </form>

                <!-- Action items -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Dark/Light Theme Toggle -->
                    <button class="btn btn-outline-secondary btn-theme-toggle" id="themeToggleBtn" type="button" title="Toggle theme">
                        <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
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
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <!-- Section 1: Brand & Telemetry -->
                <div class="col-lg-4 col-md-12">
                    <a class="d-flex align-items-center text-decoration-none mb-3" href="{{ route('home') }}">
                        @if($siteLogo)
                            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" style="max-height: 38px; width: auto; object-fit: contain;">
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
                            <i class="bi bi-geo-alt-fill text-success flex-shrink-0 mt-1"></i>
                            <span>House No. 1049, Jeevan Nagar, Gounchhi, FARIDABAD, Haryana 121004</span>
                        </div>
                    </div>
                    <div class="d-flex gap-3 fs-5 mt-2">
                        @if($githubUrl)
                            <a href="{{ $githubUrl }}" target="_blank" class="text-body-secondary hover-lift" title="GitHub"><i class="bi bi-github"></i></a>
                        @endif
                        @if($twitterUrl)
                            <a href="{{ $twitterUrl }}" target="_blank" class="text-body-secondary hover-lift" title="Twitter / X"><i class="bi bi-twitter-x"></i></a>
                        @endif
                        @if($linkedinUrl)
                            <a href="{{ $linkedinUrl }}" target="_blank" class="text-body-secondary hover-lift" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        @endif
                    </div>
                </div>

                <!-- Section 2: Quick Protocols & Sectors -->
                <div class="col-lg-4 col-md-6">
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

                <!-- Section 3: Cipher Transmission / Newsletter -->
                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-bold mb-3 text-gradient font-cyber">CIPHER TRANSMISSION</h6>
                    <p class="text-body-secondary small mb-3">Subscribe to receive weekly telemetry curations on architecture, Laravel innovations, and futuristic tech.</p>
                    <form action="{{ route('newsletter.store') }}" method="POST" id="footerNewsletterForm">
                        @csrf
                        <div class="input-group">
                            <input type="email" name="email" class="form-control rounded-start-pill ps-3 footer-email-input" placeholder="user@domain.com" required style="background: rgba(33,54,8,0.25); border-color: rgba(0,255,102,0.3); font-family: var(--font-mono); font-size: 0.85rem;">
                            <button class="btn btn-gradient rounded-end-pill px-4" type="submit">TRANSMIT</button>
                        </div>
                    </form>
                </div>
            </div>

            <hr class="my-4 border-secondary opacity-25">

            <div class="d-flex flex-wrap justify-content-between align-items-center small text-body-secondary font-mono">
                <p class="mb-0">&copy; {{ date('Y') }} SHRAWAN EFFECTS // ALL RIGHTS RESERVED.</p>
                <p class="mb-0 text-success"><i class="bi bi-shield-check me-1"></i> Everything is https</p>
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

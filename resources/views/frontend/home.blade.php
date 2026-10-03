@extends('layouts.app')

@php
    $hpCustomTitle = \App\Models\Setting::get('homepage_meta_title');
    $siteName = \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects'));
    $siteTagline = \App\Models\Setting::get('site_tagline', 'Futuristic Tech Publications & Cyber Insights');
    $hpMetaTitle = $hpCustomTitle ?: ($siteTagline ? ($siteName . ' - ' . $siteTagline) : ('Home - ' . $siteName));

    $hpCustomDesc = \App\Models\Setting::get('homepage_meta_description');
    $hpMetaDesc = $hpCustomDesc ?: \App\Models\Setting::get('footer_text', 'A futuristic, high-performance blog platform crafted with Laravel 12 and Cyberpunk HUD aesthetic.');

    $hpMetaKeywords = \App\Models\Setting::get('homepage_meta_keywords', 'laravel, php, web development, cyberpunk, futuristic, tech hud');
@endphp

@section('title', $hpMetaTitle)
@section('meta_description', $hpMetaDesc)
@section('meta_keywords', $hpMetaKeywords)
@section('og_title', $hpMetaTitle)
@section('og_description', $hpMetaDesc)
@section('twitter_title', $hpMetaTitle)
@section('twitter_description', $hpMetaDesc)
@section('canonical_url', route('home'))

@section('content')
    <!-- Futuristic HUD Status Ticker -->
    <div class="container pt-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between p-2 px-3 rounded-3" style="background: rgba(33,54,8,0.35); border: 1px solid rgba(0,255,102,0.22); font-family: var(--font-mono); font-size: 0.76rem;">
            <div class="d-flex align-items-center gap-2">
                <span class="cyber-beacon"></span>
                <span class="text-success fw-bold">// SYSTEM: ACTIVE</span>
                <span class="text-body-secondary d-none d-md-inline">| NODE: SECURE_CORE_01</span>
                <span class="text-body-secondary d-none d-lg-inline">| PROTOCOL: TLS 1.3 / HTTP/2.0</span>
            </div>
            <div class="d-flex align-items-center gap-3 text-body-secondary">
                <span class="d-none d-sm-inline"><i class="bi bi-cpu text-success me-1"></i> LATENCY: 12ms</span>
                <span class="text-success"><i class="bi bi-shield-check me-1"></i> 100% OPERATIONAL</span>
            </div>
        </div>
    </div>

    <!-- Hero Banner / Featured Section -->
    @if($featuredPosts->count() > 0)
        <section class="hero-banner">
            <div class="container position-relative" style="z-index: 1;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="telemetry-tag"><i class="bi bi-broadcast"></i> TRANSMISSION // FEATURED INTEL</span>
                </div>
                <div class="row g-4 align-items-stretch">
                    <!-- Main Hero Post -->
                    @php $leadPost = $featuredPosts->first(); @endphp
                    <div class="col-lg-7">
                        <div class="card card-post spotlight-card h-100 position-relative shadow-lg">
                            <div class="post-thumb-container position-relative">
                                <img src="{{ $leadPost->image_url }}" class="featured-thumb" alt="{{ $leadPost->title }}">
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge" style="background: rgba(5,8,4,0.85); border: 1px solid #00ff66; color: #00ff66; font-family: var(--font-mono); font-size: 0.72rem;">
                                        <i class="bi bi-star-fill text-warning me-1"></i> PRIORITY INTEL
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-4 d-flex flex-column justify-content-end">
                                <div class="mb-2 d-flex align-items-center gap-2">
                                    @if($leadPost->category)
                                        <a href="{{ route('blog.category', $leadPost->category->slug) }}" class="badge badge-category text-white text-decoration-none" style="background-color: {{ $leadPost->category->color }};">
                                            {{ $leadPost->category->name }}
                                        </a>
                                    @endif
                                </div>
                                <h2 class="card-title fw-bold">
                                    <a href="{{ route('blog.show', $leadPost->slug) }}" class="text-decoration-none text-body stretched-link">
                                        {{ $leadPost->title }}
                                    </a>
                                </h2>
                                <p class="card-text text-body-secondary mt-2">{{ $leadPost->excerpt }}</p>
                                <div class="d-flex align-items-center mt-3 pt-3 border-top border-secondary border-opacity-25">
                                    <img src="{{ $leadPost->author->avatar_url }}" alt="{{ $leadPost->author->name }}" class="author-avatar me-3">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-0 fw-semibold">{{ $leadPost->author->name }}</h6>
                                        <small class="text-body-secondary font-mono" style="font-size: 0.78rem;">
                                            {{ $leadPost->published_at ? $leadPost->published_at->format('M d, Y') : 'Recently' }} &bull; // {{ $leadPost->reading_time }} MIN READ
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Secondary Featured Posts -->
                    <div class="col-lg-5 d-flex flex-column gap-4">
                        @foreach($featuredPosts->skip(1) as $fPost)
                            <div class="card card-post spotlight-card flex-grow-1 shadow-sm">
                                <div class="row g-0 h-100">
                                    <div class="col-sm-5 post-thumb-container">
                                        <img src="{{ $fPost->image_url }}" class="img-fluid h-100 w-100 post-thumb object-fit-cover rounded-start" alt="{{ $fPost->title }}" style="min-height: 160px;">
                                    </div>
                                    <div class="col-sm-7 d-flex flex-column justify-content-center p-3">
                                        <div>
                                            @if($fPost->category)
                                                <a href="{{ route('blog.category', $fPost->category->slug) }}" class="badge badge-category text-white text-decoration-none mb-1" style="background-color: {{ $fPost->category->color }};">
                                                    {{ $fPost->category->name }}
                                                </a>
                                            @endif
                                        </div>
                                        <h5 class="fw-bold mb-2 card-title">
                                            <a href="{{ route('blog.show', $fPost->slug) }}" class="text-decoration-none text-body">
                                                {{ Str::limit($fPost->title, 55) }}
                                            </a>
                                        </h5>
                                        <div class="d-flex align-items-center mt-auto font-mono text-body-secondary" style="font-size: 0.75rem;">
                                            <span>{{ $fPost->published_at ? $fPost->published_at->format('M d') : 'Recent' }}</span>
                                            <span class="mx-2">&bull;</span>
                                            <span class="text-success"><i class="bi bi-clock me-1"></i>{{ $fPost->reading_time }}m</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Trending Articles Strip -->
    @if($trendingPosts->count() > 0)
        <section class="py-5 border-bottom position-relative" style="background: rgba(9,18,6,0.35);">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h3 class="fw-bold mb-0 d-flex align-items-center font-cyber">
                        <i class="bi bi-cpu-fill text-success me-2 fs-3"></i>
                        <span>TRENDING // <span class="text-gradient">TELEMETRY</span></span>
                    </h3>
                    <a href="{{ route('blog.index', ['sort' => 'popular']) }}" class="btn btn-tactical btn-sm rounded-pill px-3">
                        DISPATCH LOG <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="row g-4">
                    @foreach($trendingPosts as $index => $tPost)
                        <div class="col-lg-3 col-md-6">
                            <div class="trending-card d-flex gap-3 h-100 spotlight-card">
                                <span class="trend-number fs-1 fw-bold opacity-85 lh-1">0{{ $index + 1 }}</span>
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <img src="{{ $tPost->author->avatar_url }}" alt="{{ $tPost->author->name }}" class="rounded-circle author-avatar" style="width: 22px; height: 22px;">
                                        <span class="small fw-semibold">{{ $tPost->author->name }}</span>
                                    </div>
                                    <h6 class="fw-bold mb-2">
                                        <a href="{{ route('blog.show', $tPost->slug) }}" class="text-decoration-none text-body">
                                            {{ Str::limit($tPost->title, 65) }}
                                        </a>
                                    </h6>
                                    <div class="mt-auto font-mono text-body-secondary" style="font-size: 0.75rem;">
                                        <span>{{ $tPost->published_at ? $tPost->published_at->format('M d, Y') : 'Recent' }}</span>
                                        <span class="mx-1">&bull;</span>
                                        <span class="text-success"><i class="bi bi-eye me-1"></i>{{ number_format($tPost->views_count) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Category Highlights Section -->
    @if($categories->count() > 0)
        <section class="py-5 border-bottom position-relative">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h3 class="fw-bold mb-0 font-cyber">
                        <i class="bi bi-grid-3x3-gap-fill text-gradient me-2"></i>
                        <span>SECTORS // <span class="text-gradient">TOPICS</span></span>
                    </h3>
                    <a href="{{ route('blog.index') }}" class="btn btn-tactical btn-sm rounded-pill px-3">ALL SECTORS</a>
                </div>

                <div class="row g-3">
                    @foreach($categories as $category)
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('blog.category', $category->slug) }}" class="category-card text-decoration-none text-center p-3 h-100 shadow-sm d-block">
                                <div class="category-icon d-inline-flex align-items-center justify-content-center mx-auto mb-2 rounded-circle text-white shadow-sm" style="width: 50px; height: 50px; background: linear-gradient(135deg, {{ $category->color }}, #00ff66);">
                                    <i class="bi bi-bookmark-fill fs-5"></i>
                                </div>
                                <h6 class="fw-bold text-body mb-1 text-truncate">{{ $category->name }}</h6>
                                <span class="badge font-mono rounded-pill px-2" style="background: rgba(33,54,8,0.5); border: 1px solid rgba(0,255,102,0.3); color: #00ff66;">
                                    {{ $category->published_posts_count }} DOCS
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Homepage Mid-Section Ad Slot -->
    <div class="container my-2">
        @include('components.ad-slot', ['slotName' => 'home_middle', 'label' => 'Homepage Mid-Feed Leaderboard (728x90 / Responsive)', 'minHeight' => '100px'])
    </div>

    <!-- Latest Articles with Sidebar -->
    <section class="py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Articles Grid -->
                <div class="col-lg-8">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h3 class="fw-bold mb-0 font-cyber">
                            <i class="bi bi-hdd-network-fill text-success me-2"></i>
                            <span>DATASTREAM // <span class="text-gradient">LATEST</span></span>
                        </h3>
                        <a href="{{ route('blog.index') }}" class="btn btn-tactical btn-sm rounded-pill px-3">ALL DISPATCHES</a>
                    </div>

                    <div class="row g-4">
                        @forelse($recentPosts as $post)
                            <div class="col-md-6">
                                <div class="card card-post spotlight-card h-100 shadow-sm">
                                    <div class="post-thumb-container position-relative">
                                        <img src="{{ $post->image_url }}" class="card-img-top post-thumb" alt="{{ $post->title }}">
                                        @if($post->category)
                                            <a href="{{ route('blog.category', $post->category->slug) }}" class="badge badge-category text-white text-decoration-none position-absolute top-0 start-0 m-3" style="background-color: {{ $post->category->color }};">
                                                {{ $post->category->name }}
                                            </a>
                                        @endif
                                    </div>
                                    <div class="card-body d-flex flex-column p-4">
                                        <h5 class="card-title fw-bold mb-2">
                                            <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none text-body">
                                                {{ $post->title }}
                                            </a>
                                        </h5>
                                        <p class="card-text text-body-secondary small mb-3">
                                            {{ Str::limit($post->excerpt, 110) }}
                                        </p>
                                        <div class="mt-auto pt-3 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="author-avatar me-2" style="width: 32px; height: 32px;">
                                                <span class="small fw-semibold">{{ $post->author->name }}</span>
                                            </div>
                                            <div class="font-mono text-body-secondary" style="font-size: 0.75rem;">
                                                <i class="bi bi-clock me-1 text-success"></i>{{ $post->reading_time }}m
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="p-5 text-center rounded-4" style="background: rgba(9,18,6,0.5); border: 1px dashed rgba(0,255,102,0.3);">
                                    <i class="bi bi-cpu fs-1 text-success opacity-50"></i>
                                    <h5 class="mt-3 font-cyber">NO DATA PACKETS DETECTED</h5>
                                    <p class="text-body-secondary font-mono small">Telemetry stream will update once new intel is published.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('blog.index') }}" class="btn btn-gradient btn-lg px-5 shadow-lg rounded-pill">
                            <i class="bi bi-grid me-2"></i> EXPLORE ALL DISPATCHES
                        </a>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-top-widget d-flex flex-column gap-4">
                        <!-- Search Box Widget -->
                        <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift" style="border: 1px solid rgba(0,255,102,0.18) !important;">
                            <h5 class="fw-bold mb-3 font-cyber"><i class="bi bi-terminal me-2 text-success"></i> DATABASE QUERY</h5>
                            <form action="{{ route('blog.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control rounded-start-pill ps-3" placeholder="Search keywords..." style="border-color: rgba(0,255,102,0.25); font-family: var(--font-mono); font-size: 0.85rem;">
                                    <button class="btn btn-gradient rounded-end-pill px-3" type="submit"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <!-- Popular Tags Widget -->
                        @if($popularTags->count() > 0)
                            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift" style="border: 1px solid rgba(0,255,102,0.18) !important;">
                                <h5 class="fw-bold mb-3 font-cyber"><i class="bi bi-tags me-2 text-success"></i> INTEL TAGS</h5>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($popularTags as $tag)
                                        <a href="{{ route('blog.tag', $tag->slug) }}" class="tag-pill">
                                            #{{ $tag->name }} <span class="badge ms-1 rounded-pill" style="background: rgba(0,255,102,0.15); color: #00ff66;">{{ $tag->published_posts_count }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Sidebar Ad Slot -->
                        @include('components.ad-slot', ['slotName' => 'sidebar', 'label' => 'Sidebar Display Ad (300x250)', 'minHeight' => '250px', 'class' => 'my-0'])

                        <!-- Newsletter CTA Card -->
                        <div class="card card-newsletter-gradient p-4 p-md-5 text-white hover-lift">
                            <div class="position-relative" style="z-index: 1;">
                                <i class="bi bi-broadcast-pin fs-1 mb-2 d-inline-block text-success"></i>
                                <h4 class="fw-bold font-cyber">CIPHER TRANSMISSION</h4>
                                <p class="small text-white-50">Receive curated architectural blueprints, zero-day tutorials, and deep engineering intel.</p>
                                <form action="{{ route('newsletter.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="email" name="email" class="form-control rounded-pill border-0 px-3 py-2 font-mono" placeholder="agent@shrawaneffects.com" style="background: rgba(5,8,4,0.7); color: #00ff66; border: 1px solid rgba(0,255,102,0.3) !important;" required>
                                    </div>
                                    <button type="submit" class="btn btn-gradient w-100 rounded-pill py-2">
                                        <i class="bi bi-shield-check me-1"></i> INITIATE SUBSCRIPTION
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

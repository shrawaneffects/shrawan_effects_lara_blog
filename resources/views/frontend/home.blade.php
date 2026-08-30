@extends('layouts.app')

@section('title', 'Home - ' . config('app.name', 'Laravel 12 Blog'))
@section('canonical_url', route('home'))

@section('content')
    <!-- Hero Banner / Featured Section -->
    @if($featuredPosts->count() > 0)
        <section class="hero-banner">
            <div class="container position-relative" style="z-index: 1;">
                <div class="row g-4 align-items-stretch">
                    <!-- Main Hero Post -->
                    @php $leadPost = $featuredPosts->first(); @endphp
                    <div class="col-lg-7">
                        <div class="card card-post spotlight-card h-100 border-0 shadow-sm position-relative">
                            <div class="post-thumb-container">
                                <img src="{{ $leadPost->image_url }}" class="featured-thumb" alt="{{ $leadPost->title }}">
                            </div>
                            <div class="card-body p-4 d-flex flex-column justify-content-end">
                                <div class="mb-2 d-flex align-items-center gap-2">
                                    @if($leadPost->category)
                                        <a href="{{ route('blog.category', $leadPost->category->slug) }}" class="badge badge-category text-white text-decoration-none" style="background-color: {{ $leadPost->category->color }};">
                                            {{ $leadPost->category->name }}
                                        </a>
                                    @endif
                                    <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> Featured</span>
                                </div>
                                <h2 class="card-title fw-bold">
                                    <a href="{{ route('blog.show', $leadPost->slug) }}" class="text-decoration-none text-body stretched-link">
                                        {{ $leadPost->title }}
                                    </a>
                                </h2>
                                <p class="card-text text-body-secondary mt-2">{{ $leadPost->excerpt }}</p>
                                <div class="d-flex align-items-center mt-3 pt-3 border-top">
                                    <img src="{{ $leadPost->author->avatar_url }}" alt="{{ $leadPost->author->name }}" class="author-avatar me-3">
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $leadPost->author->name }}</h6>
                                        <small class="text-body-secondary">{{ $leadPost->published_at ? $leadPost->published_at->format('M d, Y') : 'Recently' }} &bull; {{ $leadPost->reading_time }} min read</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Secondary Featured Posts -->
                    <div class="col-lg-5 d-flex flex-column gap-4">
                        @foreach($featuredPosts->skip(1) as $fPost)
                            <div class="card card-post spotlight-card flex-grow-1 border-0 shadow-sm">
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
                                        <div class="d-flex align-items-center mt-auto small text-body-secondary">
                                            <span>{{ $fPost->published_at ? $fPost->published_at->format('M d') : 'Recent' }}</span>
                                            <span class="mx-2">&bull;</span>
                                            <span><i class="bi bi-clock me-1 text-primary"></i>{{ $fPost->reading_time }} min</span>
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
        <section class="py-5 bg-body-tertiary border-bottom">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h3 class="fw-bold mb-0 d-flex align-items-center">
                        <i class="bi bi-fire text-gradient-fire me-2 fs-2"></i>
                        <span>Trending <span class="text-gradient-fire">Now</span></span>
                    </h3>
                    <a href="{{ route('blog.index', ['sort' => 'popular']) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 hover-lift">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="row g-4">
                    @foreach($trendingPosts as $index => $tPost)
                        <div class="col-lg-3 col-md-6">
                            <div class="trending-card d-flex gap-3 h-100 spotlight-card">
                                <span class="trend-number fs-1 fw-black opacity-75 lh-1">0{{ $index + 1 }}</span>
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
                                    <div class="mt-auto small text-body-secondary">
                                        <span>{{ $tPost->published_at ? $tPost->published_at->format('M d, Y') : 'Recent' }}</span>
                                        <span class="mx-1">&bull;</span>
                                        <span><i class="bi bi-eye me-1 text-primary"></i>{{ number_format($tPost->views_count) }}</span>
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
                    <h3 class="fw-bold mb-0">
                        <i class="bi bi-tags-fill text-gradient me-2"></i>
                        <span>Explore <span class="text-gradient">Topics</span></span>
                    </h3>
                    <a href="{{ route('blog.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 hover-lift">All Articles</a>
                </div>

                <div class="row g-3">
                    @foreach($categories as $category)
                        <div class="col-lg-2 col-md-4 col-6">
                            <a href="{{ route('blog.category', $category->slug) }}" class="category-card text-decoration-none text-center p-3 h-100 shadow-sm d-block">
                                <div class="category-icon d-inline-flex align-items-center justify-content-center mx-auto mb-2 rounded-circle text-white shadow-sm" style="width: 50px; height: 50px; background: linear-gradient(135deg, {{ $category->color }}, #6366f1);">
                                    <i class="bi bi-bookmark-fill fs-5"></i>
                                </div>
                                <h6 class="fw-bold text-body mb-1 text-truncate">{{ $category->name }}</h6>
                                <span class="badge bg-secondary-subtle text-secondary small rounded-pill px-2">{{ $category->published_posts_count }} articles</span>
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
                        <h3 class="fw-bold mb-0">
                            <i class="bi bi-stars text-gradient-emerald me-2"></i>
                            <span>Latest <span class="text-gradient-emerald">Articles</span></span>
                        </h3>
                        <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 hover-lift">See all articles</a>
                    </div>

                    <div class="row g-4">
                        @forelse($recentPosts as $post)
                            <div class="col-md-6">
                                <div class="card card-post spotlight-card h-100 shadow-sm border-0">
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
                                        <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="author-avatar me-2" style="width: 32px; height: 32px;">
                                                <span class="small fw-semibold">{{ $post->author->name }}</span>
                                            </div>
                                            <div class="small text-body-secondary">
                                                <i class="bi bi-clock me-1 text-primary"></i>{{ $post->reading_time }} min
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="p-5 text-center bg-body-tertiary rounded-4">
                                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                                    <h5 class="mt-3">No articles published yet.</h5>
                                    <p class="text-muted">Check back soon for fresh content!</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('blog.index') }}" class="btn btn-gradient btn-lg px-5 shadow-sm rounded-pill">
                            <i class="bi bi-grid me-2"></i> Browse All Articles
                        </a>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-top-widget d-flex flex-column gap-4">
                        <!-- Search Box Widget -->
                        <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                            <h5 class="fw-bold mb-3"><i class="bi bi-search me-2 text-primary"></i> Search Blog</h5>
                            <form action="{{ route('blog.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control rounded-start-pill ps-3" placeholder="Search keywords...">
                                    <button class="btn btn-gradient rounded-end-pill px-3" type="submit"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <!-- Popular Tags Widget -->
                        @if($popularTags->count() > 0)
                            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                                <h5 class="fw-bold mb-3"><i class="bi bi-tags me-2 text-primary"></i> Popular Tags</h5>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($popularTags as $tag)
                                        <a href="{{ route('blog.tag', $tag->slug) }}" class="tag-pill">
                                            #{{ $tag->name }} <span class="badge bg-secondary ms-1 rounded-pill">{{ $tag->published_posts_count }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Sidebar Ad Slot -->
                        @include('components.ad-slot', ['slotName' => 'sidebar', 'label' => 'Sidebar Display Ad (300x250)', 'minHeight' => '250px', 'class' => 'my-0'])

                        <!-- Newsletter CTA Card -->
                        <div class="card card-newsletter-gradient border-0 shadow-lg p-4 p-md-5 text-white hover-lift">
                            <div class="position-relative" style="z-index: 1;">
                                <i class="bi bi-envelope-paper-heart fs-1 mb-2 d-inline-block"></i>
                                <h4 class="fw-bold">Stay Updated</h4>
                                <p class="small opacity-75">Get high-quality tutorials and engineering insights delivered straight to your inbox.</p>
                                <form action="{{ route('newsletter.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="email" name="email" class="form-control rounded-pill border-0 px-3 py-2" placeholder="Your email address" required>
                                    </div>
                                    <button type="submit" class="btn btn-light text-primary fw-bold w-100 rounded-pill py-2 shadow-sm">
                                        <i class="bi bi-send-fill me-1"></i> Join Newsletter
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


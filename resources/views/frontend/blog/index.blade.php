@extends('layouts.app')

@section('title', 'All Articles - ' . config('app.name', 'Laravel 12 Blog'))
@section('canonical_url', route('blog.index'))

@section('content')
    <div class="py-5 hero-banner mb-5">
        <div class="container position-relative" style="z-index: 1;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Articles</li>
                </ol>
            </nav>
            <h1 class="fw-bold mb-1 display-5 text-gradient">Explore Articles</h1>
            <p class="text-body-secondary mb-0 fs-5">Browse through technical tutorials, system design breakdowns, and developer guides.</p>
        </div>
    </div>

    <div class="container">
        <!-- Filter & Search Bar -->
        <div class="card border-0 shadow-sm p-4 mb-5 rounded-4 bg-body hover-lift">
            <form action="{{ route('blog.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-lg-5 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary rounded-start-pill ps-3"><i class="bi bi-search text-primary"></i></span>
                        <input type="text" name="search" class="form-control rounded-end-pill" placeholder="Search by title or topic..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-6">
                    <select name="category" class="form-select rounded-pill" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->published_posts_count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <select name="sort" class="form-select rounded-pill" onchange="this.form.submit()">
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Viewed</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-gradient flex-grow-1 rounded-pill">Filter</button>
                    @if(request()->hasAny(['search', 'category', 'tag', 'author', 'sort']))
                        <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary rounded-circle" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </form>
        </div>

        <div class="row g-5">
            <!-- Articles Grid -->
            <div class="col-lg-8">
                @if(request('search'))
                    <div class="mb-4">
                        <span class="text-body-secondary">Showing results for:</span>
                        <span class="fw-bold">"{{ request('search') }}"</span>
                        <span class="badge bg-primary rounded-pill ms-2">{{ $posts->total() }} found</span>
                    </div>
                @endif

                <div class="row g-4">
                    @forelse($posts as $post)
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

                        @if($loop->iteration === 2 || ($loop->iteration % 4 === 0 && !$loop->last))
                            <div class="col-12">
                                @include('components.ad-slot', ['slotName' => 'feed', 'label' => 'In-Feed Native Ad (Responsive)', 'minHeight' => '100px'])
                            </div>
                        @endif
                    @empty
                        <div class="col-12">
                            <div class="p-5 text-center bg-body rounded-4 shadow-sm border-0">
                                <i class="bi bi-search fs-1 text-muted"></i>
                                <h4 class="mt-3 fw-bold">No articles found</h4>
                                <p class="text-muted">Try adjusting your keyword search or category filters.</p>
                                <a href="{{ route('blog.index') }}" class="btn btn-gradient rounded-pill px-4">Clear All Filters</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($posts->hasPages())
                    <div class="mt-5 d-flex justify-content-center">
                        {{ $posts->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-top-widget d-flex flex-column gap-4">
                    <!-- Categories Widget -->
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                        <h5 class="fw-bold mb-3"><i class="bi bi-folder2-open me-2 text-primary"></i> Categories</h5>
                        <ul class="list-group list-group-flush">
                            @foreach($categories as $cat)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                    <a href="{{ route('blog.category', $cat->slug) }}" class="text-decoration-none text-body fw-semibold d-flex align-items-center">
                                        <span class="d-inline-block rounded-circle me-2" style="width: 10px; height: 10px; background-color: {{ $cat->color }};"></span>
                                        {{ $cat->name }}
                                    </a>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $cat->published_posts_count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Sidebar Ad Slot -->
                    @include('components.ad-slot', ['slotName' => 'sidebar', 'label' => 'Sidebar Display Ad (300x250)', 'minHeight' => '250px', 'class' => 'my-0'])

                    <!-- Popular Tags Widget -->
                    @if($tags->count() > 0)
                        <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                            <h5 class="fw-bold mb-3"><i class="bi bi-tags me-2 text-primary"></i> Popular Tags</h5>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($tags as $tag)
                                    <a href="{{ route('blog.tag', $tag->slug) }}" class="tag-pill">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Newsletter Card -->
                    <div class="card card-newsletter-gradient border-0 shadow-lg p-4 p-md-5 text-white hover-lift">
                        <div class="position-relative" style="z-index: 1;">
                            <i class="bi bi-envelope-paper-heart fs-1 mb-2 d-inline-block"></i>
                            <h4 class="fw-bold">Newsletter</h4>
                            <p class="small opacity-75">Subscribe to get the newest guides and articles right in your inbox.</p>
                            <form action="{{ route('newsletter.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <input type="email" name="email" class="form-control rounded-pill border-0 px-3 py-2" placeholder="Your email address" required>
                                </div>
                                <button type="submit" class="btn btn-light text-primary fw-bold w-100 rounded-pill py-2 shadow-sm">
                                    <i class="bi bi-send-fill me-1"></i> Subscribe
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

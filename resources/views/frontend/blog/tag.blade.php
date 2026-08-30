@extends('layouts.app')

@section('title', '#' . $tag->name . ' - Articles')
@section('canonical_url', route('blog.tag', $tag->slug))

@section('content')
    <div class="py-5 hero-banner mb-5">
        <div class="container position-relative" style="z-index: 1;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('blog.index') }}" class="text-decoration-none">Articles</a></li>
                    <li class="breadcrumb-item active" aria-current="page">#{{ $tag->name }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle text-white shadow-sm" style="width: 56px; height: 56px; background: var(--gradient-primary);">
                    <i class="bi bi-hash fs-2"></i>
                </div>
                <div>
                    <h1 class="fw-bold mb-1 display-5"><span class="text-gradient">#{{ $tag->name }}</span></h1>
                    <p class="text-body-secondary mb-0 fs-5">Explore all articles tagged under #{{ $tag->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="row g-4">
                    @forelse($posts as $post)
                        <div class="col-md-6">
                            <div class="card card-post spotlight-card h-100 shadow-sm border-0">
                                <div class="post-thumb-container">
                                    <img src="{{ $post->image_url }}" class="card-img-top post-thumb" alt="{{ $post->title }}">
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
                            <div class="p-5 text-center bg-body rounded-4 shadow-sm border-0">
                                <i class="bi bi-tag fs-1 text-muted"></i>
                                <h4 class="mt-3 fw-bold">No articles found with this tag</h4>
                                <a href="{{ route('blog.index') }}" class="btn btn-gradient rounded-pill px-4 mt-2">Browse All Articles</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($posts->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $posts->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-top-widget d-flex flex-column gap-4">
                    <!-- Tags Widget -->
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                        <h5 class="fw-bold mb-3"><i class="bi bi-tags me-2 text-primary"></i> Popular Tags</h5>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($tags as $t)
                                <a href="{{ route('blog.tag', $t->slug) }}" class="tag-pill {{ $t->id === $tag->id ? 'bg-primary text-white shadow-sm' : '' }}">
                                    #{{ $t->name }} <span class="badge bg-secondary ms-1 rounded-pill">{{ $t->published_posts_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Popular Reads -->
                    @if($popularPosts->count() > 0)
                        <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                            <h5 class="fw-bold mb-3"><i class="bi bi-fire me-2 text-gradient-fire"></i> Popular Reads</h5>
                            <div class="d-flex flex-column gap-3">
                                @foreach($popularPosts as $popPost)
                                    <div class="d-flex gap-3 align-items-center">
                                        <img src="{{ $popPost->image_url }}" alt="{{ $popPost->title }}" class="rounded-3 object-fit-cover" style="width: 70px; height: 60px;">
                                        <div>
                                            <h6 class="mb-1 fw-semibold small">
                                                <a href="{{ route('blog.show', $popPost->slug) }}" class="text-decoration-none text-body">
                                                    {{ Str::limit($popPost->title, 45) }}
                                                </a>
                                            </h6>
                                            <div class="text-muted small">
                                                <i class="bi bi-eye me-1 text-primary"></i>{{ number_format($popPost->views_count) }} views
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

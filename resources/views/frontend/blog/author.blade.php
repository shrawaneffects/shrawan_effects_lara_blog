@extends('layouts.app')

@section('title', 'Author: ' . $author->name)
@section('canonical_url', route('blog.author', $author->id))

@section('content')
    <div class="py-5 hero-banner mb-5">
        <div class="container position-relative" style="z-index: 1;">
            <div class="row align-items-center g-4">
                <div class="col-auto">
                    <img src="{{ $author->avatar_url }}" alt="{{ $author->name }}" class="author-avatar-lg shadow-lg">
                </div>
                <div class="col">
                    <span class="badge bg-primary mb-1 text-uppercase rounded-pill px-3">{{ $author->role }}</span>
                    <h1 class="fw-bold mb-1 display-5">{{ $author->name }}</h1>
                    <p class="text-body-secondary mb-0 fs-5 max-w-700">{{ $author->bio ?? 'Tech writer & software engineer.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <h4 class="fw-bold mb-4">
                    <i class="bi bi-journal-bookmark-fill text-gradient me-2"></i>
                    <span>Articles by <span class="text-gradient">{{ $author->name }}</span> ({{ $posts->total() }})</span>
                </h4>

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
                                        <small class="text-body-secondary">{{ $post->published_at ? $post->published_at->format('M d, Y') : 'Draft' }}</small>
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
                                <h5 class="text-muted">No articles published by this author yet.</h5>
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
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                        <h5 class="fw-bold mb-3"><i class="bi bi-folder2 me-2 text-primary"></i> Categories</h5>
                        <ul class="list-group list-group-flush">
                            @foreach($categories as $cat)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                    <a href="{{ route('blog.category', $cat->slug) }}" class="text-decoration-none text-body fw-semibold">
                                        {{ $cat->name }}
                                    </a>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $cat->published_posts_count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

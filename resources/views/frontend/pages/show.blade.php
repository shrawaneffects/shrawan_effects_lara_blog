@extends('layouts.app')

@section('title', ($page->meta_title ?: $page->title) . ' - ' . config('app.name', 'Laravel 12 Blog'))
@section('meta_description', $page->meta_description ?: Str::limit(strip_tags($page->content), 160))
@section('meta_keywords', $page->meta_keywords ?? '')
@section('canonical_url', route('page.show', $page->slug))

@section('content')
    <!-- Hero Banner -->
    <div class="py-5 hero-banner mb-5">
        <div class="container position-relative" style="z-index: 1;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page->title }}</li>
                </ol>
            </nav>
            <h1 class="fw-bold mb-1 display-5 text-gradient">{{ $page->title }}</h1>
            <p class="text-body-secondary mb-0 small">
                Last updated on {{ $page->updated_at->format('F d, Y') }}
            </p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-body hover-lift">
                    @if($page->image_url)
                        <div class="mb-4 text-center">
                            <img src="{{ $page->image_url }}" alt="{{ $page->title }}" class="img-fluid rounded-4 shadow-sm w-100 object-fit-cover" style="max-height: 420px;">
                        </div>
                    @endif

                    <!-- Page Body Content -->
                    <div class="page-content lead-relaxed fs-6 text-body lh-lg">
                        {!! $page->content !!}
                    </div>

                    <hr class="my-5 opacity-25">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="small text-muted">
                            <i class="bi bi-clock-history me-1"></i> Published on {{ $page->created_at->format('M d, Y') }}
                        </div>
                        <div>
                            <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill px-4 btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Return to Homepage
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

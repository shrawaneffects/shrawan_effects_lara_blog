@extends('layouts.app')

@section('title', '404 - Page Not Found | ' . \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects')))

@section('content')
    <div class="container py-5 my-md-5">
        <div class="row justify-content-center text-center">
            <div class="col-lg-6 col-md-8">
                <!-- 404 Hero Illustration / Badge -->
                <div class="mb-4">
                    <span class="display-1 fw-bold text-gradient font-cyber" style="font-size: 7rem; line-height: 1;">404</span>
                </div>
                
                <h2 class="fw-bold mb-3 font-cyber">COORDINATES NOT FOUND</h2>
                <p class="text-body-secondary mb-4 leading-relaxed font-mono">
                    The requested data sector or cyber transmission cannot be located on the current server node.
                </p>

                <!-- In-page Search Box -->
                <form action="{{ route('blog.index') }}" method="GET" class="mb-4">
                    <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden" style="border: 1px solid rgba(0,255,102,0.25);">
                        <span class="input-group-text border-0 ps-4" style="background: rgba(33,54,8,0.3); color: #00ff66;"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 font-mono" placeholder="Query datastream by keyword..." style="background: rgba(33,54,8,0.15); font-size: 0.95rem;">
                        <button class="btn btn-gradient px-4" type="submit">SEARCH</button>
                    </div>
                </form>

                <!-- Quick Action Buttons -->
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="{{ route('home') }}" class="btn btn-gradient rounded-pill px-4 py-2 shadow-sm">
                        <i class="bi bi-house-door me-1"></i> Return to Terminal
                    </a>
                    <a href="{{ route('blog.index') }}" class="btn btn-tactical rounded-pill px-4 py-2">
                        <i class="bi bi-journal-text me-1"></i> Browse Dispatches
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

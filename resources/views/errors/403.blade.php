@extends('layouts.app')

@section('title', '403 - Access Forbidden | ' . \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects')))

@section('content')
    <div class="container py-5 my-md-5">
        <div class="row justify-content-center text-center">
            <div class="col-lg-6 col-md-8">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center text-warning rounded-circle p-4 bg-warning-subtle shadow-sm mb-2">
                        <i class="bi bi-shield-lock-fill display-3"></i>
                    </div>
                    <div>
                        <span class="display-3 fw-bold text-gradient font-cyber">403</span>
                    </div>
                </div>

                <h2 class="fw-bold mb-3 font-cyber">ACCESS FORBIDDEN // RESTRICTED</h2>
                <p class="text-body-secondary mb-4 leading-relaxed font-mono">
                    Security clearance insufficient to access this encrypted node. Authentication required.
                </p>

                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="{{ route('login') }}" class="btn btn-gradient rounded-pill px-4 py-2 shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Authorize Terminal
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-tactical rounded-pill px-4 py-2">
                        <i class="bi bi-house-door me-1"></i> Return Home
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

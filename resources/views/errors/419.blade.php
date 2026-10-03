@extends('layouts.app')

@section('title', '419 - Session Expired | ' . \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects')))

@section('content')
    <div class="container py-5 my-md-5">
        <div class="row justify-content-center text-center">
            <div class="col-lg-6 col-md-8">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center text-info rounded-circle p-4 bg-info-subtle shadow-sm mb-2">
                        <i class="bi bi-clock-history display-3"></i>
                    </div>
                    <div>
                        <span class="display-3 fw-bold text-gradient font-cyber">419</span>
                    </div>
                </div>

                <h2 class="fw-bold mb-3 font-cyber">TOKEN PROTOCOL EXPIRED</h2>
                <p class="text-body-secondary mb-4 leading-relaxed font-mono">
                    Session encryption handshake has timed out. Re-establish quantum tunnel to continue.
                </p>

                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <button onclick="window.location.reload();" class="btn btn-gradient rounded-pill px-4 py-2 shadow-sm">
                        <i class="bi bi-arrow-clockwise me-1"></i> Re-Synchronize Session
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-tactical rounded-pill px-4 py-2">
                        <i class="bi bi-house-door me-1"></i> Return Home
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

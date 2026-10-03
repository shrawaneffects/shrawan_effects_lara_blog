@extends('layouts.app')

@section('title', '500 - Server Error | ' . \App\Models\Setting::get('site_name', config('app.name', 'Shrawan Effects')))

@section('content')
    <div class="container py-5 my-md-5">
        <div class="row justify-content-center text-center">
            <div class="col-lg-6 col-md-8">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center text-danger rounded-circle p-4 bg-danger-subtle shadow-sm mb-2">
                        <i class="bi bi-tools display-3"></i>
                    </div>
                    <div>
                        <span class="display-3 fw-bold text-gradient font-cyber">500</span>
                    </div>
                </div>

                <h2 class="fw-bold mb-3 font-cyber">System Transmission Disrupted</h2>
                <p class="text-body-secondary mb-4 leading-relaxed font-mono">
                    An unexpected kernel exception occurred while processing this telemetry packet. Core diagnostic telemetry has been logged.
                </p>

                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="{{ route('home') }}" class="btn btn-gradient rounded-pill px-4 py-2 shadow-sm">
                        <i class="bi bi-house-door me-1"></i> Return to Terminal
                    </a>
                    <a href="{{ route('contact.index') }}" class="btn btn-tactical rounded-pill px-4 py-2">
                        <i class="bi bi-envelope me-1"></i> Contact Command
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

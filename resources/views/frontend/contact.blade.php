@extends('layouts.app')

@section('title', 'Contact Us - ' . config('app.name', 'Laravel 12 Blog'))
@section('canonical_url', route('contact.index'))

@section('content')
    <div class="py-5 hero-banner mb-5">
        <div class="container position-relative" style="z-index: 1;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact</li>
                </ol>
            </nav>
            <h1 class="fw-bold mb-1 display-5">Get in <span class="text-gradient">Touch</span></h1>
            <p class="text-body-secondary mb-0 fs-5">Have questions, feedback, or want to contribute as a writer? Drop us a message.</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row g-5">
            <!-- Contact Info Cards -->
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4">
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-4 shadow-sm" style="width: 50px; height: 50px;">
                                <i class="bi bi-geo-alt-fill fs-4 text-gradient"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Our Location</h6>
                                <p class="text-muted small mb-0">San Francisco, California, USA</p>
                            </div>
                        </div>
                        <p class="text-body-secondary small mb-0">Open source collaboration hub with global contributors.</p>
                    </div>

                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-4 shadow-sm" style="width: 50px; height: 50px;">
                                <i class="bi bi-envelope-fill fs-4 text-gradient-emerald"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Email Inquiries</h6>
                                <p class="text-muted small mb-0">{{ \App\Models\Setting::get('contact_email', 'contact@laravelblog.example') }}</p>
                            </div>
                        </div>
                        <p class="text-body-secondary small mb-0">We typically respond to editorial and partnership inquiries within 24-48 business hours.</p>
                    </div>

                    <div class="card card-newsletter-gradient border-0 shadow-lg p-4 p-md-5 rounded-4 text-white hover-lift">
                        <div class="position-relative" style="z-index: 1;">
                            <h4 class="fw-bold mb-2"><i class="bi bi-pencil-square me-2"></i> Want to Write with Us?</h4>
                            <p class="small opacity-75 mb-4">We welcome guest authors who want to publish quality technical guides on Laravel, PHP, and modern web architectures.</p>
                            <a href="{{ route('register') }}" class="btn btn-light text-primary fw-bold rounded-pill px-4 shadow-sm">
                                <i class="bi bi-person-plus-fill me-1"></i> Register as Author
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-body hover-lift">
                    <h3 class="fw-bold mb-4">
                        <i class="bi bi-chat-dots-fill text-gradient me-2"></i>
                        <span>Send Us a <span class="text-gradient">Message</span></span>
                    </h3>

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Full Name *</label>
                                <input type="text" name="name" class="form-control rounded-pill px-3 py-2" placeholder="John Doe" required value="{{ old('name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Email Address *</label>
                                <input type="email" name="email" class="form-control rounded-pill px-3 py-2" placeholder="john@example.com" required value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Subject</label>
                            <input type="text" name="subject" class="form-control rounded-pill px-3 py-2" placeholder="Article contribution / General inquiry" value="{{ old('subject') }}">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Your Message *</label>
                            <textarea name="message" class="form-control rounded-4 p-3" rows="5" placeholder="How can we assist you?" required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-gradient btn-lg px-5 rounded-pill shadow-sm">
                            <i class="bi bi-send-fill me-2"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

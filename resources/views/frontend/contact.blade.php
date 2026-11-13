@extends('layouts.app')

@section('title', 'Contact Us - Shrawan Effects | Fullstack Website & Software Development in Faridabad')
@section('meta_description', 'Get in touch with Shrawan Effects, the premier Fullstack Website & Software Development Service Provider based in Faridabad, Haryana. Custom web applications, APIs, UI/UX, and cloud engineering.')
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
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="telemetry-tag"><i class="bi bi-geo-alt"></i> FARIDABAD, HARYANA // STUDIO HQ</span>
            </div>
            <h1 class="fw-bold mb-2 display-5 font-cyber">Contact <span class="text-gradient">Shrawan Effects</span></h1>
            <p class="text-body-secondary mb-0 fs-5 leading-relaxed" style="max-width: 780px;">
                Fullstack Website & Software Development Service Provider in Faridabad. Let's discuss your project blueprints, digital transformation, or custom cloud applications.
            </p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row g-5">
            <!-- Contact Info & Business Profile -->
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4">
                    <!-- Location Card -->
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-4 shadow-sm flex-shrink-0" style="width: 50px; height: 50px;">
                                <i class="bi bi-geo-alt-fill fs-4 text-gradient"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 font-cyber">Development Studio & Office</h6>
                                <p class="text-body fw-semibold mb-1">
                                    Shrawan Effects
                                </p>
                                <p class="text-body-secondary small mb-2 leading-relaxed">
                                    House No. 1049, Jeevan Nagar, Gounchhi,<br>
                                    FARIDABAD, Haryana 121004, India
                                </p>
                                <a href="https://maps.google.com/?q=House+No.+1049,+Jeevan+Nagar,+Gounchhi,+FARIDABAD,+Haryana+121004" target="_blank" class="btn btn-sm btn-tactical rounded-pill px-3">
                                    <i class="bi bi-map me-1"></i> Get Directions
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Email & Communication Card -->
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-4 shadow-sm flex-shrink-0" style="width: 50px; height: 50px;">
                                <i class="bi bi-envelope-fill fs-4 text-gradient-emerald"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 font-cyber">Direct Transmission</h6>
                                <p class="text-muted small mb-1">General & Project Inquiries:</p>
                                <a href="mailto:{{ \App\Models\Setting::get('contact_email', 'shrawaneffects@gmail.com') }}" class="fw-bold text-success text-decoration-none font-mono">
                                    {{ \App\Models\Setting::get('contact_email', 'shrawaneffects@gmail.com') }}
                                </a>
                            </div>
                        </div>
                        <p class="text-body-secondary small mb-0 font-mono">
                            <i class="bi bi-clock-history me-1 text-success"></i> Response Protocol: Under 24 Business Hours
                        </p>
                    </div>

                    <!-- Services Spotlight Card -->
                    <div class="card card-newsletter-gradient border-0 shadow-lg p-4 p-md-4 rounded-4 text-white hover-lift">
                        <div class="position-relative" style="z-index: 1;">
                            <span class="badge bg-white text-dark font-mono mb-2">SERVICES SUITE</span>
                            <h4 class="fw-bold mb-2 font-cyber">Fullstack Solutions</h4>
                            <p class="small text-white-50 mb-3">
                                Custom Websites, Enterprise Software, E-Commerce, Mobile Apps, REST APIs, and Cloud Infrastructure.
                            </p>
                            <a href="{{ route('services.index') }}" class="btn btn-gradient rounded-pill px-4 shadow-sm w-100">
                                <i class="bi bi-briefcase me-1"></i> View All Services
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-body hover-lift">
                    <h3 class="fw-bold mb-2 font-cyber">
                        <i class="bi bi-chat-dots-fill text-gradient me-2"></i>
                        <span>Transmit a <span class="text-gradient">Message</span></span>
                    </h3>
                    <p class="text-body-secondary small mb-4">
                        Send us your technical requirements, project scope, or general inquiries. Our lead architects will get back to you promptly.
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success d-flex align-items-center rounded-3 mb-4">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Full Name *</label>
                                <input type="text" name="name" class="form-control rounded-pill px-3 py-2" placeholder="e.g. Rahul Sharma" required value="{{ old('name') }}">
                                @error('name') <small class="text-danger font-mono">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Email Address *</label>
                                <input type="email" name="email" class="form-control rounded-pill px-3 py-2" placeholder="name@domain.com" required value="{{ old('email') }}">
                                @error('email') <small class="text-danger font-mono">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Phone / WhatsApp (Optional)</label>
                                <input type="text" name="phone" class="form-control rounded-pill px-3 py-2 font-mono" placeholder="+91 98765 43210" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Service Interested In</label>
                                <select name="service" class="form-select rounded-pill px-3 py-2">
                                    <option value="">Select a Service...</option>
                                    <option value="Fullstack Website Development">Fullstack Website Development</option>
                                    <option value="Custom Software & SaaS Development">Custom Software & SaaS Development</option>
                                    <option value="E-Commerce Solutions">E-Commerce Solutions</option>
                                    <option value="Mobile App Development">Mobile App Development</option>
                                    <option value="API & Backend Integration">API & Backend Integration</option>
                                    <option value="UI/UX Design & Prototyping">UI/UX Design & Prototyping</option>
                                    <option value="Cloud Deployment & DevOps">Cloud Deployment & DevOps</option>
                                    <option value="SEO & Performance Optimization">SEO & Performance Optimization</option>
                                    <option value="Other">Other / General Consultation</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Subject *</label>
                            <input type="text" name="subject" class="form-control rounded-pill px-3 py-2" placeholder="Project blueprint / Consultation inquiry" required value="{{ old('subject') }}">
                            @error('subject') <small class="text-danger font-mono">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Project Scope & Message *</label>
                            <textarea name="message" class="form-control rounded-4 p-3" rows="5" placeholder="Tell us about your project timeline, business objectives, and requirements..." required>{{ old('message') }}</textarea>
                            @error('message') <small class="text-danger font-mono">{{ $message }}</small> @enderror
                        </div>

                        <button type="submit" class="btn btn-gradient btn-lg px-5 rounded-pill shadow-sm">
                            <i class="bi bi-send-fill me-2"></i> INITIATE TRANSMISSION
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

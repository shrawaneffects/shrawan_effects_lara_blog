@extends('layouts.app')

@section('title', 'Services - Shrawan Effects | Fullstack Website & Software Development in Faridabad')
@section('meta_description', 'Discover professional fullstack website development, custom SaaS software, e-commerce stores, REST APIs, and SEO services offered by Shrawan Effects based in Faridabad, Haryana.')
@section('canonical_url', route('services.index'))

@section('content')
    <!-- Hero Banner -->
    <div class="py-5 hero-banner mb-5">
        <div class="container position-relative" style="z-index: 1;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 font-mono">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Services</li>
                </ol>
            </nav>

            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="telemetry-tag"><i class="bi bi-briefcase"></i> SHRAWAN EFFECTS // SERVICES SUITE</span>
            </div>

            <h1 class="fw-bold mb-3 display-4 font-cyber">
                Engineered for <span class="text-gradient">Performance & Scale</span>
            </h1>
            <p class="text-body-secondary fs-5 leading-relaxed mb-4" style="max-width: 820px;">
                Fullstack website and software development service provider in Faridabad, Haryana. We build high-throughput Laravel web applications, enterprise software architectures, robust REST APIs, and digital storefronts that accelerate business growth.
            </p>

            <div class="d-flex flex-wrap gap-3 align-items-center">
                <a href="#all-services" class="btn btn-gradient rounded-pill px-4 py-2.5">
                    <i class="bi bi-grid-3x3-gap-fill me-2"></i> Explore Services
                </a>
                <a href="{{ route('contact.index') }}" class="btn btn-tactical rounded-pill px-4 py-2.5">
                    <i class="bi bi-chat-square-dots me-2"></i> Request Consultation
                </a>
                <a href="tel:+919555277347" class="btn btn-outline-secondary rounded-pill px-3 py-2.5 font-mono small d-none d-sm-inline-flex align-items-center">
                    <i class="bi bi-telephone text-success me-2"></i> +91 95552 77347
                </a>
            </div>
        </div>
    </div>

    <!-- Featured Services Grid -->
    <div class="container mb-5" id="all-services">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 border-bottom pb-3">
            <div>
                <span class="telemetry-tag mb-1"><i class="bi bi-cpu"></i> CORE CAPABILITIES</span>
                <h3 class="fw-bold mb-0 font-cyber">Production-Grade <span class="text-gradient">Solutions</span></h3>
            </div>
            <p class="text-body-secondary small mb-0 font-mono">
                {{ $services->count() }} SPECIALIZED SERVICES AVAILABLE
            </p>
        </div>

        <div class="row g-4">
            @foreach($services as $service)
                <div class="col-lg-4 col-md-6">
                    <div class="card card-post spotlight-card h-100 p-4 rounded-4 shadow-sm d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary" style="width: 52px; height: 52px;">
                                <i class="bi {{ $service->icon ?: 'bi-briefcase' }} fs-3 text-gradient"></i>
                            </div>
                            @if($service->pricing_starts_at)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 font-mono small">
                                    {{ $service->pricing_starts_at }}
                                </span>
                            @endif
                        </div>

                        <h4 class="fw-bold mb-2 font-cyber">
                            <a href="{{ route('services.show', $service->slug) }}" class="text-decoration-none text-body stretched-link">
                                {{ $service->title }}
                            </a>
                        </h4>

                        <p class="text-body-secondary small mb-3 leading-relaxed flex-grow-1">
                            {{ $service->short_description }}
                        </p>

                        @if(!empty($service->features) && is_array($service->features))
                            <ul class="list-unstyled small mb-4 text-body-secondary font-mono">
                                @foreach(array_slice($service->features, 0, 3) as $feat)
                                    <li class="mb-1 d-flex align-items-start gap-2">
                                        <i class="bi bi-check2 text-success flex-shrink-0 mt-0.5"></i>
                                        <span class="text-truncate">{{ $feat }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between position-relative" style="z-index: 2;">
                            <span class="small font-mono text-muted text-truncate" style="max-width: 180px;">
                                {{ $service->technologies ?: 'Laravel, PHP, Cloud' }}
                            </span>
                            <a href="{{ route('services.show', $service->slug) }}" class="btn btn-sm btn-tactical rounded-pill px-3">
                                Scope <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Why Partner With Shrawan Effects -->
    <div class="container mb-5">
        <div class="card card-newsletter-gradient border-0 shadow-lg p-4 p-md-5 rounded-4 text-white">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <span class="badge bg-white text-dark font-mono mb-2">LOCAL EXPERTISE // NCR REGION</span>
                    <h2 class="fw-bold font-cyber mb-3">Why Partner With Shrawan Effects in Faridabad?</h2>
                    <p class="small text-white-75 mb-4 leading-relaxed">
                        We blend high-level full-stack software architecture with local hands-on communication. Operating from Faridabad (Haryana), we work closely with startups, manufacturing industries, e-commerce brands, and global clients to engineer digital products that last.
                    </p>
                    <div class="row g-3 small font-mono">
                        <div class="col-sm-6 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-success fs-5"></i>
                            <span>100% PSR & Clean Code Standards</span>
                        </div>
                        <div class="col-sm-6 d-flex align-items-center gap-2">
                            <i class="bi bi-lightning-charge text-success fs-5"></i>
                            <span>High-Performance Core Web Vitals</span>
                        </div>
                        <div class="col-sm-6 d-flex align-items-center gap-2">
                            <i class="bi bi-lock text-success fs-5"></i>
                            <span>Enterprise Security & Role-Based Control</span>
                        </div>
                        <div class="col-sm-6 d-flex align-items-center gap-2">
                            <i class="bi bi-headset text-success fs-5"></i>
                            <span>Direct Architect Access & Fast Support</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 text-center text-lg-end">
                    <div class="p-4 rounded-4" style="background: rgba(5,8,4,0.4); border: 1px solid rgba(0,255,102,0.3);">
                        <i class="bi bi-geo-alt-fill text-success fs-2 mb-2 d-inline-block"></i>
                        <h5 class="fw-bold font-cyber mb-1">Faridabad Headquarters</h5>
                        <p class="small text-white-50 mb-3 font-mono">
                            House No. 1049, Jeevan Nagar, Gounchhi,<br>FARIDABAD, Haryana 121004
                        </p>
                        <a href="{{ route('contact.index') }}" class="btn btn-gradient rounded-pill px-4 py-2.5 w-100">
                            <i class="bi bi-send me-1"></i> Start Your Project
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

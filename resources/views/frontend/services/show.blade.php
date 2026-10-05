@extends('layouts.app')

@section('title', $service->title . ' - Shrawan Effects | Faridabad')
@section('meta_description', Str::limit($service->short_description, 160))
@section('canonical_url', route('services.show', $service->slug))

@section('content')
    <!-- Service Header Banner -->
    <div class="py-5 hero-banner mb-5">
        <div class="container position-relative" style="z-index: 1;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 font-mono">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('services.index') }}" class="text-decoration-none">Services</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $service->title }}</li>
                </ol>
            </nav>

            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <span class="telemetry-tag"><i class="bi bi-cpu"></i> SERVICE SPECIFICATION</span>
                @if($service->pricing_starts_at)
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 font-mono">
                        <i class="bi bi-tag-fill me-1"></i> {{ $service->pricing_starts_at }}
                    </span>
                @endif
            </div>

            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-4 shadow-sm" style="width: 60px; height: 60px;">
                    <i class="bi {{ $service->icon ?: 'bi-briefcase' }} fs-2 text-gradient"></i>
                </div>
                <h1 class="fw-bold mb-0 display-5 font-cyber">{{ $service->title }}</h1>
            </div>

            <p class="text-body-secondary fs-5 leading-relaxed" style="max-width: 800px;">
                {{ $service->short_description }}
            </p>
        </div>
    </div>

    <!-- Main Content & Sidebar -->
    <div class="container mb-5">
        <div class="row g-5">
            <!-- Left Main Column -->
            <div class="col-lg-8">
                <!-- Cover Image if present -->
                @if($service->image)
                    <div class="mb-5 rounded-4 overflow-hidden shadow-lg border">
                        <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="img-fluid w-100 object-fit-cover" style="max-height: 420px;">
                    </div>
                @endif

                <!-- Detailed Scope & Description -->
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-body mb-5">
                    <h3 class="fw-bold mb-4 font-cyber border-bottom pb-3">
                        <i class="bi bi-file-earmark-text text-gradient me-2"></i>
                        Scope & Architecture Overview
                    </h3>

                    <div class="post-content mb-4 leading-relaxed">
                        {!! $service->content ?: '<p>' . e($service->short_description) . '</p>' !!}
                    </div>

                    <!-- Deliverables Checklist -->
                    @if(!empty($service->features) && is_array($service->features))
                        <div class="mt-4 pt-4 border-top">
                            <h4 class="fw-bold mb-3 font-cyber">Included Deliverables</h4>
                            <div class="row g-3">
                                @foreach($service->features as $feature)
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 border d-flex align-items-start gap-2 bg-body-tertiary">
                                            <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0 mt-0.5"></i>
                                            <span class="small fw-semibold">{{ $feature }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Technologies Used -->
                    @if($service->technologies)
                        <div class="mt-5 pt-4 border-top">
                            <h5 class="fw-bold mb-3 font-cyber">Core Technology Stack</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(explode(',', $service->technologies) as $tech)
                                    <span class="tag-pill font-mono">
                                        <i class="bi bi-terminal me-1"></i> {{ trim($tech) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <div class="col-lg-4">
                <div class="sticky-top-widget d-flex flex-column gap-4">
                    <!-- Project Inquiry Card -->
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift" style="border: 1px solid rgba(0,255,102,0.2) !important;">
                        <span class="telemetry-tag mb-2"><i class="bi bi-lightning-charge"></i> FAST ENGAGEMENT</span>
                        <h4 class="fw-bold mb-2 font-cyber">Initiate Project Scope</h4>
                        <p class="text-body-secondary small mb-3">
                            Connect directly with our lead architects in Faridabad to plan your software specifications and receive a customized quote.
                        </p>

                        <div class="p-3 rounded-3 mb-3 bg-body-tertiary font-mono small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Service:</span>
                                <span class="fw-bold text-truncate ms-2">{{ $service->title }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Pricing:</span>
                                <span class="fw-bold text-success">{{ $service->pricing_starts_at ?: 'Custom Quote' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Turnaround:</span>
                                <span class="fw-bold">Agile Sprints</span>
                            </div>
                        </div>

                        <a href="{{ route('contact.index') }}?service={{ urlencode($service->title) }}" class="btn btn-gradient w-100 rounded-pill py-2.5 mb-2 shadow-sm">
                            <i class="bi bi-chat-dots-fill me-1"></i> Discuss This Project
                        </a>

                        <a href="https://wa.me/919555277347?text={{ urlencode('Hello Shrawan Effects, I am interested in ' . $service->title . '.') }}" target="_blank" class="btn btn-outline-success w-100 rounded-pill py-2 font-mono small">
                            <i class="bi bi-whatsapp me-1"></i> WhatsApp: +91 95552 77347
                        </a>
                    </div>

                    <!-- Faridabad Studio Location Widget -->
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                        <h5 class="fw-bold mb-2 font-cyber"><i class="bi bi-geo-alt-fill text-success me-2"></i> Local Office</h5>
                        <p class="text-body-secondary small mb-2 font-mono">
                            House No. 1049, Jeevan Nagar, Gounchhi,<br>FARIDABAD, Haryana 121004, India
                        </p>
                        <a href="https://maps.google.com/?q=House+No.+1049,+Jeevan+Nagar,+Gounchhi,+FARIDABAD,+Haryana+121004" target="_blank" class="btn btn-sm btn-tactical rounded-pill">
                            <i class="bi bi-map me-1"></i> Open Google Maps
                        </a>
                    </div>

                    <!-- Related Services -->
                    @if($relatedServices->count() > 0)
                        <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift">
                            <h5 class="fw-bold mb-3 font-cyber">Complementary Services</h5>
                            <div class="d-flex flex-column gap-3">
                                @foreach($relatedServices as $rService)
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-3 flex-shrink-0" style="width: 38px; height: 38px;">
                                            <i class="bi {{ $rService->icon ?: 'bi-briefcase' }}"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0">
                                                <a href="{{ route('services.show', $rService->slug) }}" class="text-decoration-none text-body">
                                                    {{ $rService->title }}
                                                </a>
                                            </h6>
                                            <small class="text-muted font-mono" style="font-size: 0.74rem;">{{ $rService->pricing_starts_at ?: 'Custom' }}</small>
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

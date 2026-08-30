@extends('layouts.app')

@section('title', 'Frequently Asked Questions (FAQ) - ' . config('app.name', 'Laravel 12 Blog'))
@section('canonical_url', route('faq'))

@section('content')
    <div class="py-5 hero-banner mb-5">
        <div class="container position-relative" style="z-index: 1;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">FAQ</li>
                </ol>
            </nav>
            <h1 class="fw-bold mb-1 display-5">Frequently Asked <span class="text-gradient">Questions</span></h1>
            <p class="text-body-secondary mb-0 fs-5">Everything you need to know about publishing, reading, and engaging on our platform.</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                <!-- Platform Overview Accordion -->
                <h4 class="fw-bold mb-3 d-flex align-items-center">
                    <i class="bi bi-journal-bookmark-fill text-gradient me-2 fs-4"></i> General & Reading
                </h4>
                <div class="accordion accordion-flush d-flex flex-column gap-3 mb-5" id="generalFaqAccordion">
                    <div class="accordion-item border rounded-4 bg-body shadow-sm overflow-hidden hover-lift">
                        <h2 class="accordion-header" id="gHeading1">
                            <button class="accordion-button fw-bold text-body bg-transparent p-4" type="button" data-bs-toggle="collapse" data-bs-target="#gCollapse1" aria-expanded="true">
                                <span class="badge bg-primary-subtle text-primary rounded-circle me-3 px-2 py-1 small">Q1</span>
                                How often is new content published?
                            </button>
                        </h2>
                        <div id="gCollapse1" class="accordion-collapse collapse show" data-bs-parent="#generalFaqAccordion">
                            <div class="accordion-body px-4 pb-4 pt-0 text-body-secondary">
                                We publish in-depth technical breakdowns, architectural tutorials, and developer guides multiple times every week across various categories including Laravel, Web Development, and UI/UX Design.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border rounded-4 bg-body shadow-sm overflow-hidden hover-lift">
                        <h2 class="accordion-header" id="gHeading2">
                            <button class="accordion-button collapsed fw-bold text-body bg-transparent p-4" type="button" data-bs-toggle="collapse" data-bs-target="#gCollapse2" aria-expanded="false">
                                <span class="badge bg-primary-subtle text-primary rounded-circle me-3 px-2 py-1 small">Q2</span>
                                Can I bookmark or save articles to read later?
                            </button>
                        </h2>
                        <div id="gCollapse2" class="accordion-collapse collapse" data-bs-parent="#generalFaqAccordion">
                            <div class="accordion-body px-4 pb-4 pt-0 text-body-secondary">
                                Yes! Registered readers can bookmark articles, comment, and receive customized newsletter digests tailored to their favorite topics and tags.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Writing & Authorship Accordion -->
                <h4 class="fw-bold mb-3 d-flex align-items-center">
                    <i class="bi bi-pencil-square text-gradient-fire me-2 fs-4"></i> Authors & Publishing
                </h4>
                <div class="accordion accordion-flush d-flex flex-column gap-3 mb-5" id="authorFaqAccordion">
                    <div class="accordion-item border rounded-4 bg-body shadow-sm overflow-hidden hover-lift">
                        <h2 class="accordion-header" id="aHeading1">
                            <button class="accordion-button collapsed fw-bold text-body bg-transparent p-4" type="button" data-bs-toggle="collapse" data-bs-target="#aCollapse1" aria-expanded="false">
                                <span class="badge bg-danger-subtle text-danger rounded-circle me-3 px-2 py-1 small">Q3</span>
                                How do I apply to become a contributing author?
                            </button>
                        </h2>
                        <div id="aCollapse1" class="accordion-collapse collapse" data-bs-parent="#authorFaqAccordion">
                            <div class="accordion-body px-4 pb-4 pt-0 text-body-secondary">
                                You can apply via our <a href="{{ route('contact.index') }}" class="text-decoration-none fw-semibold">Contact page</a> by submitting a sample draft or linking to your existing technical portfolio.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border rounded-4 bg-body shadow-sm overflow-hidden hover-lift">
                        <h2 class="accordion-header" id="aHeading2">
                            <button class="accordion-button collapsed fw-bold text-body bg-transparent p-4" type="button" data-bs-toggle="collapse" data-bs-target="#aCollapse2" aria-expanded="false">
                                <span class="badge bg-danger-subtle text-danger rounded-circle me-3 px-2 py-1 small">Q4</span>
                                Does the platform support Schema.org rich snippets and Table of Contents?
                            </button>
                        </h2>
                        <div id="aCollapse2" class="accordion-collapse collapse" data-bs-parent="#authorFaqAccordion">
                            <div class="accordion-body px-4 pb-4 pt-0 text-body-secondary">
                                Yes! Every article automatically generates Google-compliant Schema.org JSON-LD structured data and dynamic Table of Contents navigation with smooth scrolling and scroll-spy active state highlighting.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Still have questions card -->
                <div class="card card-newsletter-gradient border-0 shadow-lg p-4 p-md-5 rounded-4 text-white hover-lift text-center">
                    <h3 class="fw-bold mb-2">Still have questions?</h3>
                    <p class="opacity-75 mb-4">Can't find the answer you're looking for? Our team is always here to help.</p>
                    <div>
                        <a href="{{ route('contact.index') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-primary shadow-sm">
                            <i class="bi bi-envelope-heart-fill me-1"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

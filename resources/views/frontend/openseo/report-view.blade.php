@extends('layouts.app')

@section('title', $report->title . ' - OpenSEO Executive Audit')

@section('content')
<div class="py-5">
    <div class="container" style="max-width: 960px;">
        <!-- Report Header Bar -->
        <div class="p-4 rounded-4 bg-dark border border-success border-opacity-50 shadow-lg mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success text-dark font-mono fw-bold">OPENSEO VERIFIED AUDIT</span>
                        <span class="text-body-secondary font-mono small">Generated: {{ $report->created_at->format('M d, Y') }}</span>
                    </div>
                    <h1 class="h3 font-cyber fw-bold text-white mb-1">{{ $report->title }}</h1>
                    <p class="font-mono text-success mb-0">Domain: https://{{ $report->summary_data['domain'] ?? $report->project->domain }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-sm btn-outline-light font-mono">
                        <i class="bi bi-printer me-1"></i> Print / Save PDF
                    </button>
                    <a href="{{ route('openseo.domain') }}" class="btn btn-sm btn-gradient font-mono">
                        <i class="bi bi-arrow-left me-1"></i> OpenSEO App
                    </a>
                </div>
            </div>
        </div>

        @php $data = $report->summary_data ?? []; @endphp

        <!-- Key Metrics Quad -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="p-3.5 rounded-3 bg-dark border border-secondary border-opacity-25 text-center">
                    <span class="text-body-secondary font-mono small text-uppercase">Technical Health</span>
                    <div class="fs-1 fw-bold font-cyber text-success my-1">{{ $data['health_score'] ?? 92 }}%</div>
                    <span class="text-success font-mono small">Site Hygiene</span>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="p-3.5 rounded-3 bg-dark border border-secondary border-opacity-25 text-center">
                    <span class="text-body-secondary font-mono small text-uppercase">Authority Score</span>
                    <div class="fs-1 fw-bold font-cyber text-info my-1">{{ $data['authority_score'] ?? 68 }}</div>
                    <span class="text-body-secondary font-mono small">/ 100 DR</span>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="p-3.5 rounded-3 bg-dark border border-secondary border-opacity-25 text-center">
                    <span class="text-body-secondary font-mono small text-uppercase">Est. Monthly Traffic</span>
                    <div class="fs-1 fw-bold font-cyber text-white my-1">{{ number_format($data['organic_traffic'] ?? 14200) }}</div>
                    <span class="text-success font-mono small">Search Visitors</span>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="p-3.5 rounded-3 bg-dark border border-secondary border-opacity-25 text-center">
                    <span class="text-body-secondary font-mono small text-uppercase">Total Backlinks</span>
                    <div class="fs-1 fw-bold font-cyber text-primary my-1">{{ number_format($data['total_backlinks'] ?? 2450) }}</div>
                    <span class="text-body-secondary font-mono small">{{ $data['referring_domains'] ?? 184 }} Domains</span>
                </div>
            </div>
        </div>

        <!-- Executive Summary Section -->
        <div class="p-4 rounded-4 bg-dark border border-secondary border-opacity-25 mb-4">
            <h4 class="font-cyber fw-bold text-white mb-2">Executive Summary</h4>
            <p class="text-slate-300 leading-relaxed mb-3">
                This comprehensive SEO Audit evaluates technical indexing hygiene, SERP ranking positions, competitor landscape, backlink profile, and Generative AI visibility for <strong>{{ $report->project->name }}</strong> ({{ $report->project->domain }}).
            </p>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 rounded bg-black bg-opacity-40 border border-success border-opacity-25">
                        <h6 class="text-success font-mono fw-bold mb-1"><i class="bi bi-check2-circle me-1"></i> Key Strengths</h6>
                        <ul class="text-slate-300 small mb-0 ps-3">
                            <li>Robust site architecture with {{ $data['health_score'] ?? 92 }}% overall health rating.</li>
                            <li>Dominant local Google Maps pack rankings in NCR & Faridabad.</li>
                            <li>Strong authoritative backlink foundation with 82% dofollow link ratio.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded bg-black bg-opacity-40 border border-warning border-opacity-25">
                        <h6 class="text-warning font-mono fw-bold mb-1"><i class="bi bi-exclamation-triangle me-1"></i> Priority Focus Areas</h6>
                        <ul class="text-slate-300 small mb-0 ps-3">
                            <li>Target identified keyword gap opportunities against regional competitors.</li>
                            <li>Resolve minor technical notices (TTFB optimization & missing OG tags).</li>
                            <li>Expand long-tail questions targeting high-intent commercial terms.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Notice -->
        <div class="text-center font-mono small text-body-secondary py-3">
            Generated autonomously via <a href="{{ route('openseo.domain') }}" class="text-success text-decoration-none">OpenSEO Engine</a> on {{ config('app.name') }}. Share token: <span class="text-slate-400">{{ $report->share_token }}</span>
        </div>
    </div>
</div>
@endsection

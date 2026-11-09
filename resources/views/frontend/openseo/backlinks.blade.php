@extends('frontend.openseo.layouts.openseo')

@section('title', 'Backlinks & Referring Domains - ' . $activeProject->domain . ' - OpenSEO')

@section('openseo_content')
<!-- KPI Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Total Backlinks</span>
            <div class="openseo-stat-num">{{ number_format($summary['total_backlinks']) }}</div>
            <span class="text-body-secondary font-mono small mt-2 d-block">Inbound hyperlinks pointing to site</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Referring Domains</span>
            <div class="openseo-stat-num text-info">{{ number_format($summary['referring_domains']) }}</div>
            <span class="text-info font-mono small mt-2 d-block">Unique websites linking</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Domain Trust Score</span>
            <div class="d-flex align-items-baseline gap-2">
                <div class="openseo-stat-num">{{ $summary['domain_trust_score'] }}</div>
                <div class="text-body-secondary font-mono small">/ 100</div>
            </div>
            <div class="progress mt-3 bg-dark" style="height: 6px;">
                <div class="progress-bar bg-success" style="width: {{ $summary['domain_trust_score'] }}%;"></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Dofollow vs Nofollow</span>
            <div class="d-flex align-items-baseline gap-2">
                <div class="openseo-stat-num text-success">
                    @php
                        $total = ($summary['dofollow_count'] + $summary['nofollow_count']) ?: 1;
                        $dofollowPct = round(($summary['dofollow_count'] / $total) * 100);
                    @endphp
                    {{ $dofollowPct }}%
                </div>
                <div class="text-body-secondary font-mono small">dofollow</div>
            </div>
            <div class="d-flex gap-2 mt-2 font-mono small">
                <span class="text-success">{{ number_format($summary['dofollow_count']) }} Follow</span>
                <span class="text-secondary">{{ number_format($summary['nofollow_count']) }} NoFollow</span>
            </div>
        </div>
    </div>
</div>

<!-- Top Anchor Text Distribution -->
<div class="openseo-hud-card p-3 p-md-4 mb-4">
    <h5 class="font-cyber fw-bold text-white mb-1">Top Anchor Text Distribution</h5>
    <p class="text-body-secondary font-mono small mb-3">Keywords used in anchor links by referring websites</p>

    <div class="row g-2">
        @foreach($anchors as $anchor)
            <div class="col-sm-6 col-lg-3">
                <div class="p-2.5 rounded-3 bg-dark border border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                    <span class="text-white font-mono small text-truncate me-2" title="{{ $anchor->anchor_text }}">
                        "{{ $anchor->anchor_text ?: 'Empty Anchor' }}"
                    </span>
                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 font-mono">
                        {{ $anchor->count }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Inbound Backlinks Table -->
<div class="openseo-hud-card p-3 p-md-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="font-cyber fw-bold text-white mb-0">Inbound Backlinks ({{ $backlinks->total() }})</h5>
            <p class="text-body-secondary font-mono small mb-0">Active backlinks linking to {{ $activeProject->domain }}</p>
        </div>
        <a href="{{ route('openseo.reports.export', 'backlinks') }}" class="btn btn-sm btn-outline-success font-mono">
            <i class="bi bi-download me-1"></i> Export Links CSV
        </a>
    </div>

    <div class="table-responsive">
        <table class="table openseo-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Referring Page (Source)</th>
                    <th>Target Page</th>
                    <th>Anchor Text</th>
                    <th class="text-center">DR</th>
                    <th class="text-center">Type</th>
                    <th class="text-end">First Seen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backlinks as $bl)
                    <tr>
                        <td>
                            <div class="fw-bold text-white text-truncate" style="max-width: 320px;">
                                {{ $bl->source_title ?: $bl->source_url }}
                            </div>
                            <a href="{{ $bl->source_url }}" target="_blank" class="font-mono text-body-secondary small text-truncate d-block" style="max-width: 320px;">
                                {{ $bl->source_url }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                            </a>
                        </td>
                        <td>
                            <a href="{{ $bl->target_url }}" target="_blank" class="font-mono text-info small text-truncate d-block" style="max-width: 250px;">
                                {{ $bl->target_url }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-dark text-white border border-secondary border-opacity-50 font-mono">
                                "{{ $bl->anchor_text ?: 'N/A' }}"
                            </span>
                        </td>
                        <td class="text-center font-mono">
                            <span class="badge bg-dark text-success border border-success border-opacity-25 px-2 py-1">
                                {{ $bl->domain_rating }}
                            </span>
                        </td>
                        <td class="text-center font-mono">
                            @if($bl->is_dofollow)
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">
                                    dofollow
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-25 text-body-secondary border border-secondary border-opacity-25">
                                    nofollow
                                </span>
                            @endif
                        </td>
                        <td class="text-end font-mono text-body-secondary small text-nowrap">
                            {{ $bl->first_seen ? $bl->first_seen->format('M d, Y') : 'N/A' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-body-secondary font-mono py-4">
                            No backlinks logged yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($backlinks->hasPages())
        <div class="p-3 border-top border-secondary border-opacity-25">
            {{ $backlinks->links() }}
        </div>
    @endif
</div>
@endsection

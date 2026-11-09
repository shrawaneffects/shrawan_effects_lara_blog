@extends('frontend.openseo.layouts.openseo')

@section('title', 'Google Search Console Performance - ' . $activeProject->domain . ' - OpenSEO')

@section('openseo_content')
<!-- KPI Row: Clicks, Impressions, CTR, Position -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Total Clicks</span>
            <div class="openseo-stat-num text-info">{{ number_format($total_clicks) }}</div>
            <span class="text-info font-mono small mt-2 d-block">
                <i class="bi bi-cursor-fill me-1"></i> Search engine click-throughs
            </span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Total Impressions</span>
            <div class="openseo-stat-num text-white">{{ number_format($total_impressions) }}</div>
            <span class="text-body-secondary font-mono small mt-2 d-block">
                <i class="bi bi-eye-fill me-1"></i> SERP views in Google
            </span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Average CTR</span>
            <div class="openseo-stat-num text-success">{{ $avg_ctr }}%</div>
            <span class="text-success font-mono small mt-2 d-block">
                <i class="bi bi-percent me-1"></i> Click-through efficiency
            </span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Average Position</span>
            <div class="openseo-stat-num text-warning">{{ $avg_position }}</div>
            <span class="text-warning font-mono small mt-2 d-block">
                <i class="bi bi-bar-chart-fill me-1"></i> Google search average rank
            </span>
        </div>
    </div>
</div>

<!-- Date Filter Bar -->
<div class="openseo-hud-card p-3 p-md-4 mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="font-cyber fw-bold text-white mb-0">Search Console Analytics</h5>
            <p class="text-body-secondary font-mono small mb-0">Verified Google Organic Traffic data for {{ $activeProject->domain }}</p>
        </div>
        <div class="d-flex gap-1.5 font-mono small">
            <a href="{{ route('openseo.search-performance', ['days' => 7]) }}" class="btn btn-sm {{ $days == 7 ? 'btn-gradient' : 'btn-outline-secondary' }}">7 Days</a>
            <a href="{{ route('openseo.search-performance', ['days' => 28]) }}" class="btn btn-sm {{ $days == 28 ? 'btn-gradient' : 'btn-outline-secondary' }}">28 Days</a>
            <a href="{{ route('openseo.search-performance', ['days' => 90]) }}" class="btn btn-sm {{ $days == 90 ? 'btn-gradient' : 'btn-outline-secondary' }}">3 Months</a>
        </div>
    </div>
</div>

<!-- Tables: Top Queries & Top Pages -->
<div class="row g-4">
    <!-- Top Queries Table -->
    <div class="col-lg-7">
        <div class="openseo-hud-card p-3 p-md-4">
            <h5 class="font-cyber fw-bold text-white mb-1">Top Google Search Queries</h5>
            <p class="text-body-secondary font-mono small mb-3">Exact search terms bringing organic search visitors</p>

            <div class="table-responsive">
                <table class="table openseo-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Search Query</th>
                            <th class="text-end">Clicks</th>
                            <th class="text-end">Impressions</th>
                            <th class="text-end">CTR</th>
                            <th class="text-center">Avg Pos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($top_queries as $q)
                            <tr>
                                <td>
                                    <span class="fw-bold text-white font-mono">{{ $q['query'] }}</span>
                                </td>
                                <td class="text-end font-mono text-info fw-bold">
                                    {{ number_format($q['clicks']) }}
                                </td>
                                <td class="text-end font-mono text-body-secondary">
                                    {{ number_format($q['impressions']) }}
                                </td>
                                <td class="text-end font-mono text-success">
                                    {{ $q['ctr'] }}%
                                </td>
                                <td class="text-center font-mono">
                                    <span class="badge bg-dark {{ $q['position'] <= 3 ? 'text-success border-success' : 'text-body-secondary border-secondary' }} border border-opacity-25 px-2 py-1">
                                        #{{ $q['position'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Pages Table -->
    <div class="col-lg-5">
        <div class="openseo-hud-card p-3 p-md-4">
            <h5 class="font-cyber fw-bold text-white mb-1">Top Organic Pages</h5>
            <p class="text-body-secondary font-mono small mb-3">Most clicked landing pages</p>

            <div class="table-responsive">
                <table class="table openseo-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Landing Page</th>
                            <th class="text-end">Clicks</th>
                            <th class="text-end">Avg Pos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($top_pages as $p)
                            <tr>
                                <td>
                                    <a href="{{ $p['page'] }}" target="_blank" class="font-mono text-white small text-truncate d-block" style="max-width: 220px;">
                                        {{ $p['page'] }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </td>
                                <td class="text-end font-mono text-info fw-bold">
                                    {{ number_format($p['clicks']) }}
                                </td>
                                <td class="text-end font-mono">
                                    #{{ $p['position'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

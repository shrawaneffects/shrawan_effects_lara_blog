@extends('frontend.openseo.layouts.openseo')

@section('title', 'Technical Site Audit - ' . $activeProject->domain . ' - OpenSEO')

@section('openseo_content')
<!-- Audit Health Summary Row -->
<div class="row g-3 mb-4">
    <!-- Overall Health Score -->
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100 text-center d-flex flex-column justify-content-center">
            <span class="text-body-secondary font-mono small text-uppercase mb-2">Overall Site Health</span>
            <div class="openseo-stat-num text-success my-1" style="font-size: 3.2rem;">
                {{ $audit->health_score ?? 90 }}%
            </div>
            <span class="text-success font-mono small">
                <i class="bi bi-shield-check me-1"></i> Excellent Technical Hygiene
            </span>
        </div>
    </div>

    <!-- Total Pages Crawled -->
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Crawled Pages</span>
            <div class="openseo-stat-num text-white">{{ $audit->total_pages_crawled ?? 0 }}</div>
            <span class="text-body-secondary font-mono small mt-2 d-block">
                Last Crawled: <strong class="text-white">{{ $audit->completed_at ? $audit->completed_at->diffForHumans() : 'Recently' }}</strong>
            </span>
            <form action="{{ route('openseo.audit.crawl') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-sm btn-gradient font-mono w-100">
                    <i class="bi bi-arrow-repeat me-1"></i> Run Fresh Audit Crawl
                </button>
            </form>
        </div>
    </div>

    <!-- Errors (High Severity) -->
    <div class="col-sm-6 col-xl-2">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-body-secondary font-mono small text-uppercase">Errors</span>
                <span class="badge bg-danger bg-opacity-25 text-danger font-mono">HIGH</span>
            </div>
            <div class="openseo-stat-num text-danger">{{ $audit->total_errors ?? 0 }}</div>
            <span class="text-body-secondary font-mono small mt-2 d-block">Broken links, 4xx/5xx</span>
        </div>
    </div>

    <!-- Warnings (Medium Severity) -->
    <div class="col-sm-6 col-xl-2">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-body-secondary font-mono small text-uppercase">Warnings</span>
                <span class="badge bg-warning bg-opacity-25 text-warning font-mono">MED</span>
            </div>
            <div class="openseo-stat-num text-warning">{{ $audit->total_warnings ?? 0 }}</div>
            <span class="text-body-secondary font-mono small mt-2 d-block">Missing meta tags, TTFB</span>
        </div>
    </div>

    <!-- Notices (Low Severity) -->
    <div class="col-sm-6 col-xl-2">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-body-secondary font-mono small text-uppercase">Notices</span>
                <span class="badge bg-info bg-opacity-25 text-info font-mono">LOW</span>
            </div>
            <div class="openseo-stat-num text-info">{{ $audit->total_notices ?? 0 }}</div>
            <span class="text-body-secondary font-mono small mt-2 d-block">Minor content suggestions</span>
        </div>
    </div>
</div>

<!-- Technical Audit Issues Table -->
<div class="openseo-hud-card p-3 p-md-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="font-cyber fw-bold text-white mb-0">Discovered Technical Issues</h5>
            <p class="text-body-secondary font-mono small mb-0">Prioritized checklist to optimize crawlability and rank potential</p>
        </div>
        <a href="{{ route('openseo.reports.export', 'audit') }}" class="btn btn-sm btn-outline-success font-mono">
            <i class="bi bi-download me-1"></i> Export Issues CSV
        </a>
    </div>

    <div class="table-responsive">
        <table class="table openseo-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Severity</th>
                    <th>Page URL</th>
                    <th>Issue Type</th>
                    <th class="text-center">Status</th>
                    <th>Description & Recommendation</th>
                </tr>
            </thead>
            <tbody>
                @forelse($issues as $issue)
                    <tr>
                        <td>
                            @if($issue->severity === 'high')
                                <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 font-mono">
                                    <i class="bi bi-exclamation-octagon-fill me-1"></i> ERROR
                                </span>
                            @elseif($issue->severity === 'medium')
                                <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 font-mono">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> WARNING
                                </span>
                            @else
                                <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-50 font-mono">
                                    <i class="bi bi-info-circle-fill me-1"></i> NOTICE
                                </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ $issue->page_url }}" target="_blank" class="font-mono text-white small text-truncate d-block" style="max-width: 280px;">
                                {{ $issue->page_url }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-dark text-white border border-secondary border-opacity-50 font-mono">
                                {{ $issue->issue_type }}
                            </span>
                        </td>
                        <td class="text-center font-mono">
                            <span class="badge bg-dark {{ $issue->http_status == 200 ? 'text-success' : 'text-danger' }} border border-secondary border-opacity-50">
                                {{ $issue->http_status }}
                            </span>
                        </td>
                        <td>
                            <div class="text-white small fw-semibold">{{ $issue->description }}</div>
                            @if($issue->recommendation)
                                <div class="text-success small font-mono mt-1">
                                    <i class="bi bi-wrench me-1"></i> {{ $issue->recommendation }}
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-body-secondary font-mono py-4">
                            Zero technical errors found! Run a fresh audit crawl above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($issues instanceof \Illuminate\Pagination\LengthAwarePaginator && $issues->hasPages())
        <div class="p-3 border-top border-secondary border-opacity-25">
            {{ $issues->links() }}
        </div>
    @endif
</div>
@endsection

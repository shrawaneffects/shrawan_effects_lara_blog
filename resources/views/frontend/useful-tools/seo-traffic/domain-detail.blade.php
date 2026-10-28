@extends('layouts.app')

@section('title', $cleanDomain . ' - SEO & Rank Tracking | ' . config('app.name', 'Shrawan Effects'))
@section('meta_description', 'Detailed organic keyword positions 1–100, ranking trends, and traffic analysis for ' . $cleanDomain)
@section('canonical_url', route('useful-tools.seo-traffic.domain-detail', $cleanDomain))

@section('content')
<div class="container py-4 my-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2 font-mono small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('useful-tools.seo-traffic.index') }}" class="text-decoration-none">SEO & Traffic Analyzer</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $cleanDomain }}</li>
        </ol>
    </nav>

    <!-- Domain Header & Actions -->
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="telemetry-tag"><i class="bi bi-globe"></i> TARGET DOMAIN</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-mono small">Google India (1–100)</span>
                </div>
                <h1 class="fw-bold mb-1 display-6 font-cyber">{{ $cleanDomain }}</h1>
                <p class="text-body-secondary small mb-0 font-mono">
                    <i class="bi bi-clock-history me-1 text-success"></i> Monitored in real-time across organic SERP, keyword discovery, and competitor metrics.
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-sm btn-gradient rounded-pill px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addKeywordModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Keyword
                </button>
                <form action="{{ route('useful-tools.seo-traffic.discover-keywords') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="domain" value="{{ $cleanDomain }}">
                    <button type="submit" class="btn btn-sm btn-tactical rounded-pill px-3 py-2">
                        <i class="bi bi-magic me-1"></i> Discover Keywords
                    </button>
                </form>
                <form action="{{ route('useful-tools.seo-traffic.fetch-traffic') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="domain" value="{{ $cleanDomain }}">
                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2">
                        <i class="bi bi-arrow-repeat me-1"></i> Refresh Traffic
                    </button>
                </form>
                <a href="{{ route('useful-tools.seo-traffic.export-keywords', $cleanDomain) }}" class="btn btn-sm btn-success rounded-pill px-3 py-2">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Rankings
                </a>
            </div>
        </div>

        <hr class="my-3 border-secondary opacity-25">

        <!-- Domain Metric Pills -->
        <div class="row g-2 text-center font-mono">
            <div class="col-6 col-md-2">
                <div class="p-2 rounded-3 border bg-body-tertiary">
                    <div class="text-muted small">Total Keywords</div>
                    <div class="fw-bold fs-5 text-body mt-1">{{ number_format($domainTotalKeywords) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="p-2 rounded-3 border bg-body-tertiary">
                    <div class="text-success small">Positions 1–3</div>
                    <div class="fw-bold fs-5 text-success mt-1">{{ number_format($domainTop3) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="p-2 rounded-3 border bg-body-tertiary">
                    <div class="text-primary small">Positions 1–10</div>
                    <div class="fw-bold fs-5 text-primary mt-1">{{ number_format($domainTop10) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="p-2 rounded-3 border bg-body-tertiary">
                    <div class="text-info small">Positions 1–100</div>
                    <div class="fw-bold fs-5 text-info mt-1">{{ number_format($domainTop100) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="p-2 rounded-3 border bg-body-tertiary">
                    <div class="text-warning small">&gt; 100 / Unranked</div>
                    <div class="fw-bold fs-5 text-warning mt-1">{{ number_format($domainUnranked) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="p-2 rounded-3 border bg-body-tertiary">
                    <div class="text-muted small">Monthly Visits (Est.)</div>
                    <div class="fw-bold fs-5 text-gradient mt-1">{{ $latestTraffic ? number_format($latestTraffic->monthly_visits) : 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center rounded-3 mb-4 shadow-sm">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning d-flex align-items-center rounded-3 mb-4 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('warning') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center rounded-3 mb-4 shadow-sm">
            <i class="bi bi-shield-slash-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Ranking Trend Chart Section -->
    @if($chartKeywords->count() > 0)
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
            <div>
                <h5 class="fw-bold mb-0 font-cyber">
                    <i class="bi bi-graph-up text-gradient me-2"></i> Keyword Position Trend (Historical Depth 1–100)
                </h5>
                <span class="text-body-secondary small font-mono">Position #1 represents the highest rank (Top of page 1)</span>
            </div>
            <span class="telemetry-tag"><i class="bi bi-bezier2"></i> HISTORICAL SERP</span>
        </div>
        <div style="height: 280px; position: relative;">
            <canvas id="rankingHistoryChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Main Grid: Keywords & Traffic/Competitors -->
    <div class="row g-4">
        <!-- Keywords Table (Col 8) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 border-bottom pb-3">
                    <h5 class="fw-bold mb-0 font-cyber">
                        <i class="bi bi-list-stars text-gradient me-2"></i> Tracked Keywords
                    </h5>
                    <span class="text-body-secondary small font-mono">
                        {{ $keywords->total() }} Keywords Configured
                    </span>
                </div>

                <!-- Tier Filter Pills -->
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <a href="{{ route('useful-tools.seo-traffic.domain-detail', $cleanDomain) }}" class="btn btn-sm {{ !request('tier') ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 font-mono small">All ({{ $domainTotalKeywords }})</a>
                    <a href="{{ route('useful-tools.seo-traffic.domain-detail', [$cleanDomain, 'tier' => 'top3']) }}" class="btn btn-sm {{ request('tier') === 'top3' ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 font-mono small">Top 3 ({{ $domainTop3 }})</a>
                    <a href="{{ route('useful-tools.seo-traffic.domain-detail', [$cleanDomain, 'tier' => 'top10']) }}" class="btn btn-sm {{ request('tier') === 'top10' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 font-mono small">Top 10 ({{ $domainTop10 }})</a>
                    <a href="{{ route('useful-tools.seo-traffic.domain-detail', [$cleanDomain, 'tier' => 'top100']) }}" class="btn btn-sm {{ request('tier') === 'top100' ? 'btn-info text-dark' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 font-mono small">Top 100 ({{ $domainTop100 }})</a>
                    <a href="{{ route('useful-tools.seo-traffic.domain-detail', [$cleanDomain, 'tier' => 'improved']) }}" class="btn btn-sm {{ request('tier') === 'improved' ? 'btn-success' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 font-mono small">Improved</a>
                    <a href="{{ route('useful-tools.seo-traffic.domain-detail', [$cleanDomain, 'tier' => 'declined']) }}" class="btn btn-sm {{ request('tier') === 'declined' ? 'btn-danger' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 font-mono small">Declined</a>
                    <a href="{{ route('useful-tools.seo-traffic.domain-detail', [$cleanDomain, 'tier' => 'unranked']) }}" class="btn btn-sm {{ request('tier') === 'unranked' ? 'btn-warning text-dark' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 font-mono small">Unranked ({{ $domainUnranked }})</a>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 font-mono" style="font-size: 0.82rem;">
                        <thead class="table-light text-uppercase" style="font-size: 0.7rem;">
                            <tr>
                                <th>Keyword</th>
                                <th>Current Rank</th>
                                <th>Shift</th>
                                <th>Est. Volume</th>
                                <th>Difficulty</th>
                                <th>Last Checked</th>
                                <th class="text-end">Verify</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($keywords as $kw)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-body" style="font-size: 0.88rem;">{{ $kw->keyword }}</div>
                                        @if($kw->ranking_url)
                                            <a href="{{ $kw->ranking_url }}" target="_blank" rel="noopener noreferrer" class="text-success small text-decoration-none text-truncate d-inline-block" style="max-width: 220px;" title="{{ $kw->ranking_url }}">
                                                <i class="bi bi-link-45deg"></i> {{ parse_url($kw->ranking_url, PHP_URL_PATH) ?: '/' }}
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($kw->current_position !== null)
                                            <span class="badge {{ $kw->current_position <= 3 ? 'bg-success' : ($kw->current_position <= 10 ? 'bg-primary' : 'bg-body-secondary text-body border') }} rounded-pill px-2.5 py-1 fs-6">
                                                #{{ $kw->current_position }}
                                            </span>
                                        @else
                                            <span class="badge bg-body-secondary text-muted rounded-pill px-2.5 py-1">
                                                &gt; 100 / None
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($kw->position_change > 0)
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5">
                                                <i class="bi bi-arrow-up"></i> +{{ $kw->position_change }}
                                            </span>
                                        @elseif($kw->position_change < 0)
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0.5">
                                                <i class="bi bi-arrow-down"></i> {{ $kw->position_change }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $kw->search_volume ? number_format($kw->search_volume) . '/mo' : 'N/A' }}</td>
                                    <td>
                                        @if($kw->keyword_difficulty !== null)
                                            <div class="d-flex align-items-center gap-1">
                                                <div class="progress rounded-pill flex-grow-1" style="height: 6px; width: 45px;">
                                                    <div class="progress-bar {{ $kw->keyword_difficulty > 65 ? 'bg-danger' : ($kw->keyword_difficulty > 40 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $kw->keyword_difficulty }}%;"></div>
                                                </div>
                                                <small>{{ $kw->keyword_difficulty }}%</small>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $kw->last_checked_at ? $kw->last_checked_at->diffForHumans() : 'Never' }}</small>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('useful-tools.seo-traffic.check-rank', $kw->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" title="Check live SERP position">
                                                <i class="bi bi-arrow-clockwise"></i> Check
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No keywords found matching current criteria. Add keywords manually or click "Discover Keywords".
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($keywords->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $keywords->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar: Traffic Intelligence & Competitor Comparison (Col 4) -->
        <div class="col-lg-4">
            <!-- Traffic Intelligence Card -->
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h6 class="fw-bold mb-0 font-cyber">
                        <i class="bi bi-speedometer2 text-gradient me-2"></i> TRAFFIC ANALYTICS
                    </h6>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill font-mono small">
                        Third-Party Est.
                    </span>
                </div>

                @if($latestTraffic)
                    <div class="p-3 rounded-4 border bg-body-tertiary mb-3">
                        <div class="text-muted font-mono small mb-1">Estimated Monthly Visits</div>
                        <h2 class="fw-bold font-cyber text-gradient mb-2">{{ number_format($latestTraffic->monthly_visits) }}</h2>
                        <div class="d-flex justify-content-between small font-mono text-body-secondary pt-2 border-top">
                            <span>Organic: <strong>{{ number_format($latestTraffic->organic_traffic ?: 0) }}</strong></span>
                            <span>Paid: <strong>{{ number_format($latestTraffic->paid_traffic ?: 0) }}</strong></span>
                        </div>
                    </div>

                    @if(!empty($latestTraffic->top_countries))
                        <div class="mb-3">
                            <span class="fw-semibold small font-cyber text-muted d-block mb-2">TOP GEOGRAPHIC REGIONS</span>
                            @foreach($latestTraffic->top_countries as $ct)
                                <div class="d-flex justify-content-between small font-mono mb-1">
                                    <span>{{ $ct['country'] ?? 'Global' }}</span>
                                    <span class="fw-bold text-success">{{ $ct['share_pct'] ?? 0 }}%</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="small text-muted font-mono">
                        Data fresh as of: {{ $latestTraffic->snapshot_date ? $latestTraffic->snapshot_date->format('F Y') : 'Current' }} • Provider: {{ ucfirst($latestTraffic->provider) }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-bar-chart-line fs-2 text-muted mb-2 d-block"></i>
                        <h6 class="small fw-bold">No Traffic Snapshot Recorded</h6>
                        <p class="small text-muted font-mono mb-3">Click below to fetch third-party traffic metrics from Semrush.</p>
                        <form action="{{ route('useful-tools.seo-traffic.fetch-traffic') }}" method="POST">
                            @csrf
                            <input type="hidden" name="domain" value="{{ $cleanDomain }}">
                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Fetch Domain Traffic
                            </button>
                        </form>
                    </div>
                @endif

                <!-- First-Party Analytics Note -->
                <div class="mt-3 pt-3 border-top small text-body-secondary font-mono" style="font-size: 0.74rem;">
                    <i class="bi bi-shield-check text-success me-1"></i>
                    <strong>Category Distinction:</strong> Third-party estimated visits are mathematically modeled. Official GA4 and Search Console metrics require first-party OAuth authorization.
                </div>
            </div>

            <!-- Competitor Intelligence Card -->
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h6 class="fw-bold mb-0 font-cyber">
                        <i class="bi bi-intersect text-gradient me-2"></i> COMPETITOR OVERLAP
                    </h6>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-0.5 small" data-bs-toggle="modal" data-bs-target="#addCompetitorModal">
                        <i class="bi bi-plus"></i> Add
                    </button>
                </div>

                @if(!empty($competitorAnalysis['competitors']))
                    <div class="d-flex flex-column gap-3">
                        @foreach($competitorAnalysis['competitors'] as $comp)
                            <div class="p-3 rounded-3 border bg-body-tertiary">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="font-cyber small">{{ $comp['competitor_domain'] }}</strong>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill font-mono small">
                                        {{ $comp['shared_keywords_count'] }} Shared
                                    </span>
                                </div>
                                <div class="small font-mono text-body-secondary mb-2">
                                    <div>Competitor Unique: <strong>{{ $comp['competitor_unique_count'] }}</strong></div>
                                    <div>Est. Monthly Visits: <strong>{{ $comp['estimated_monthly_visits'] ? number_format($comp['estimated_monthly_visits']) : 'N/A' }}</strong></div>
                                </div>
                                @if(!empty($comp['shared_keywords']))
                                    <div class="small text-muted font-mono" style="font-size: 0.72rem;">
                                        Overlapping: {{ implode(', ', $comp['shared_keywords']) }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3 text-muted font-mono small">
                        No competitors linked to {{ $cleanDomain }} yet. Link competitor domains to benchmark keyword overlap and market presence.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Keyword -->
<div class="modal fade" id="addKeywordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 bg-body">
            <div class="modal-header border-bottom">
                <h5 class="modal-title font-cyber fw-bold">
                    <i class="bi bi-plus-circle text-gradient me-2"></i> Add Keyword to Track
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('useful-tools.seo-traffic.add-keyword') }}" method="POST">
                @csrf
                <input type="hidden" name="domain" value="{{ $cleanDomain }}">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Search Keyword *</label>
                        <input type="text" name="keyword" class="form-control rounded-pill px-3 py-2" placeholder="e.g. Website Developer in Faridabad" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Country</label>
                            <select name="country" class="form-select rounded-pill px-3 py-2 small">
                                <option value="IN" selected>India (google.co.in)</option>
                                <option value="US">United States (google.com)</option>
                                <option value="GB">United Kingdom (google.co.uk)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Device</label>
                            <select name="device" class="form-select rounded-pill px-3 py-2 small">
                                <option value="desktop" selected>Desktop</option>
                                <option value="mobile">Mobile</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gradient rounded-pill px-4 font-cyber fw-bold">Add & Track</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Competitor -->
<div class="modal fade" id="addCompetitorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 bg-body">
            <div class="modal-header border-bottom">
                <h5 class="modal-title font-cyber fw-bold">
                    <i class="bi bi-intersect text-gradient me-2"></i> Link Competitor Domain
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('useful-tools.seo-traffic.add-competitor') }}" method="POST">
                @csrf
                <input type="hidden" name="main_domain" value="{{ $cleanDomain }}">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Competitor Domain URL *</label>
                        <input type="text" name="competitor_domain" class="form-control rounded-pill px-3 py-2 font-mono small" placeholder="e.g. competitor.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Notes / Focus Area</label>
                        <input type="text" name="notes" class="form-control rounded-pill px-3 py-2 small" placeholder="e.g. Local competitor in NCR">
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gradient rounded-pill px-4 font-cyber fw-bold">Link Competitor</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($chartKeywords->count() > 0)
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('rankingHistoryChart');
    if (!ctx) return;

    @php
        $colors = ['#10b981', '#3b82f6', '#f59e0b', '#ec4899', '#8b5cf6'];
        $datasets = [];
        $allDates = [];

        foreach ($chartKeywords as $kIndex => $ck) {
            $dataPoints = [];
            foreach ($ck->rankings as $rk) {
                $dateStr = $rk->checked_date->format('M d');
                $allDates[$dateStr] = true;
                $dataPoints[$dateStr] = $rk->position ?: null;
            }
        }
        $sortedDates = array_keys($allDates);

        foreach ($chartKeywords as $kIndex => $ck) {
            $rowValues = [];
            $historicalByDate = [];
            foreach ($ck->rankings as $rk) {
                $historicalByDate[$rk->checked_date->format('M d')] = $rk->position ?: null;
            }
            foreach ($sortedDates as $d) {
                $rowValues[] = $historicalByDate[$d] ?? null;
            }

            $datasets[] = [
                'label' => $ck->keyword,
                'data' => $rowValues,
                'borderColor' => $colors[$kIndex % count($colors)],
                'backgroundColor' => 'transparent',
                'tension' => 0.3,
                'borderWidth' => 2,
                'pointRadius' => 4,
            ];
        }
    @endphp

    const dates = {!! json_encode($sortedDates) !!};
    const datasets = {!! json_encode($datasets) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates.length > 0 ? dates : ['Today'],
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    reverse: true, // Rank 1 is top, 100 is bottom
                    min: 1,
                    max: 100,
                    ticks: {
                        stepSize: 10,
                        callback: function(value) { return '#' + value; }
                    },
                    grid: { color: 'rgba(255, 255, 255, 0.08)' }
                },
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { boxWidth: 12, font: { family: 'monospace' } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': #' + context.raw;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif
@endsection

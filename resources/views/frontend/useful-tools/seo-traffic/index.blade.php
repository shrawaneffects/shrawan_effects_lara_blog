@extends('layouts.app')

@section('title', 'Website SEO & Traffic Analyzer - Useful Tools | ' . config('app.name', 'Shrawan Effects'))
@section('meta_description', 'Professional SEO keyword discovery, Google 1-100 SERP rank tracking, competitor intelligence, and domain traffic analytics.')
@section('canonical_url', route('useful-tools.seo-traffic.index'))

@section('content')
<div class="container py-4 my-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2 font-mono small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Useful Tools</a></li>
            <li class="breadcrumb-item active" aria-current="page">Website SEO & Traffic Analyzer</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="telemetry-tag"><i class="bi bi-graph-up-arrow"></i> SEO TELEMETRY SUITE</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono small">Positions 1–100 Live</span>
            </div>
            <h1 class="fw-bold mb-1 display-6 font-cyber">Website SEO & <span class="text-gradient">Traffic Analyzer</span></h1>
            <p class="text-body-secondary small mb-0 font-mono">
                Real-time Google organic SERP rank tracking, domain keyword discovery, competitor overlap, and multi-tier traffic intelligence.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-gradient rounded-pill px-3 py-2 small shadow-sm" data-bs-toggle="modal" data-bs-target="#trackDomainModal">
                <i class="bi bi-plus-circle me-1"></i> Track New Domain
            </button>
            <a href="{{ route('useful-tools.business-scraper.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small">
                <i class="bi bi-building me-1"></i> Business Scraper
            </a>
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

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <span class="text-body-secondary small font-mono">Tracked Domains</span>
                <h3 class="fw-bold mb-0 text-gradient font-cyber mt-1">{{ number_format($trackedDomains->count()) }}</h3>
                <small class="text-muted font-mono" style="font-size: 0.7rem;">Active Projects</small>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <span class="text-body-secondary small font-mono">Keywords (1–100)</span>
                <h3 class="fw-bold mb-0 text-body font-cyber mt-1">{{ number_format($totalTrackedKeywords) }}</h3>
                <small class="text-success font-mono" style="font-size: 0.7rem;">{{ $top100Count }} in Top 100</small>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <span class="text-body-secondary small font-mono">Top 3 Rankings</span>
                <h3 class="fw-bold mb-0 text-success font-cyber mt-1">{{ number_format($top3Count) }}</h3>
                <small class="text-muted font-mono" style="font-size: 0.7rem;">Highest Organic CTR</small>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <span class="text-body-secondary small font-mono">Top 10 (Page 1)</span>
                <h3 class="fw-bold mb-0 text-primary font-cyber mt-1">{{ number_format($top10Count) }}</h3>
                <small class="text-muted font-mono" style="font-size: 0.7rem;">First Page SERP</small>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <span class="text-body-secondary small font-mono">Position Changes</span>
                <div class="d-flex align-items-baseline gap-2 mt-1">
                    <span class="fw-bold text-success font-cyber fs-4">+{{ $improvedCount }}</span>
                    <span class="fw-bold text-danger font-cyber fs-5">-{{ $declinedCount }}</span>
                </div>
                <small class="text-muted font-mono" style="font-size: 0.7rem;">Improvements vs Drops</small>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <span class="text-body-secondary small font-mono">Est. Monthly Visits</span>
                <h3 class="fw-bold mb-0 text-warning font-cyber mt-1">{{ number_format($totalEstimatedVisits) }}</h3>
                <small class="text-muted font-mono" style="font-size: 0.7rem;">Third-Party Estimate</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Tracked Domains List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h5 class="fw-bold mb-0 font-cyber">
                        <i class="bi bi-globe2 text-gradient me-2"></i> Tracked Domains & Portfolios
                    </h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#trackDomainModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Domain
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 font-mono" style="font-size: 0.86rem;">
                        <thead class="table-light text-uppercase" style="font-size: 0.72rem;">
                            <tr>
                                <th>Target Domain</th>
                                <th>Tracked Keywords</th>
                                <th>Top Rank</th>
                                <th>Data Mode</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trackedDomains as $item)
                                @php
                                    $bestRank = \App\Models\UsefulTools\UsefulToolTrackedKeyword::where('user_id', auth()->id())
                                        ->where('domain', $item->domain)
                                        ->whereNotNull('current_position')
                                        ->min('current_position');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-3" style="width: 32px; height: 32px;">
                                                <i class="bi bi-globe"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('useful-tools.seo-traffic.domain-detail', $item->domain) }}" class="fw-bold text-body text-decoration-none font-cyber">
                                                    {{ $item->domain }}
                                                </a>
                                                <div class="small text-muted font-mono" style="font-size: 0.72rem;">
                                                    <i class="bi bi-shield-check text-success me-1"></i> SSRF Validated
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-body-secondary text-body border rounded-pill px-2.5 py-1">
                                            {{ $item->keywords_count }} Keywords
                                        </span>
                                    </td>
                                    <td>
                                        @if($bestRank)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                                                Position #{{ $bestRank }}
                                            </span>
                                        @else
                                            <span class="text-muted small">Not Ranked 1–100</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="telemetry-tag"><i class="bi bi-check2"></i> ORGANIC 1-100</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('useful-tools.seo-traffic.domain-detail', $item->domain) }}" class="btn btn-sm btn-gradient rounded-pill px-3">
                                            Open Suite <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-graph-up fs-1 text-body-tertiary mb-3 d-block"></i>
                                        <h6 class="fw-bold font-cyber">No Domains Tracked Yet</h6>
                                        <p class="text-body-secondary small font-mono mb-3" style="max-width: 440px; margin: 0 auto;">
                                            Add your primary domain or competitor websites to start monitoring organic Google search rankings from position 1 to 100.
                                        </p>
                                        <button class="btn btn-gradient rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#trackDomainModal">
                                            <i class="bi bi-plus-circle me-1"></i> Track First Domain
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent SERP Ranking Checks Activity -->
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h6 class="fw-bold mb-0 font-cyber">
                        <i class="bi bi-activity text-gradient me-2"></i> Recent SERP Verification Logs
                    </h6>
                    <span class="telemetry-tag"><i class="bi bi-broadcast"></i> REAL-TIME ENGINE</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle font-mono small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Keyword</th>
                                <th>Domain</th>
                                <th>Position</th>
                                <th>Shift</th>
                                <th>Provider</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRankings as $rank)
                                <tr>
                                    <td class="fw-semibold">{{ $rank->trackedKeyword->keyword ?? 'N/A' }}</td>
                                    <td class="text-muted">{{ $rank->trackedKeyword->domain ?? 'N/A' }}</td>
                                    <td>
                                        @if($rank->is_found && $rank->position)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5">
                                                #{{ $rank->position }}
                                            </span>
                                        @else
                                            <span class="badge bg-body-secondary text-muted rounded-pill px-2 py-0.5">&gt; 100</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rank->position_change > 0)
                                            <span class="text-success"><i class="bi bi-arrow-up-right"></i> +{{ $rank->position_change }}</span>
                                        @elseif($rank->position_change < 0)
                                            <span class="text-danger"><i class="bi bi-arrow-down-right"></i> {{ $rank->position_change }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ strtoupper($rank->provider) }}</td>
                                    <td>{{ $rank->checked_date ? $rank->checked_date->format('M d, Y') : 'Today' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No ranking checks conducted yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Providers Diagnostic & Setup -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h6 class="fw-bold mb-0 font-cyber">
                        <i class="bi bi-hdd-network text-gradient me-2"></i> DATA GATEWAYS
                    </h6>
                    <span class="telemetry-tag"><i class="bi bi-key"></i> API KEYS</span>
                </div>

                <p class="text-body-secondary small mb-3 leading-relaxed">
                    The analyzer connects to authorized providers to deliver exact 1–100 rankings and competitor estimates.
                </p>

                <div class="d-flex flex-column gap-2 mb-3">
                    @foreach($providersStatus as $key => $p)
                        <div class="p-3 rounded-3 border bg-body-tertiary">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <strong class="small">{{ $p['name'] }}</strong>
                                <x-useful-tools.provider-status-badge :isConfigured="$p['is_configured']" :name="$p['name']" :description="$p['description']" />
                            </div>
                            <div class="text-body-secondary font-mono small mb-1" style="font-size: 0.72rem;">{{ $p['type'] }}</div>
                            <div class="text-muted font-mono" style="font-size: 0.68rem;">{{ $p['description'] }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="alert alert-info py-2.5 px-3 small font-mono rounded-3 mb-0" style="font-size: 0.75rem;">
                    <i class="bi bi-shield-check text-primary me-1"></i>
                    <strong>Cost Control Active:</strong> All calls are deduplicated and logged in <code>useful_tool_usage_logs</code>.
                </div>
            </div>

            <!-- Quick Information Box -->
            <div class="card card-newsletter-gradient border-0 shadow-lg p-4 rounded-4 text-white">
                <span class="badge bg-white text-dark font-mono mb-2">SEO STANDARDS</span>
                <h5 class="fw-bold font-cyber mb-2">1–100 Rank Integrity</h5>
                <p class="small text-white-75 mb-3 leading-relaxed">
                    Unlike tools that stop at page 1, our engine scans all organic 100 positions to track early improvements before keywords reach top positions.
                </p>
                <div class="small font-mono text-white-50">
                    <div>• SSRF Network Guard Active</div>
                    <div>• Zero Unchecked Zero-Ranks</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Track New Domain -->
<div class="modal fade" id="trackDomainModal" tabindex="-1" aria-labelledby="trackDomainModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 bg-body">
            <div class="modal-header border-bottom">
                <h5 class="modal-title font-cyber fw-bold" id="trackDomainModalLabel">
                    <i class="bi bi-globe text-gradient me-2"></i> Track New Domain
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('useful-tools.seo-traffic.add-keyword') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Domain / Website URL *</label>
                        <input type="text" name="domain" class="form-control rounded-pill px-3 py-2 font-mono small" placeholder="e.g. shrawaneffects.com or https://example.com" required>
                        <div class="form-text small font-mono" style="font-size: 0.72rem;">
                            SSRF protected: Localhost, 127.0.0.1, and private IP blocks are blocked.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Initial Keyword to Monitor *</label>
                        <input type="text" name="keyword" class="form-control rounded-pill px-3 py-2 small" placeholder="e.g. Website Development Faridabad" required>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Target Country</label>
                            <select name="country" class="form-select rounded-pill px-3 py-2 small">
                                <option value="IN" selected>India (google.co.in)</option>
                                <option value="US">United States (google.com)</option>
                                <option value="GB">United Kingdom (google.co.uk)</option>
                                <option value="AE">UAE (google.ae)</option>
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
                    <button type="submit" class="btn btn-gradient rounded-pill px-4 font-cyber fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Start Tracking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

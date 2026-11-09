@extends('frontend.openseo.layouts.openseo')

@section('title', 'Domain Overview - ' . $activeProject->domain . ' - OpenSEO')

@section('openseo_content')
<!-- Metric Cards Row -->
<div class="row g-3 mb-4">
    <!-- Authority Score -->
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-body-secondary font-mono small text-uppercase">Authority Score</span>
                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 font-mono">DR</span>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <div class="openseo-stat-num">{{ $overview['authority_score'] }}</div>
                <div class="text-body-secondary font-mono small">/ 100</div>
            </div>
            <div class="progress mt-3 bg-dark border border-secondary border-opacity-25" style="height: 6px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $overview['authority_score'] }}%;" aria-valuenow="{{ $overview['authority_score'] }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <span class="text-body-secondary font-mono small d-block mt-2">
                <i class="bi bi-shield-check text-success me-1"></i> Strong Domain Trust
            </span>
        </div>
    </div>

    <!-- Organic Search Traffic -->
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-body-secondary font-mono small text-uppercase">Est. Organic Traffic</span>
                <i class="bi bi-arrow-up-right text-success"></i>
            </div>
            <div class="openseo-stat-num">{{ number_format($overview['organic_traffic']) }}</div>
            <span class="text-success font-mono small d-block mt-2">
                <i class="bi bi-graph-up-arrow me-1"></i> +14.2% from last month
            </span>
            <span class="text-body-secondary font-mono small d-block">Monthly estimated visits</span>
        </div>
    </div>

    <!-- Organic Keywords -->
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-body-secondary font-mono small text-uppercase">Organic Keywords</span>
                <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25 font-mono">SERP</span>
            </div>
            <div class="openseo-stat-num">{{ number_format($overview['organic_keywords_count']) }}</div>
            <div class="d-flex gap-2 mt-2 font-mono small">
                <span class="text-success"><i class="bi bi-trophy-fill me-1"></i>Top 3: {{ $overview['rank_distribution']['top3'] }}</span>
                <span class="text-info"><i class="bi bi-star-fill me-1"></i>Top 10: {{ $overview['rank_distribution']['top10'] }}</span>
            </div>
            <span class="text-body-secondary font-mono small d-block">Monitored search terms</span>
        </div>
    </div>

    <!-- Backlinks Profile -->
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-body-secondary font-mono small text-uppercase">Backlinks / Domains</span>
                <i class="bi bi-link-45deg text-info fs-5"></i>
            </div>
            <div class="d-flex align-items-baseline gap-2">
                <div class="openseo-stat-num">{{ number_format($overview['backlinks_summary']['total_backlinks'] ?? 2450) }}</div>
                <div class="text-body-secondary font-mono small">links</div>
            </div>
            <span class="text-info font-mono small d-block mt-2">
                <i class="bi bi-globe me-1"></i> {{ number_format($overview['backlinks_summary']['referring_domains'] ?? 184) }} Referring Domains
            </span>
            <span class="text-body-secondary font-mono small d-block">Dofollow ratio: 82%</span>
        </div>
    </div>
</div>

<!-- Charts Row: Traffic Trend & Rank Distribution -->
<div class="row g-4 mb-4">
    <!-- 6-Month Organic Growth Trend -->
    <div class="col-lg-8">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="font-cyber fw-bold mb-0 text-white">Organic Traffic Trend (6 Months)</h5>
                    <p class="text-body-secondary font-mono small mb-0">Historical search engine visitor estimate</p>
                </div>
                <span class="badge bg-dark text-success border border-success border-opacity-50 font-mono">
                    <span class="cyber-beacon me-1"></span>DATA ACCELERATING
                </span>
            </div>

            <!-- Visual Bar Graph -->
            <div class="pt-3">
                <div class="d-flex align-items-end justify-content-between gap-2" style="height: 180px;">
                    @foreach($overview['traffic_trend'] as $point)
                        @php
                            $maxTraffic = max(array_column($overview['traffic_trend'], 'traffic'));
                            $heightPct = $maxTraffic > 0 ? round(($point['traffic'] / $maxTraffic) * 100) : 50;
                        @endphp
                        <div class="text-center flex-fill d-flex flex-column align-items-center justify-content-end h-100">
                            <span class="font-mono text-success small mb-1" style="font-size: 0.72rem;">{{ number_format($point['traffic']) }}</span>
                            <div class="w-100 rounded-top" style="height: {{ $heightPct }}%; background: linear-gradient(180deg, #00ff66 0%, rgba(0, 240, 255, 0.4) 100%); min-height: 15px; border: 1px solid rgba(0, 255, 102, 0.5);"></div>
                            <span class="font-mono text-body-secondary small mt-2">{{ $point['month'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- SERP Rank Distribution -->
    <div class="col-lg-4">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <h5 class="font-cyber fw-bold mb-1 text-white">SERP Positions Breakdown</h5>
            <p class="text-body-secondary font-mono small mb-3">Distribution of ranking positions</p>

            <div class="d-flex flex-column gap-3">
                <div>
                    <div class="d-flex justify-content-between font-mono small mb-1">
                        <span class="text-success"><i class="bi bi-record-circle-fill me-1"></i> Pos 1 - 3 (Top Spot)</span>
                        <span class="text-white fw-bold">{{ $overview['rank_distribution']['top3'] }}</span>
                    </div>
                    <div class="progress bg-dark" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $overview['rank_distribution']['top3'] * 15 }}%;"></div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between font-mono small mb-1">
                        <span class="text-info"><i class="bi bi-record-circle-fill me-1"></i> Pos 4 - 10 (Page 1)</span>
                        <span class="text-white fw-bold">{{ $overview['rank_distribution']['top10'] }}</span>
                    </div>
                    <div class="progress bg-dark" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: {{ $overview['rank_distribution']['top10'] * 15 }}%;"></div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between font-mono small mb-1">
                        <span class="text-primary"><i class="bi bi-record-circle-fill me-1"></i> Pos 11 - 20 (Page 2)</span>
                        <span class="text-white fw-bold">{{ $overview['rank_distribution']['top20'] }}</span>
                    </div>
                    <div class="progress bg-dark" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ max(10, $overview['rank_distribution']['top20'] * 15) }}%;"></div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between font-mono small mb-1">
                        <span class="text-secondary"><i class="bi bi-record-circle-fill me-1"></i> Pos 21 - 50</span>
                        <span class="text-white fw-bold">{{ $overview['rank_distribution']['top50'] }}</span>
                    </div>
                    <div class="progress bg-dark" style="height: 6px;">
                        <div class="progress-bar bg-secondary" style="width: {{ max(5, $overview['rank_distribution']['top50'] * 15) }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables: Top Ranking Pages & Top Competitors -->
<div class="row g-4">
    <!-- Top Pages Table -->
    <div class="col-lg-7">
        <div class="openseo-hud-card p-3 p-md-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="font-cyber fw-bold mb-0 text-white">Top Organic Pages</h5>
                    <p class="text-body-secondary font-mono small mb-0">Highest traffic drivers for {{ $activeProject->domain }}</p>
                </div>
                <a href="{{ route('openseo.keywords') }}" class="btn btn-sm btn-outline-success font-mono">
                    View All Keywords
                </a>
            </div>

            <div class="table-responsive">
                <table class="table openseo-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>URL & Title</th>
                            <th class="text-end">Traffic</th>
                            <th class="text-end">Keywords</th>
                            <th>Top Keyword</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overview['top_pages'] as $page)
                            <tr>
                                <td>
                                    <div class="fw-bold text-white text-truncate" style="max-width: 280px;">{{ $page['title'] }}</div>
                                    <a href="{{ $page['url'] }}" target="_blank" class="font-mono text-body-secondary small text-truncate d-block" style="max-width: 280px;">
                                        {{ $page['url'] }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </td>
                                <td class="text-end font-mono text-success fw-bold">
                                    {{ number_format($page['traffic']) }}
                                </td>
                                <td class="text-end font-mono text-body-secondary">
                                    {{ $page['keywords_count'] }}
                                </td>
                                <td>
                                    <span class="badge bg-dark text-info border border-info border-opacity-25 font-mono">
                                        {{ $page['top_keyword'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Organic Competitors -->
    <div class="col-lg-5">
        <div class="openseo-hud-card p-3 p-md-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="font-cyber fw-bold mb-0 text-white">Top Competitors</h5>
                    <p class="text-body-secondary font-mono small mb-0">Market overlap & organic traffic</p>
                </div>
                <a href="{{ route('openseo.competitors') }}" class="btn btn-sm btn-outline-info font-mono">
                    Gap Analysis
                </a>
            </div>

            <div class="table-responsive">
                <table class="table openseo-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Competitor</th>
                            <th class="text-end">Traffic</th>
                            <th class="text-end">Overlap</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overview['competitors'] as $comp)
                            <tr>
                                <td>
                                    <div class="fw-bold text-white font-mono">{{ $comp->competitor_domain }}</div>
                                    <span class="text-body-secondary small font-mono">{{ number_format($comp->common_keywords) }} shared kws</span>
                                </td>
                                <td class="text-end font-mono text-info fw-bold">
                                    {{ number_format($comp->organic_traffic) }}
                                </td>
                                <td class="text-end font-mono text-success">
                                    {{ $comp->keyword_overlap_pct }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-body-secondary font-mono py-4">
                                    No competitors recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

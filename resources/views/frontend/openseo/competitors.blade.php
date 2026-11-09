@extends('frontend.openseo.layouts.openseo')

@section('title', 'Competitor Insights - ' . $activeProject->domain . ' - OpenSEO')

@section('openseo_content')
<!-- Add Competitor Card -->
<div class="openseo-hud-card p-3 p-md-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="font-cyber fw-bold text-white mb-0">Organic Competitors</h5>
            <p class="text-body-secondary font-mono small mb-0">Direct search rivals sharing SERP real estate with {{ $activeProject->domain }}</p>
        </div>
        <button class="btn btn-sm btn-gradient font-mono" type="button" data-bs-toggle="collapse" data-bs-target="#addCompetitorCollapse" aria-expanded="false" aria-controls="addCompetitorCollapse">
            <i class="bi bi-plus-lg me-1"></i> Add Competitor
        </button>
    </div>

    <!-- Collapsible Add Form -->
    <div class="collapse mb-3" id="addCompetitorCollapse">
        <div class="p-3 rounded-3 bg-dark border border-secondary border-opacity-50">
            <form action="{{ route('openseo.competitors.store') }}" method="POST" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-6">
                    <input type="text" name="competitor_domain" class="form-control form-control-sm bg-dark text-white border-secondary border-opacity-50 font-mono" placeholder="Domain name (e.g. competitor.com)..." required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="notes" class="form-control form-control-sm bg-dark text-white border-secondary border-opacity-50 font-mono" placeholder="Optional notes (e.g. regional agency)...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-gradient w-100 font-mono">
                        Save Competitor
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Competitors List Table -->
    <div class="table-responsive">
        <table class="table openseo-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Competitor Domain</th>
                    <th class="text-end">Est. Monthly Traffic</th>
                    <th class="text-end">Organic Keywords</th>
                    <th class="text-end">Shared Keywords</th>
                    <th class="text-end">Market Overlap</th>
                    <th>Notes</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($competitors as $comp)
                    <tr>
                        <td>
                            <div class="fw-bold text-white fs-6 font-mono">{{ $comp->competitor_domain }}</div>
                        </td>
                        <td class="text-end font-mono text-info fw-bold">
                            {{ number_format($comp->organic_traffic) }}
                        </td>
                        <td class="text-end font-mono text-body-secondary">
                            {{ number_format($comp->organic_keywords) }}
                        </td>
                        <td class="text-end font-mono text-success fw-bold">
                            {{ number_format($comp->common_keywords) }}
                        </td>
                        <td class="text-end font-mono">
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">
                                {{ $comp->keyword_overlap_pct }}%
                            </span>
                        </td>
                        <td class="text-body-secondary small">
                            {{ $comp->notes ?: 'Regional competitor' }}
                        </td>
                        <td class="text-end">
                            <form action="{{ route('openseo.competitors.destroy', $comp->id) }}" method="POST" onsubmit="return confirm('Remove competitor?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger font-mono p-1 px-2" title="Remove">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-body-secondary font-mono py-4">
                            No competitors added yet. Click "+ Add Competitor" above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Keyword Gap Analysis (High Value Opportunities) -->
<div class="openseo-hud-card p-3 p-md-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="font-cyber fw-bold text-white mb-0">Keyword Gap Opportunities</h5>
            <p class="text-body-secondary font-mono small mb-0">Keywords that competitors rank for on Page 1, but {{ $activeProject->name }} does not yet target</p>
        </div>
        <span class="badge bg-dark text-warning border border-warning border-opacity-50 font-mono">
            <i class="bi bi-lightning-charge-fill me-1"></i> HIGH IMPACT
        </span>
    </div>

    <div class="table-responsive">
        <table class="table openseo-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Untargeted Opportunity Keyword</th>
                    <th class="text-end">Search Volume</th>
                    <th class="text-end">KD%</th>
                    <th class="text-end">Est. CPC</th>
                    <th>Top Ranking Competitor</th>
                    <th class="text-center">Competitor Rank</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gapKeywords as $gap)
                    <tr>
                        <td>
                            <span class="fw-bold text-white fs-6">{{ $gap['keyword'] }}</span>
                        </td>
                        <td class="text-end font-mono text-success fw-bold">
                            {{ number_format($gap['search_volume']) }}
                        </td>
                        <td class="text-end font-mono">
                            @php
                                $gkdColor = $gap['keyword_difficulty'] <= 30 ? 'text-success' : 'text-warning';
                            @endphp
                            <span class="{{ $gkdColor }} fw-bold">{{ $gap['keyword_difficulty'] }}%</span>
                        </td>
                        <td class="text-end font-mono text-body-secondary">
                            ${{ number_format($gap['cpc'], 2) }}
                        </td>
                        <td>
                            <span class="badge bg-dark text-info border border-info border-opacity-25 font-mono">
                                {{ $gap['top_competitor'] }}
                            </span>
                        </td>
                        <td class="text-center font-mono">
                            <span class="badge bg-dark text-warning border border-warning border-opacity-25 px-2.5 py-1">
                                #{{ $gap['competitor_rank'] }}
                            </span>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('openseo.keywords.track') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="keyword" value="{{ $gap['keyword'] }}">
                                <input type="hidden" name="search_volume" value="{{ $gap['search_volume'] }}">
                                <input type="hidden" name="cpc" value="{{ $gap['cpc'] }}">
                                <input type="hidden" name="keyword_difficulty" value="{{ $gap['keyword_difficulty'] }}">
                                <button type="submit" class="btn btn-sm btn-gradient font-mono py-1 px-2.5">
                                    <i class="bi bi-plus-lg me-1"></i> Steal Keyword
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

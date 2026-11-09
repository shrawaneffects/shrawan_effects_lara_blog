@extends('frontend.openseo.layouts.openseo')

@section('title', 'Rank Tracker - ' . $activeProject->domain . ' - OpenSEO')

@section('openseo_content')
<!-- KPI Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl">
        <div class="openseo-hud-card p-3 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Tracked Keywords</span>
            <div class="openseo-stat-num">{{ $totalTracked }}</div>
            <span class="text-body-secondary font-mono small mt-1 d-block">Active monitoring</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="openseo-hud-card p-3 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">In Top 3 (Podium)</span>
            <div class="openseo-stat-num text-success">{{ $top3Count }}</div>
            <span class="text-success font-mono small mt-1 d-block"><i class="bi bi-trophy-fill me-1"></i>High CTR position</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="openseo-hud-card p-3 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">In Top 10 (Page 1)</span>
            <div class="openseo-stat-num text-info">{{ $top10Count }}</div>
            <span class="text-info font-mono small mt-1 d-block"><i class="bi bi-star-fill me-1"></i>First page results</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="openseo-hud-card p-3 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Improved (▲)</span>
            <div class="openseo-stat-num text-success">{{ $improvedCount }}</div>
            <span class="text-success font-mono small mt-1 d-block"><i class="bi bi-arrow-up-right me-1"></i>Positive momentum</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="openseo-hud-card p-3 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Declined (▼)</span>
            <div class="openseo-stat-num text-danger">{{ $declinedCount }}</div>
            <span class="text-danger font-mono small mt-1 d-block"><i class="bi bi-arrow-down-right me-1"></i>Attention required</span>
        </div>
    </div>
</div>

<!-- Controls & Filter Bar -->
<div class="openseo-hud-card p-3 p-md-4 mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <!-- Quick Filters -->
        <div class="d-flex align-items-center gap-1.5 flex-wrap">
            <a href="{{ route('openseo.rank-tracking') }}" class="btn btn-sm {{ !request('filter') ? 'btn-gradient' : 'btn-outline-secondary' }} font-mono">
                All ({{ $totalTracked }})
            </a>
            <a href="{{ route('openseo.rank-tracking', ['filter' => 'top3']) }}" class="btn btn-sm {{ request('filter') === 'top3' ? 'btn-gradient' : 'btn-outline-secondary' }} font-mono">
                Top 3 ({{ $top3Count }})
            </a>
            <a href="{{ route('openseo.rank-tracking', ['filter' => 'top10']) }}" class="btn btn-sm {{ request('filter') === 'top10' ? 'btn-gradient' : 'btn-outline-secondary' }} font-mono">
                Top 10 ({{ $top10Count }})
            </a>
            <a href="{{ route('openseo.rank-tracking', ['filter' => 'improved']) }}" class="btn btn-sm {{ request('filter') === 'improved' ? 'btn-gradient' : 'btn-outline-secondary' }} font-mono text-success">
                ▲ Improved ({{ $improvedCount }})
            </a>
            <a href="{{ route('openseo.rank-tracking', ['filter' => 'declined']) }}" class="btn btn-sm {{ request('filter') === 'declined' ? 'btn-gradient' : 'btn-outline-secondary' }} font-mono text-danger">
                ▼ Declined ({{ $declinedCount }})
            </a>
        </div>

        <!-- Search & Refresh Actions -->
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('openseo.rank-tracking') }}" method="GET" class="d-flex">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm bg-dark text-white border-secondary border-opacity-50 font-mono" placeholder="Filter tracked...">
            </form>

            <form action="{{ route('openseo.rank-tracking.refresh-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-gradient font-mono text-nowrap">
                    <i class="bi bi-arrow-repeat me-1"></i> Refresh All SERPs
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Tracked Keywords SERP Table -->
<div class="openseo-hud-card p-3 p-md-4">
    <div class="table-responsive">
        <table class="table openseo-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Keyword & Ranked Page</th>
                    <th class="text-center">Current Position</th>
                    <th class="text-center">Change</th>
                    <th class="text-center">Best Rank</th>
                    <th class="text-end">Volume</th>
                    <th class="text-end">KD%</th>
                    <th>SERP Features</th>
                    <th class="text-end">Last Checked</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trackedKeywords as $kw)
                    <tr>
                        <td>
                            <div class="fw-bold text-white fs-6">{{ $kw->keyword }}</div>
                            @if($kw->target_url)
                                <a href="{{ $kw->target_url }}" target="_blank" class="font-mono text-body-secondary small d-block text-truncate" style="max-width: 300px;">
                                    {{ $kw->target_url }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            @endif
                        </td>
                        <td class="text-center font-mono">
                            @if($kw->current_rank)
                                <span class="badge bg-dark {{ $kw->current_rank <= 3 ? 'text-success border-success' : ($kw->current_rank <= 10 ? 'text-info border-info' : 'text-white border-secondary') }} border border-opacity-50 px-3 py-2 fs-6">
                                    #{{ $kw->current_rank }}
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-25 text-body-secondary font-mono">> 100</span>
                            @endif
                        </td>
                        <td class="text-center font-mono">
                            @if($kw->rank_change > 0)
                                <span class="rank-change-badge rank-change-up">
                                    <i class="bi bi-arrow-up-short"></i> +{{ $kw->rank_change }}
                                </span>
                            @elseif($kw->rank_change < 0)
                                <span class="rank-change-badge rank-change-down">
                                    <i class="bi bi-arrow-down-short"></i> {{ $kw->rank_change }}
                                </span>
                            @else
                                <span class="rank-change-badge rank-change-same">
                                    <i class="bi bi-dash"></i> 0
                                </span>
                            @endif
                        </td>
                        <td class="text-center font-mono text-body-secondary">
                            #{{ $kw->best_rank ?: ($kw->current_rank ?: '--') }}
                        </td>
                        <td class="text-end font-mono text-success">
                            {{ number_format($kw->search_volume) }}
                        </td>
                        <td class="text-end font-mono">
                            <span class="text-{{ $kw->kd_badge_color }} fw-bold">{{ $kw->keyword_difficulty }}%</span>
                        </td>
                        <td>
                            @if(!empty($kw->serp_features))
                                <div class="d-flex gap-1 flex-wrap">
                                    @foreach($kw->serp_features as $feature)
                                        <span class="badge bg-dark text-info border border-info border-opacity-25 font-mono" style="font-size: 0.65rem;">
                                            {{ $feature }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-body-secondary small font-mono">Standard SERP</span>
                            @endif
                        </td>
                        <td class="text-end font-mono small text-body-secondary text-nowrap">
                            {{ $kw->last_checked_at ? $kw->last_checked_at->diffForHumans() : 'Pending' }}
                        </td>
                        <td class="text-end text-nowrap">
                            <form action="{{ route('openseo.rank-tracking.check', $kw->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success font-mono p-1 px-2" title="Run SERP Check">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </form>
                            <form action="{{ route('openseo.keywords.untrack', $kw->id) }}" method="POST" onsubmit="return confirm('Stop tracking this keyword?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger font-mono p-1 px-2" title="Untrack">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-body-secondary font-mono py-4">
                            No keywords currently tracked. Add keywords from the Keyword Research tab to start tracking rankings.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($trackedKeywords->hasPages())
        <div class="p-3 border-top border-secondary border-opacity-25">
            {{ $trackedKeywords->links() }}
        </div>
    @endif
</div>
@endsection

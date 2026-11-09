@extends('frontend.openseo.layouts.openseo')

@section('title', 'Keyword Research - ' . $activeProject->domain . ' - OpenSEO')

@section('openseo_content')
<!-- Search Form Bar -->
<div class="openseo-hud-card p-3 p-md-4 mb-4">
    <form action="{{ route('openseo.keywords') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-lg-8">
            <div class="input-group">
                <span class="input-group-text bg-dark border-secondary border-opacity-50 text-success">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="q" value="{{ $query }}" class="form-control form-control-lg bg-dark text-white border-secondary border-opacity-50 font-mono" placeholder="Enter keyword or search term (e.g. web development, laravel agency)..." required>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <select name="country" class="form-select form-select-lg bg-dark text-white border-secondary border-opacity-50 font-mono">
                <option value="US" {{ ($activeProject->target_country == 'US') ? 'selected' : '' }}>United States (US)</option>
                <option value="IN" {{ ($activeProject->target_country == 'IN') ? 'selected' : '' }}>India (IN)</option>
                <option value="GB" {{ ($activeProject->target_country == 'GB') ? 'selected' : '' }}>United Kingdom (GB)</option>
                <option value="CA" {{ ($activeProject->target_country == 'CA') ? 'selected' : '' }}>Canada (CA)</option>
                <option value="AU" {{ ($activeProject->target_country == 'AU') ? 'selected' : '' }}>Australia (AU)</option>
            </select>
        </div>
        <div class="col-sm-6 col-lg-2">
            <button type="submit" class="btn btn-lg btn-gradient w-100 font-mono">
                <i class="bi bi-cpu me-1"></i> Analyze
            </button>
        </div>
    </form>
</div>

<!-- Seed Keyword Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="font-cyber fw-bold text-white mb-0">
                    Keyword: <span class="text-gradient font-mono">"{{ $researchData['seed']['keyword'] }}"</span>
                </h4>
                <p class="text-body-secondary font-mono small mb-0">Algorithms analyzed across live SERP indexes</p>
            </div>
            @if($researchData['seed']['is_tracked'])
                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 font-mono p-2">
                    <i class="bi bi-check2-circle me-1"></i> Tracked in Project
                </span>
            @else
                <form action="{{ route('openseo.keywords.track') }}" method="POST">
                    @csrf
                    <input type="hidden" name="keyword" value="{{ $researchData['seed']['keyword'] }}">
                    <input type="hidden" name="search_volume" value="{{ $researchData['seed']['search_volume'] }}">
                    <input type="hidden" name="cpc" value="{{ $researchData['seed']['cpc'] }}">
                    <input type="hidden" name="competition" value="{{ $researchData['seed']['competition'] }}">
                    <input type="hidden" name="keyword_difficulty" value="{{ $researchData['seed']['keyword_difficulty'] }}">
                    <input type="hidden" name="search_intent" value="{{ $researchData['seed']['search_intent'] }}">
                    <button type="submit" class="btn btn-sm btn-gradient font-mono">
                        <i class="bi bi-plus-circle me-1"></i> Add to Rank Tracker
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Monthly Volume -->
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Search Volume</span>
            <div class="openseo-stat-num">{{ number_format($researchData['seed']['search_volume']) }}</div>
            <span class="text-body-secondary font-mono small mt-2 d-block">Monthly avg searches</span>
        </div>
    </div>

    <!-- Keyword Difficulty -->
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Keyword Difficulty (KD)</span>
            <div class="d-flex align-items-baseline gap-2">
                <div class="openseo-stat-num">{{ $researchData['seed']['keyword_difficulty'] }}%</div>
                @php
                    $kd = $researchData['seed']['keyword_difficulty'];
                    $kdColor = $kd <= 30 ? 'success' : ($kd <= 60 ? 'warning' : 'danger');
                    $kdLabel = $kd <= 15 ? 'Very Easy' : ($kd <= 30 ? 'Easy' : ($kd <= 50 ? 'Possible' : ($kd <= 70 ? 'Hard' : 'Very Hard')));
                @endphp
                <span class="badge bg-{{ $kdColor }} bg-opacity-25 text-{{ $kdColor }} border border-{{ $kdColor }} border-opacity-50 font-mono small">
                    {{ $kdLabel }}
                </span>
            </div>
            <div class="progress mt-3 bg-dark" style="height: 6px;">
                <div class="progress-bar bg-{{ $kdColor }}" style="width: {{ $kd }}%;"></div>
            </div>
        </div>
    </div>

    <!-- CPC & Competition -->
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Cost Per Click (CPC)</span>
            <div class="openseo-stat-num">${{ number_format($researchData['seed']['cpc'], 2) }}</div>
            <span class="text-body-secondary font-mono small mt-2 d-block">
                Paid Competition: <strong class="text-white">{{ $researchData['seed']['competition'] }}</strong> / 1.00
            </span>
        </div>
    </div>

    <!-- Search Intent -->
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Search Intent</span>
            <div class="mt-2">
                <span class="badge-intent badge-intent-{{ $researchData['seed']['search_intent'] }} fs-6 px-3 py-2">
                    {{ $researchData['seed']['search_intent'] }}
                </span>
            </div>
            <span class="text-body-secondary font-mono small mt-3 d-block">User purchase / information stage</span>
        </div>
    </div>
</div>

<!-- Keyword Ideas & Question Queries Grid -->
<div class="row g-4 mb-4">
    <!-- Variations / Suggestions -->
    <div class="col-lg-7">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <h5 class="font-cyber fw-bold text-white mb-1">Keyword Variations & Long-Tail Terms</h5>
            <p class="text-body-secondary font-mono small mb-3">Targeted queries with intent and competition data</p>

            <div class="table-responsive">
                <table class="table openseo-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            <th>Intent</th>
                            <th class="text-end">Volume</th>
                            <th class="text-end">KD%</th>
                            <th class="text-end">CPC</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($researchData['variations'] as $v)
                            <tr>
                                <td>
                                    <a href="{{ route('openseo.keywords', ['q' => $v['keyword']]) }}" class="text-white fw-bold text-decoration-none">
                                        {{ $v['keyword'] }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge-intent badge-intent-{{ $v['search_intent'] }}">
                                        {{ $v['search_intent'] }}
                                    </span>
                                </td>
                                <td class="text-end font-mono text-success">
                                    {{ number_format($v['search_volume']) }}
                                </td>
                                <td class="text-end font-mono">
                                    @php
                                        $vkdColor = $v['keyword_difficulty'] <= 30 ? 'text-success' : ($v['keyword_difficulty'] <= 60 ? 'text-warning' : 'text-danger');
                                    @endphp
                                    <span class="{{ $vkdColor }} fw-bold">{{ $v['keyword_difficulty'] }}%</span>
                                </td>
                                <td class="text-end font-mono text-body-secondary">
                                    ${{ number_format($v['cpc'], 2) }}
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('openseo.keywords.track') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="keyword" value="{{ $v['keyword'] }}">
                                        <input type="hidden" name="search_volume" value="{{ $v['search_volume'] }}">
                                        <input type="hidden" name="cpc" value="{{ $v['cpc'] }}">
                                        <input type="hidden" name="competition" value="{{ $v['competition'] }}">
                                        <input type="hidden" name="keyword_difficulty" value="{{ $v['keyword_difficulty'] }}">
                                        <input type="hidden" name="search_intent" value="{{ $v['search_intent'] }}">
                                        <button type="submit" class="btn btn-xs btn-outline-success font-mono py-1 px-2" title="Track Keyword">
                                            <i class="bi bi-plus-lg"></i> Track
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Questions ("People Also Ask") -->
    <div class="col-lg-5">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <h5 class="font-cyber fw-bold text-white mb-1">Questions ("People Also Ask")</h5>
            <p class="text-body-secondary font-mono small mb-3">Informational query ideas for blogs & FAQs</p>

            <div class="table-responsive">
                <table class="table openseo-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Question Query</th>
                            <th class="text-end">Volume</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($researchData['questions'] as $q)
                            <tr>
                                <td>
                                    <div class="text-white small fw-semibold">{{ $q['keyword'] }}</div>
                                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 font-mono" style="font-size: 0.65rem;">
                                        KD {{ $q['keyword_difficulty'] }}%
                                    </span>
                                </td>
                                <td class="text-end font-mono text-info small">
                                    {{ number_format($q['search_volume']) }}
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('openseo.keywords.track') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="keyword" value="{{ $q['keyword'] }}">
                                        <input type="hidden" name="search_volume" value="{{ $q['search_volume'] }}">
                                        <input type="hidden" name="cpc" value="{{ $q['cpc'] }}">
                                        <input type="hidden" name="keyword_difficulty" value="{{ $q['keyword_difficulty'] }}">
                                        <input type="hidden" name="search_intent" value="Informational">
                                        <button type="submit" class="btn btn-xs btn-outline-primary font-mono py-1 px-2">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Saved Project Keywords Table -->
<div class="openseo-hud-card p-3 p-md-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="font-cyber fw-bold text-white mb-0">Project Keywords ({{ $savedKeywords->total() }})</h5>
            <p class="text-body-secondary font-mono small mb-0">Keywords saved and tracked under {{ $activeProject->name }}</p>
        </div>
        <a href="{{ route('openseo.rank-tracking') }}" class="btn btn-sm btn-gradient font-mono">
            <i class="bi bi-graph-up-arrow me-1"></i> Open Rank Tracker
        </a>
    </div>

    <div class="table-responsive">
        <table class="table openseo-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Keyword</th>
                    <th>Intent</th>
                    <th class="text-end">Volume</th>
                    <th class="text-end">CPC</th>
                    <th class="text-end">KD%</th>
                    <th class="text-center">Current Rank</th>
                    <th class="text-center">Tracked</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($savedKeywords as $kw)
                    <tr>
                        <td>
                            <span class="fw-bold text-white">{{ $kw->keyword }}</span>
                            @if($kw->target_url)
                                <a href="{{ $kw->target_url }}" target="_blank" class="font-mono text-body-secondary small d-block text-truncate" style="max-width: 260px;">
                                    {{ $kw->target_url }}
                                </a>
                            @endif
                        </td>
                        <td>
                            <span class="badge-intent badge-intent-{{ $kw->search_intent }}">
                                {{ $kw->search_intent }}
                            </span>
                        </td>
                        <td class="text-end font-mono text-success">
                            {{ number_format($kw->search_volume) }}
                        </td>
                        <td class="text-end font-mono text-body-secondary">
                            ${{ number_format($kw->cpc, 2) }}
                        </td>
                        <td class="text-end font-mono">
                            <span class="text-{{ $kw->kd_badge_color }} fw-bold">{{ $kw->keyword_difficulty }}%</span>
                        </td>
                        <td class="text-center font-mono">
                            @if($kw->current_rank)
                                <span class="badge bg-dark text-success border border-success border-opacity-50 px-2.5 py-1.5 fs-6">
                                    #{{ $kw->current_rank }}
                                </span>
                            @else
                                <span class="text-body-secondary">--</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($kw->is_tracked)
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 font-mono">
                                    <i class="bi bi-check-lg"></i> Yes
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-25 text-body-secondary font-mono">No</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('openseo.keywords.untrack', $kw->id) }}" method="POST" onsubmit="return confirm('Stop tracking this keyword?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger font-mono p-1 px-2" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-body-secondary font-mono py-4">
                            No keywords saved in this project yet. Use the search bar above to discover keywords.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($savedKeywords->hasPages())
        <div class="p-3 border-top border-secondary border-opacity-25">
            {{ $savedKeywords->links() }}
        </div>
    @endif
</div>
@endsection

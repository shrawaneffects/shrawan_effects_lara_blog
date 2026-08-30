@extends('layouts.admin')

@section('title', '2026 SEO Management Dashboard')
@section('page_title', 'SEO Architecture & Health')

@section('content')
    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">2026 On-Page SEO Management</h4>
            <p class="text-body-secondary small mb-0">Audit search intent, keyword placement, SERP previews, orphan pages, structured data, and content freshness.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.seo.grader') }}" class="btn btn-outline-primary rounded-pill px-3">
                <i class="bi bi-award-fill me-1"></i> SEO Grader
            </a>
            <a href="{{ route('admin.seo.server-config') }}" class="btn btn-outline-info rounded-pill px-3">
                <i class="bi bi-hdd-network me-1"></i> Robots & .htaccess
            </a>
            <a href="{{ route('admin.seo.redirects') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-signpost-split me-1"></i> 301 Redirects
            </a>
            <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-diagram-3 me-1"></i> Sitemap.xml
            </a>
            <a href="{{ route('admin.seo.clear-cache') }}" onclick="return confirm('Purge all system and SEO caches?');" class="btn btn-outline-danger rounded-pill px-3 shadow-sm" title="Purge all caches and temporary files">
                <i class="bi bi-trash3-fill me-1"></i> Clear Cache
            </a>
            <form action="{{ route('admin.seo.run-batch-audit') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary rounded-pill px-3.5 shadow-sm">
                    <i class="bi bi-play-circle-fill me-1"></i> Batch Audit
                </button>
            </form>
        </div>
    </div>

    <!-- Performance & Cache Management Hub -->
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body glass-panel mb-4" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.05) 0%, rgba(99, 102, 241, 0.05) 100%);">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <h6 class="fw-bold mb-1 text-danger d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-charge-fill text-warning fs-5"></i> SEO Speed, Performance & Cache Manager
                </h6>
                <p class="text-body-secondary small mb-0">Clear compiled views, application queries, route caches, configuration, or re-optimize system for maximum Core Web Vitals score.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <form action="{{ route('admin.seo.clear-cache') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="type" value="all">
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm">
                        <i class="bi bi-trash3-fill me-1"></i> ⚡ Purge All System Caches
                    </button>
                </form>
                <form action="{{ route('admin.seo.optimize') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="bi bi-rocket-takeoff-fill me-1"></i> Re-Optimize System
                    </button>
                </form>
            </div>
        </div>

        <div class="row g-2 pt-2 border-top">
            <div class="col-6 col-md-3">
                <form action="{{ route('admin.seo.clear-cache') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="views">
                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill w-100 py-1.5 text-start px-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-file-earmark-code me-1 text-primary"></i> Clear Views Cache</span>
                        <i class="bi bi-chevron-right small text-muted"></i>
                    </button>
                </form>
            </div>
            <div class="col-6 col-md-3">
                <form action="{{ route('admin.seo.clear-cache') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="routes">
                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill w-100 py-1.5 text-start px-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-signpost-2 me-1 text-info"></i> Clear Route Cache</span>
                        <i class="bi bi-chevron-right small text-muted"></i>
                    </button>
                </form>
            </div>
            <div class="col-6 col-md-3">
                <form action="{{ route('admin.seo.clear-cache') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="config">
                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill w-100 py-1.5 text-start px-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-gear me-1 text-warning"></i> Clear Config Cache</span>
                        <i class="bi bi-chevron-right small text-muted"></i>
                    </button>
                </form>
            </div>
            <div class="col-6 col-md-3">
                <form action="{{ route('admin.seo.clear-cache') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="application">
                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill w-100 py-1.5 text-start px-3 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-database me-1 text-success"></i> Clear App Data Cache</span>
                        <i class="bi bi-chevron-right small text-muted"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Health Metrics Grid -->
    <div class="row g-3 mb-4">
        <!-- Average SEO Score -->
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-body-secondary small fw-semibold">Avg Site SEO Score</span>
                    <div class="p-2 rounded-3 bg-{{ $avgScore >= 75 ? 'success' : ($avgScore >= 50 ? 'warning' : 'danger') }}-subtle text-{{ $avgScore >= 75 ? 'success' : ($avgScore >= 50 ? 'warning' : 'danger') }}">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1 text-{{ $avgScore >= 75 ? 'success' : ($avgScore >= 50 ? 'warning' : 'danger') }}">{{ $avgScore }}<span class="fs-6 text-muted fw-normal">/100</span></h2>
                <small class="text-muted">Across {{ $totalPosts }} total articles</small>
            </div>
        </div>

        <!-- Critical Errors -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.seo.index', ['filter' => 'critical']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body h-100 position-relative overflow-hidden">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-body-secondary small fw-semibold">Critical SEO Errors</span>
                        <div class="p-2 rounded-3 bg-danger-subtle text-danger">
                            <i class="bi bi-shield-x fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1 text-danger">{{ $criticalCount }}</h2>
                    <small class="text-danger fw-semibold">Requires immediate attention</small>
                </div>
            </a>
        </div>

        <!-- Warnings / Recommendations -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.seo.index', ['filter' => 'needs_review']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body h-100 position-relative overflow-hidden">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-body-secondary small fw-semibold">Recommendations</span>
                        <div class="p-2 rounded-3 bg-warning-subtle text-warning">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1 text-warning">{{ $warningCount }}</h2>
                    <small class="text-muted">Optimization opportunities</small>
                </div>
            </a>
        </div>

        <!-- Orphan Articles -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.seo.index', ['filter' => 'orphan']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body h-100 position-relative overflow-hidden">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-body-secondary small fw-semibold">Orphan Articles</span>
                        <div class="p-2 rounded-3 bg-info-subtle text-info">
                            <i class="bi bi-link-45deg fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1 text-info">{{ $orphanCount }}</h2>
                    <small class="text-muted">0 incoming internal links</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Secondary Insights Badges Bar -->
    <div class="card border-0 shadow-sm p-3 rounded-4 bg-body mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="badge bg-body-secondary text-body p-2 px-3 rounded-pill border">
                    <i class="bi bi-file-earmark-text text-primary me-1"></i> Missing Titles: <strong>{{ $missingTitlesCount }}</strong>
                </span>
                <span class="badge bg-body-secondary text-body p-2 px-3 rounded-pill border">
                    <i class="bi bi-card-text text-primary me-1"></i> Missing Descriptions: <strong>{{ $missingDescriptionsCount }}</strong>
                </span>
                <span class="badge bg-body-secondary text-body p-2 px-3 rounded-pill border">
                    <i class="bi bi-copy text-warning me-1"></i> Duplicate Titles: <strong>{{ $duplicateTitlesCount }}</strong>
                </span>
                <span class="badge bg-body-secondary text-body p-2 px-3 rounded-pill border">
                    <i class="bi bi-arrows-angle-contract text-warning me-1"></i> Cannibalization Flags: <strong>{{ $cannibalizationCount }}</strong>
                </span>
                <span class="badge bg-body-secondary text-body p-2 px-3 rounded-pill border">
                    <i class="bi bi-clock-history text-danger me-1"></i> Stale Content (12m+): <strong>{{ $staleCount }}</strong>
                </span>
            </div>
            @if(request('filter'))
                <a href="{{ route('admin.seo.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-x-circle me-1"></i> Clear Filter: <span class="badge bg-primary ms-1">{{ request('filter') }}</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card border-0 shadow-sm p-3 rounded-4 bg-body mb-4">
        <form action="{{ route('admin.seo.index') }}" method="GET" class="row g-2 align-items-center">
            <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-body-tertiary"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by title, keyword, or slug..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Audit Health States</option>
                    <option value="critical" {{ request('filter') === 'critical' ? 'selected' : '' }}>Critical Issues Only</option>
                    <option value="needs_review" {{ request('filter') === 'needs_review' ? 'selected' : '' }}>Recommendations Available</option>
                    <option value="good" {{ request('filter') === 'good' ? 'selected' : '' }}>Optimized (Good)</option>
                    <option value="orphan" {{ request('filter') === 'orphan' ? 'selected' : '' }}>Orphan Pages (0 Inlinks)</option>
                    <option value="missing_meta" {{ request('filter') === 'missing_meta' ? 'selected' : '' }}>Missing Meta Descriptions</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3">Filter</button>
                @if(request()->hasAny(['search', 'category', 'filter']))
                    <a href="{{ route('admin.seo.index') }}" class="btn btn-sm btn-outline-secondary rounded-3" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- SEO Articles Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-body overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">Score</th>
                        <th>Article & SERP Title</th>
                        <th>Target Keyword & Intent</th>
                        <th>Index / Sitemap</th>
                        <th>Links</th>
                        <th>Freshness</th>
                        <th class="text-end" style="width: 130px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $p)
                        @php
                            $s = $p->seo;
                            $score = $s ? $s->seo_score : 0;
                            $status = $s ? $s->seo_status : 'needs_review';
                        @endphp
                        <tr>
                            <!-- Score Gauge Badge -->
                            <td>
                                <span class="badge rounded-pill fs-6 px-2.5 py-1.5 bg-{{ $score >= 75 ? 'success' : ($score >= 50 ? 'warning' : 'danger') }}">
                                    {{ $score }}
                                </span>
                            </td>

                            <!-- Article Info -->
                            <td>
                                <div class="fw-bold text-truncate" style="max-width: 320px;">
                                    <a href="{{ route('admin.posts.edit', $p->id) }}" class="text-decoration-none text-body">
                                        {{ $p->title }}
                                    </a>
                                </div>
                                <small class="text-body-secondary d-block text-truncate" style="max-width: 320px; font-size: 0.78rem;">
                                    {{ $s && $s->seo_title ? $s->seo_title : ($p->meta_title ?: 'No custom SEO title set') }}
                                </small>
                            </td>

                            <!-- Target Keyword & Search Intent -->
                            <td>
                                @if($s && $s->primary_keyword)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">
                                        <i class="bi bi-key-fill me-1"></i>{{ $s->primary_keyword }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary mb-1">Not set</span>
                                @endif
                                <div style="font-size: 0.75rem;" class="text-muted text-capitalize">
                                    {{ $s->search_intent ?? 'informational' }}
                                </div>
                            </td>

                            <!-- Robots & Sitemap -->
                            <td>
                                <div class="d-flex flex-column gap-1" style="font-size: 0.78rem;">
                                    <span>
                                        @if(!$s || $s->robots_index)
                                            <i class="bi bi-check-circle-fill text-success me-1"></i>Indexable
                                        @else
                                            <i class="bi bi-slash-circle-fill text-danger me-1"></i>Noindex
                                        @endif
                                    </span>
                                    <span>
                                        @if(!$s || $s->include_in_sitemap)
                                            <i class="bi bi-check2 text-primary me-1"></i>In Sitemap
                                        @else
                                            <i class="bi bi-x text-muted me-1"></i>Excluded
                                        @endif
                                    </span>
                                </div>
                            </td>

                            <!-- Inlinks, Outlinks & Orphan Status (Excluding Media) -->
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center gap-1">
                                        @if($p->incoming_links_count > 0)
                                            <span class="badge bg-success-subtle text-success" title="{{ $p->incoming_links_count }} internal articles link here">
                                                <i class="bi bi-box-arrow-in-down-right me-0.5"></i> {{ $p->incoming_links_count }} In
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary" title="0 inbound links">
                                                <i class="bi bi-box-arrow-in-down-right me-0.5"></i> 0 In
                                            </span>
                                        @endif

                                        <span class="badge bg-body-secondary text-body border" title="{{ $p->outgoing_links_count }} outbound links (excluding media assets)">
                                            <i class="bi bi-box-arrow-up-right me-0.5"></i> {{ $p->outgoing_links_count }} Out
                                        </span>
                                    </div>

                                    @if($p->is_orphan)
                                        <div>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle" title="Orphan Article: 0 other blog posts link to this guide">
                                                <i class="bi bi-exclamation-triangle-fill me-0.5"></i> Orphan
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Freshness -->
                            <td>
                                <span class="badge bg-{{ $p->freshness['color'] }}-subtle text-{{ $p->freshness['color'] }}" title="{{ implode(' ', $p->freshness['recommendations'] ?? []) }}">
                                    {{ $p->freshness['label'] }}
                                </span>
                            </td>

                            <!-- Action -->
                            <td class="text-end">
                                <a href="{{ route('admin.posts.edit', $p->id) }}#seoManagementPanel" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-pencil me-1"></i> Optimize
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-check fs-1 d-block mb-2 text-muted"></i>
                                No articles match the selected SEO filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination & Records Meta Footer -->
        <div class="card-footer bg-body-tertiary border-top py-3 px-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="small text-body-secondary">
                    @if($posts->total() > 0)
                        Showing <span class="fw-bold text-body">{{ $posts->firstItem() }}</span> to <span class="fw-bold text-body">{{ $posts->lastItem() }}</span> of <span class="fw-bold text-body">{{ number_format($posts->total()) }}</span> articles
                    @else
                        No articles to display
                    @endif
                </div>

                <div class="d-flex flex-wrap align-items-center gap-3">
                    <form action="{{ route('admin.seo.index') }}" method="GET" class="d-flex align-items-center gap-2">
                        @foreach(request()->except(['page', 'per_page']) as $k => $v)
                            @if(is_array($v))
                                @foreach($v as $subV)
                                    <input type="hidden" name="{{ $k }}[]" value="{{ $subV }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                        <label for="seoPerPageSelect" class="small text-body-secondary text-nowrap mb-0">Per Page:</label>
                        <select name="per_page" id="seoPerPageSelect" class="form-select form-select-sm rounded-pill" style="width: 78px;" onchange="this.form.submit()">
                            <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </form>

                    @if($posts->hasPages())
                        <div class="pagination-wrapper mb-0">
                            {{ $posts->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
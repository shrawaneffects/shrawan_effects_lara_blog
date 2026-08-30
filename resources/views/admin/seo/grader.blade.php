@extends('layouts.admin')

@section('title', 'On-Page SEO Grader & Link Auditor')
@section('page_title', 'On-Page SEO Grader')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/trending-seo.css') }}">
<style>
    .grader-hero {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(217, 70, 239, 0.08) 50%, rgba(6, 182, 212, 0.12) 100%);
        border: 1px solid rgba(99, 102, 241, 0.2);
    }
    .filter-btn.active {
        background: #6366f1 !important;
        color: #fff !important;
        border-color: #6366f1 !important;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
    }
</style>
@endpush

@section('content')
    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h4 class="fw-bold mb-0">On-Page SEO Grader & Link Auditor</h4>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">2026 Engine</span>
            </div>
            <p class="text-body-secondary small mb-0">Grade internal & external linking architecture, follow/nofollow distribution, anchor text diversity, and on-page signals.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.seo.index') }}" class="btn btn-outline-secondary rounded-pill px-3.5">
                <i class="bi bi-arrow-left me-1"></i> SEO Health Suite
            </a>
            <a href="{{ route('admin.seo.redirects') }}" class="btn btn-outline-primary rounded-pill px-3.5">
                <i class="bi bi-signpost-split me-1"></i> 301 Redirects
            </a>
        </div>
    </div>

    <!-- Article Switcher Bar -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 glass-panel">
        <form action="{{ route('admin.seo.grader') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-8 col-lg-9">
                <div class="input-group">
                    <span class="input-group-text bg-body-tertiary border-0"><i class="bi bi-file-earmark-text text-primary"></i></span>
                    <select name="post_id" class="form-select border-0 bg-body-tertiary" onchange="this.form.submit()">
                        @foreach($posts as $p)
                            <option value="{{ $p->id }}" {{ (isset($selectedPost) && $selectedPost->id === $p->id) ? 'selected' : '' }}>
                                {{ $p->title }} (Score: {{ $p->seo->seo_score ?? 0 }}/100) — {{ $p->status }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4 col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill w-100 shadow-sm">
                    <i class="bi bi-award-fill me-1"></i> Grade Article
                </button>
                @if(isset($selectedPost))
                    <a href="{{ route('admin.posts.edit', $selectedPost->id) }}" class="btn btn-outline-secondary rounded-circle" title="Edit Post">
                        <i class="bi bi-pencil"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if(isset($gradeResult))
        @php
            $linkGrader = $gradeResult['link_grader'];
            $audit = $gradeResult['audit'];
            $score = $gradeResult['total_score'];
            $grade = $gradeResult['letter_grade'];
            $gradeColor = $gradeResult['grade_color'];
            $dashOffset = round(377 - (377 * ($score / 100)));
        @endphp

        <!-- HERO GRADE & SCORECARD -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 grader-hero glass-panel">
            <div class="row g-4 align-items-center">
                <!-- Circular Grade Gauge -->
                <div class="col-12 col-md-4 col-lg-3 text-center border-end-md">
                    <div class="grade-ring-wrapper mb-2">
                        <svg class="grade-ring-svg">
                            <circle class="grade-ring-circle-bg" cx="70" cy="70" r="60"></circle>
                            <circle class="grade-ring-circle-val" cx="70" cy="70" r="60" style="stroke-dashoffset: {{ $dashOffset }}; stroke: var(--grade-{{ strtolower(str_replace('+', '-plus', $grade)) }}, #6366f1);"></circle>
                        </svg>
                        <div class="grade-letter-text text-{{ $gradeColor }}">{{ $grade }}</div>
                    </div>
                    <h5 class="fw-bold mb-0 text-{{ $gradeColor }}">{{ $gradeResult['grade_label'] }}</h5>
                    <small class="text-body-secondary fw-semibold">Overall Quality Score: <strong>{{ $score }}/100</strong></small>
                </div>

                <!-- Fast Summary Cards -->
                <div class="col-12 col-md-8 col-lg-9">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mb-1">
                                {{ $selectedPost->category ? $selectedPost->category->name : 'General' }}
                            </span>
                            <h4 class="fw-bold mb-0">{{ $selectedPost->title }}</h4>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-body text-body border p-2 px-3 rounded-pill shadow-sm">
                                <i class="bi bi-fonts me-1 text-primary"></i> {{ $gradeResult['summary']['word_count'] }} words
                            </span>
                        </div>
                    </div>

                    <!-- 6 Category Radial / Progress Gauges -->
                    <div class="row g-2">
                        @foreach($gradeResult['categories'] as $catKey => $cat)
                            <div class="col-6 col-sm-4 col-lg-2">
                                <div class="p-2.5 rounded-3 bg-body border text-center h-100 shadow-sm">
                                    <small class="text-body-secondary d-block text-capitalize" style="font-size: 0.75rem;">
                                        {{ str_replace('_', ' ', $catKey) }}
                                    </small>
                                    <span class="badge fs-6 my-1 bg-{{ $cat['color'] }}-subtle text-{{ $cat['color'] }} rounded-pill px-2.5">
                                        {{ $cat['grade'] }}
                                    </span>
                                    <div class="progress mt-1" style="height: 3px;">
                                        <div class="progress-bar bg-{{ $cat['color'] }}" style="width: {{ $cat['percentage'] }}%;"></div>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">{{ $cat['percentage'] }}%</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- LINKING AUDIT METRICS GRID (Internal & External, Follow & Nofollow) -->
        <div class="row g-3 mb-4">
            <!-- 1. Internal Links Total & Follow/Nofollow -->
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm p-3.5 rounded-4 bg-body h-100 glass-panel">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-body-secondary small fw-semibold">Internal Links</span>
                        <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                            <i class="bi bi-link-45deg fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-2 text-primary">{{ $linkGrader['internal']['total'] }}</h3>
                    <div class="d-flex flex-column gap-1 small">
                        <div class="d-flex justify-content-between">
                            <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Dofollow:</span>
                            <strong class="text-success">{{ $linkGrader['internal']['dofollow'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-{{ $linkGrader['internal']['nofollow'] > 0 ? 'warning' : 'muted' }}"><i class="bi bi-slash-circle me-1"></i> Nofollow:</span>
                            <strong class="text-{{ $linkGrader['internal']['nofollow'] > 0 ? 'warning' : 'muted' }}">{{ $linkGrader['internal']['nofollow'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. External Links Total & Distribution -->
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm p-3.5 rounded-4 bg-body h-100 glass-panel">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-body-secondary small fw-semibold">External Links</span>
                        <div class="p-2 rounded-3 bg-info-subtle text-info">
                            <i class="bi bi-box-arrow-up-right fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-2 text-info">{{ $linkGrader['external']['total'] }}</h3>
                    <div class="d-flex flex-column gap-1 small">
                        <div class="d-flex justify-content-between">
                            <span class="text-success"><i class="bi bi-check2 me-1"></i> Dofollow:</span>
                            <strong>{{ $linkGrader['external']['dofollow'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-warning"><i class="bi bi-shield-shaded me-1"></i> Nofollow/Rel:</span>
                            <strong>{{ $linkGrader['external']['nofollow'] + $linkGrader['external']['sponsored'] + $linkGrader['external']['ugc'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Link Density & Ratio -->
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm p-3.5 rounded-4 bg-body h-100 glass-panel">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-body-secondary small fw-semibold">Link Density</span>
                        <div class="p-2 rounded-3 bg-success-subtle text-success">
                            <i class="bi bi-speedometer2 fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-2 text-success">{{ $linkGrader['density'] }} <span class="fs-6 text-muted fw-normal">/100 words</span></h3>
                    <small class="text-body-secondary d-block" style="font-size: 0.78rem;">
                        {{ $linkGrader['density'] >= 1.0 && $linkGrader['density'] <= 3.5 ? 'Optimal link distribution' : 'Consider adjusting link frequency' }}
                    </small>
                </div>
            </div>

            <!-- 4. Link Quality & Issues Flag -->
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm p-3.5 rounded-4 bg-body h-100 glass-panel">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-body-secondary small fw-semibold">Link Architecture Score</span>
                        <div class="p-2 rounded-3 bg-{{ $linkGrader['link_score'] >= 75 ? 'success' : ($linkGrader['link_score'] >= 50 ? 'warning' : 'danger') }}-subtle text-{{ $linkGrader['link_score'] >= 75 ? 'success' : ($linkGrader['link_score'] >= 50 ? 'warning' : 'danger') }}">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-2 text-{{ $linkGrader['link_score'] >= 75 ? 'success' : ($linkGrader['link_score'] >= 50 ? 'warning' : 'danger') }}">{{ $linkGrader['link_score'] }}<span class="fs-6 text-muted fw-normal">/100</span></h3>
                    <small class="text-body-secondary d-block" style="font-size: 0.78rem;">
                        {{ $linkGrader['generic_anchors_count'] }} generic anchors • {{ $linkGrader['security_warnings_count'] }} security flags
                    </small>
                </div>
            </div>
        </div>

        <!-- DETAILED LINKS INVENTORY TABLE & REL FILTER -->
        <div class="card border-0 shadow-sm rounded-4 bg-body overflow-hidden mb-4 glass-panel">
            <div class="p-4 border-bottom bg-body-tertiary d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-table me-2 text-primary"></i> Content Links Inventory & Attribute Inspector</h5>
                    <small class="text-body-secondary">Examine every hyperlink inside the article content with its anchor text, destination, rel flags, and PageRank flow.</small>
                </div>

                <!-- Filter Pills -->
                <div class="btn-group btn-group-sm" role="group" id="linkFilterGroup">
                    <button type="button" class="btn btn-outline-secondary filter-btn active" onclick="filterLinks('all', this)">All ({{ count($linkGrader['links']) }})</button>
                    <button type="button" class="btn btn-outline-secondary filter-btn" onclick="filterLinks('internal', this)">Internal ({{ $linkGrader['internal']['total'] }})</button>
                    <button type="button" class="btn btn-outline-secondary filter-btn" onclick="filterLinks('external', this)">External ({{ $linkGrader['external']['total'] }})</button>
                    <button type="button" class="btn btn-outline-secondary filter-btn" onclick="filterLinks('dofollow', this)">Follow ({{ $linkGrader['internal']['dofollow'] + $linkGrader['external']['dofollow'] }})</button>
                    <button type="button" class="btn btn-outline-secondary filter-btn" onclick="filterLinks('nofollow', this)">Nofollow/Rel ({{ $linkGrader['internal']['nofollow'] + $linkGrader['external']['nofollow'] + $linkGrader['external']['sponsored'] + $linkGrader['external']['ugc'] }})</button>
                    <button type="button" class="btn btn-outline-secondary filter-btn" onclick="filterLinks('issues', this)">With Issues</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="linksInventoryTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 250px;">Anchor Text</th>
                            <th>Destination URL (href)</th>
                            <th>Scope</th>
                            <th>Rel Attribute</th>
                            <th>Target</th>
                            <th>Health & Recommendations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($linkGrader['links'] as $link)
                            @php
                                $hasIssues = count($link['issues']) > 0;
                                $rowClass = '';
                                if ($link['type'] === 'internal') $rowClass .= ' link-internal';
                                if ($link['type'] === 'external') $rowClass .= ' link-external';
                                if ($link['is_dofollow']) $rowClass .= ' link-dofollow';
                                if ($link['is_nofollow'] || $link['is_sponsored'] || $link['is_ugc']) $rowClass .= ' link-nofollow';
                                if ($hasIssues) $rowClass .= ' link-issues';
                            @endphp
                            <tr class="link-row {{ $rowClass }}">
                                <!-- Anchor Text -->
                                <td>
                                    <div class="fw-semibold text-truncate" style="max-width: 240px;" title="{{ $link['anchor'] }}">
                                        {{ $link['anchor'] }}
                                    </div>
                                    @if($link['is_generic'])
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.7rem;">
                                            Generic Anchor
                                        </span>
                                    @endif
                                </td>

                                <!-- Target URL -->
                                <td>
                                    <a href="{{ $link['href'] }}" target="_blank" class="text-decoration-none text-truncate d-inline-block" style="max-width: 320px;" title="{{ $link['href'] }}">
                                        <code>{{ $link['href'] }}</code>
                                    </a>
                                </td>

                                <!-- Internal vs External -->
                                <td>
                                    @if($link['type'] === 'internal')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                            <i class="bi bi-link-45deg me-0.5"></i> Internal
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                            <i class="bi bi-box-arrow-up-right me-0.5"></i> External
                                        </span>
                                    @endif
                                </td>

                                <!-- Rel Attribute -->
                                <td>
                                    @if($link['is_sponsored'])
                                        <span class="badge badge-sponsored rounded-pill px-2.5">rel="sponsored"</span>
                                    @elseif($link['is_ugc'])
                                        <span class="badge badge-ugc rounded-pill px-2.5">rel="ugc"</span>
                                    @elseif($link['is_nofollow'])
                                        <span class="badge badge-nofollow rounded-pill px-2.5">rel="nofollow"</span>
                                    @else
                                        <span class="badge badge-follow rounded-pill px-2.5">dofollow</span>
                                    @endif
                                </td>

                                <!-- Target -->
                                <td>
                                    <span class="badge bg-body text-body border" style="font-size: 0.75rem;">
                                        {{ $link['target'] }}
                                    </span>
                                </td>

                                <!-- Health Status -->
                                <td>
                                    @if(count($link['issues']) === 0)
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i> Clean
                                        </span>
                                    @else
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($link['issues'] as $iss)
                                                <small class="text-{{ $iss['severity'] === 'critical' ? 'danger' : ($iss['severity'] === 'warning' ? 'warning' : 'info') }} fw-semibold" style="font-size: 0.76rem;">
                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $iss['message'] }}
                                                </small>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-link-45deg fs-1 d-block mb-2 text-muted"></i>
                                    No hyperlinks detected in this article content.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ACTIONABLE SEO CHECKLIST -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-body glass-panel">
            <h5 class="fw-bold mb-3"><i class="bi bi-check2-circle me-2 text-success"></i> Priority Actionable Fixes & Audit Rules</h5>
            <div class="d-flex flex-column gap-2">
                @foreach($audit['rules'] as $rule)
                    @php
                        $sev = $rule['severity'];
                        $icon = $sev === 'critical' ? 'bi-x-circle-fill text-danger' : ($sev === 'warning' ? 'bi-exclamation-triangle-fill text-warning' : 'bi-check-circle-fill text-success');
                        $bg = $sev === 'critical' ? 'bg-danger-subtle border border-danger-subtle' : ($sev === 'warning' ? 'bg-warning-subtle border border-warning-subtle' : 'bg-body border');
                    @endphp
                    <div class="p-3 rounded-3 {{ $bg }} d-flex align-items-start gap-3">
                        <i class="bi {{ $icon }} fs-5 flex-shrink-0 mt-0.5"></i>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-1 small">{{ $rule['message'] }}</h6>
                                @if($rule['deduction'] > 0)
                                    <span class="badge bg-danger-subtle text-danger">-{{ $rule['deduction'] }} pts</span>
                                @endif
                            </div>
                            <p class="text-body-secondary mb-0" style="font-size: 0.82rem;">{{ $rule['recommendation'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script src="{{ asset('js/trending-background.js') }}"></script>
<script>
    function filterLinks(type, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const rows = document.querySelectorAll('.link-row');
        rows.forEach(row => {
            if (type === 'all') {
                row.style.display = '';
            } else if (type === 'internal') {
                row.style.display = row.classList.contains('link-internal') ? '' : 'none';
            } else if (type === 'external') {
                row.style.display = row.classList.contains('link-external') ? '' : 'none';
            } else if (type === 'dofollow') {
                row.style.display = row.classList.contains('link-dofollow') ? '' : 'none';
            } else if (type === 'nofollow') {
                row.style.display = row.classList.contains('link-nofollow') ? '' : 'none';
            } else if (type === 'issues') {
                row.style.display = row.classList.contains('link-issues') ? '' : 'none';
            }
        });
    }
</script>
@endpush
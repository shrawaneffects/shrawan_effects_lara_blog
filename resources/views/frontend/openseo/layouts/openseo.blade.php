@extends('layouts.app')

@push('styles')
<style>
    .openseo-hud-card {
        background: rgba(9, 18, 6, 0.75);
        border: 1px solid rgba(0, 255, 102, 0.22);
        border-radius: 14px;
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    [data-bs-theme="light"] .openseo-hud-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
    }
    .openseo-hud-card:hover {
        border-color: rgba(0, 255, 102, 0.45);
        box-shadow: 0 0 20px rgba(0, 255, 102, 0.12);
    }
    [data-bs-theme="light"] .openseo-hud-card:hover {
        border-color: #15803d !important;
        box-shadow: 0 6px 20px rgba(21, 128, 61, 0.08) !important;
    }

    .openseo-stat-num {
        font-family: var(--font-cyber);
        font-weight: 800;
        font-size: 2.1rem;
        line-height: 1.1;
        color: #00ff66;
        text-shadow: 0 0 15px rgba(0, 255, 102, 0.4);
    }
    [data-bs-theme="light"] .openseo-stat-num {
        color: #15803d !important;
        text-shadow: none !important;
    }

    .openseo-nav-tab {
        font-family: var(--font-mono);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 0.65rem 1.15rem;
        border-radius: 10px;
        color: #94a3b8;
        border: 1px solid transparent;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        white-space: nowrap;
        text-decoration: none;
    }
    .openseo-nav-tab:hover {
        color: #00ff66;
        background: rgba(33, 54, 8, 0.35);
        border-color: rgba(0, 255, 102, 0.2);
    }
    [data-bs-theme="light"] .openseo-nav-tab:hover {
        color: #15803d;
        background: #f0fdf4;
        border-color: #86efac;
    }
    .openseo-nav-tab.active {
        color: #050804 !important;
        background: linear-gradient(135deg, #00ff66 0%, #00f0ff 100%) !important;
        font-weight: 700;
        box-shadow: 0 0 18px rgba(0, 255, 102, 0.35);
    }
    [data-bs-theme="light"] .openseo-nav-tab.active {
        color: #ffffff !important;
        background: linear-gradient(135deg, #16a34a 0%, #0284c7 100%) !important;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25) !important;
    }

    .openseo-table {
        font-size: 0.88rem;
        font-family: var(--font-main);
    }
    .openseo-table th {
        font-family: var(--font-mono);
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        background: rgba(18, 30, 5, 0.6) !important;
        border-color: rgba(0, 255, 102, 0.15);
        padding: 0.85rem 1rem;
    }
    [data-bs-theme="light"] .openseo-table th {
        background: #f8fafc !important;
        color: #475569 !important;
        border-color: #e2e8f0 !important;
    }
    .openseo-table td {
        vertical-align: middle;
        padding: 0.85rem 1rem;
        border-color: rgba(0, 255, 102, 0.1);
    }
    [data-bs-theme="light"] .openseo-table td {
        border-color: #f1f5f9;
    }

    .badge-intent {
        font-family: var(--font-mono);
        font-size: 0.68rem;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        text-transform: uppercase;
        font-weight: 700;
    }
    .badge-intent-Informational { background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.4); }
    .badge-intent-Commercial { background: rgba(168, 85, 247, 0.2); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.4); }
    .badge-intent-Transactional { background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.4); }
    .badge-intent-Navigational { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); }

    .rank-change-badge {
        font-family: var(--font-mono);
        font-size: 0.74rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
    }
    .rank-change-up { color: #00ff66; background: rgba(0, 255, 102, 0.12); border: 1px solid rgba(0, 255, 102, 0.3); }
    .rank-change-down { color: #f43f5e; background: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.3); }
    .rank-change-same { color: #94a3b8; background: rgba(148, 163, 184, 0.1); }
</style>
@endpush

@section('content')
<div class="py-4">
    <div class="container-fluid px-lg-5">
        <!-- Top HUD Header -->
        <div class="openseo-hud-card p-3 p-md-4 mb-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2.5 rounded-3 bg-dark border border-success border-opacity-50 text-success fs-3 shadow">
                        <i class="bi bi-radar"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h2 class="h4 font-cyber fw-bold mb-0 text-gradient">
                                OpenSEO Engine
                            </h2>
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 font-mono text-uppercase" style="font-size: 0.65rem;">
                                v0.1.26 • Full Suite
                            </span>
                            <span class="badge bg-dark text-info border border-info border-opacity-50 font-mono" style="font-size: 0.65rem;">
                                <span class="cyber-beacon me-1"></span>CONNECTED
                            </span>
                        </div>
                        <p class="text-body-secondary small mb-0 mt-1">
                            Self-hosted SEO platform powered by DataForSEO algorithms • Active Domain:
                            <span class="text-white fw-bold font-mono">{{ $activeProject->domain ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Right Side: Project Selector & Action Buttons -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @php
                        $allProjects = \App\Models\OpenSeo\OpenSeoProject::all();
                    @endphp
                    @if($allProjects->count() > 1)
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-success dropdown-toggle font-mono" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-folder2-open me-1"></i> Switch Project ({{ $activeProject->name ?? 'Default' }})
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow">
                                @foreach($allProjects as $p)
                                    <li>
                                        <a class="dropdown-item font-mono {{ ($activeProject && $activeProject->id === $p->id) ? 'active' : '' }}" href="{{ route('openseo.projects.switch', $p->id) }}">
                                            <i class="bi bi-globe2 me-2"></i> {{ $p->name }} ({{ $p->domain }})
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item font-mono text-success" href="{{ route('openseo.projects.index') }}">
                                        <i class="bi bi-plus-circle me-1"></i> Manage All Projects
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('openseo.projects.index') }}" class="btn btn-sm btn-outline-secondary font-mono">
                            <i class="bi bi-folder-plus me-1"></i> Add Project
                        </a>
                    @endif

                    <a href="{{ route('openseo.reports.export', 'keywords') }}" class="btn btn-sm btn-tactical font-mono">
                        <i class="bi bi-download me-1"></i> Export CSV
                    </a>
                </div>
            </div>

            <hr class="border-secondary border-opacity-25 my-3">

            <!-- Sub Navigation Tabs Bar -->
            <div class="overflow-auto pb-1">
                <div class="d-flex align-items-center gap-1.5 flex-nowrap">
                    <a href="{{ route('openseo.domain') }}" class="openseo-nav-tab {{ request()->routeIs('openseo.domain') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Domain Overview
                    </a>
                    <a href="{{ route('openseo.keywords') }}" class="openseo-nav-tab {{ request()->routeIs('openseo.keywords') ? 'active' : '' }}">
                        <i class="bi bi-key"></i> Keyword Research
                    </a>
                    <a href="{{ route('openseo.rank-tracking') }}" class="openseo-nav-tab {{ request()->routeIs('openseo.rank-tracking') ? 'active' : '' }}">
                        <i class="bi bi-graph-up-arrow"></i> Rank Tracker
                    </a>
                    <a href="{{ route('openseo.competitors') }}" class="openseo-nav-tab {{ request()->routeIs('openseo.competitors') ? 'active' : '' }}">
                        <i class="bi bi-crosshair"></i> Competitor Insights
                    </a>
                    <a href="{{ route('openseo.backlinks') }}" class="openseo-nav-tab {{ request()->routeIs('openseo.backlinks') ? 'active' : '' }}">
                        <i class="bi bi-link-45deg"></i> Backlinks
                    </a>
                    <a href="{{ route('openseo.audit') }}" class="openseo-nav-tab {{ request()->routeIs('openseo.audit') ? 'active' : '' }}">
                        <i class="bi bi-shield-check"></i> Site Audit
                    </a>
                    <a href="{{ route('openseo.ai-visibility') }}" class="openseo-nav-tab {{ request()->routeIs('openseo.ai-visibility') ? 'active' : '' }}">
                        <i class="bi bi-robot"></i> AI Visibility
                    </a>
                    <a href="{{ route('openseo.local-seo') }}" class="openseo-nav-tab {{ request()->routeIs('openseo.local-seo') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt"></i> Local Maps
                    </a>
                    <a href="{{ route('openseo.search-performance') }}" class="openseo-nav-tab {{ request()->routeIs('openseo.search-performance') ? 'active' : '' }}">
                        <i class="bi bi-google"></i> Search Console
                    </a>
                    <a href="{{ route('openseo.reports') }}" class="openseo-nav-tab {{ request()->routeIs('openseo.reports') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Reports
                    </a>
                    <a href="{{ route('openseo.settings') }}" class="openseo-nav-tab {{ request()->routeIs('openseo.settings') ? 'active' : '' }}">
                        <i class="bi bi-sliders"></i> Settings
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-success border-opacity-50 bg-success bg-opacity-10 text-success font-mono small d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-info border-opacity-50 bg-info bg-opacity-10 text-info font-mono small d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-info-circle-fill fs-5"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Subview Content Slot -->
        @yield('openseo_content')
    </div>
</div>
@endsection

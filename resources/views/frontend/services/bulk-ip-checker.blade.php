@extends('layouts.app')

@php
    $bulkMaxBatchSize = $maxBatchSize ?? 50;
    $bulkDefaultBatchSize = min($recommendedBatchSize ?? 30, $bulkMaxBatchSize);
    $bulkDefaultConcurrency = $recommendedConcurrency ?? 3;
    $bulkResolveUrl = rtrim($resolveUrl ?? (\Illuminate\Support\Facades\Route::has('services.bulk-ip-checker.resolve')
        ? route('services.bulk-ip-checker.resolve', [], false)
        : '/services/bulk-website-ip-checker/resolve'), '/');
    $bulkSampleDomains = $sampleDomains ?? [
        'shrawaneffects.com',
        'www.shrawaneffects.com',
        'https://google.com/search?q=dns',
        'cloudflare.com',
        'github.com',
        'amazon.com',
        'microsoft.com',
        'digitalocean.com',
        'ipv6.google.com',
        'nonexistent-domain-check-example.invalid',
    ];
    $bulkIpTypeLabels = $ipTypeLabels ?? [
        'cdn_proxy' => 'CDN / Proxy',
        'cloud' => 'Cloud Hyperscaler',
        'datacenter' => 'VPS / Datacenter',
        'hosting_isp' => 'Hosting / ISP',
        'private_bogon' => 'Private / Bogon',
        'unresolved' => 'Unresolved',
    ];
    $jsVersion = file_exists(public_path('js/bulk-ip-checker.js')) ? filemtime(public_path('js/bulk-ip-checker.js')) : '1';
@endphp

@section('title', 'Bulk Website IP Checker & Class-C Subnet Analyzer (10,000+ Domains) - Shrawan Effects')
@section('meta_description', 'Free Bulk Website IP Checker supporting 10,000+ domains. Resolve IPv4 & IPv6, detect CDN/Proxy/Cloud/Hosting IP types, find Same-IP & Class-C (/24) shared hosting footprints, and export CSV/JSON.')
@section('canonical_url', \Illuminate\Support\Facades\Route::has('services.bulk-ip-checker') ? route('services.bulk-ip-checker') : url('/services/bulk-website-ip-checker'))

@push('styles')
<style>
    .bulk-hud-panel {
        background: rgba(9, 18, 6, 0.82);
        border: 1px solid rgba(0, 255, 102, 0.22);
        border-radius: 16px;
        backdrop-filter: blur(12px);
    }
    [data-bs-theme="light"] .bulk-hud-panel {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 6px 22px rgba(15, 23, 42, 0.05);
    }

    .bulk-dropzone {
        border: 1.5px dashed rgba(0, 255, 102, 0.35);
        border-radius: 14px;
        background: rgba(5, 8, 4, 0.55);
        transition: border-color 0.25s ease, background-color 0.25s ease, box-shadow 0.25s ease;
    }
    [data-bs-theme="light"] .bulk-dropzone {
        border-color: #94a3b8;
        background: #f8fafc;
    }
    .bulk-dropzone.bulk-drop-active {
        border-color: #00ff66 !important;
        background: rgba(0, 255, 102, 0.08) !important;
        box-shadow: 0 0 20px rgba(0, 255, 102, 0.22);
    }

    .bulk-textarea {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        resize: vertical;
        min-height: 220px;
        font-size: 1rem;
        line-height: 1.55;
    }

    .bulk-kpi-card {
        background: rgba(9, 18, 6, 0.78);
        border: 1px solid rgba(0, 255, 102, 0.2);
        border-radius: 14px;
        transition: border-color 0.25s ease, transform 0.25s ease;
    }
    .bulk-kpi-card:hover {
        border-color: rgba(0, 255, 102, 0.45);
        transform: translateY(-2px);
    }
    [data-bs-theme="light"] .bulk-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }

    .bulk-kpi-num {
        font-family: var(--font-cyber);
        font-weight: 800;
        font-size: 1.85rem;
        line-height: 1.1;
        color: #00ff66;
    }
    [data-bs-theme="light"] .bulk-kpi-num {
        color: #15803d;
    }

    .bulk-nav-tab {
        font-family: var(--font-main);
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.65rem 1.2rem;
        border-radius: 10px;
        border: 1px solid rgba(0, 255, 102, 0.2);
        background: rgba(9, 18, 6, 0.5);
        color: #94a3b8;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .bulk-nav-tab:hover {
        color: #00ff66;
        border-color: rgba(0, 255, 102, 0.5);
    }
    .bulk-nav-tab.active {
        color: #050804 !important;
        background: linear-gradient(135deg, #00ff66 0%, #00f0ff 100%) !important;
        border-color: transparent;
        font-weight: 700;
        box-shadow: 0 0 18px rgba(0, 255, 102, 0.3);
    }
    [data-bs-theme="light"] .bulk-nav-tab {
        background: #f1f5f9;
        color: #334155;
        border-color: #cbd5e1;
    }
    [data-bs-theme="light"] .bulk-nav-tab.active {
        color: #ffffff !important;
        background: linear-gradient(135deg, #16a34a 0%, #0284c7 100%) !important;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
    }

    .bulk-filter-chip {
        border: 1px solid rgba(0, 255, 102, 0.25);
        background: rgba(9, 18, 6, 0.5);
        color: #cbd5e1;
        border-radius: 999px;
        padding: 0.35rem 0.85rem;
        font-family: var(--font-main);
        font-size: 1rem;
        transition: all 0.2s ease;
    }
    .bulk-filter-chip:hover {
        border-color: #00ff66;
        color: #00ff66;
    }
    .bulk-filter-chip.active {
        background: #00ff66;
        color: #050804 !important;
        border-color: #00ff66;
        font-weight: 700;
    }
    [data-bs-theme="light"] .bulk-filter-chip {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #334155;
    }
    [data-bs-theme="light"] .bulk-filter-chip.active {
        background: #15803d;
        border-color: #15803d;
        color: #ffffff !important;
    }

    .bulk-table {
        font-family: var(--font-main);
        font-size: 1rem;
    }
    .bulk-table th {
        font-family: var(--font-main);
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
        background: rgba(18, 30, 5, 0.85) !important;
        color: #94a3b8;
        border-color: rgba(0, 255, 102, 0.16);
        padding: 0.65rem 0.5rem;
        user-select: none;
    }
    .bulk-table th[data-sort] {
        cursor: pointer;
    }
    .bulk-table th[data-sort]:hover {
        color: #00ff66;
    }
    .bulk-table td {
        padding: 0.55rem 0.5rem;
        vertical-align: middle;
        border-color: rgba(0, 255, 102, 0.1);
    }
    [data-bs-theme="light"] .bulk-table th {
        background: #f1f5f9 !important;
        color: #334155 !important;
        border-color: #e2e8f0 !important;
    }
    [data-bs-theme="light"] .bulk-table td {
        border-color: #e2e8f0 !important;
    }

    .bulk-row-shared {
        background-color: rgba(245, 158, 11, 0.05) !important;
    }
    .bulk-row-unresolved {
        opacity: 0.82;
    }
    .bulk-subtext {
        font-size: 1rem;
        max-width: 240px;
    }

    /* IP Type Badges */
    .bulk-type-badge {
        font-size: 1rem;
        padding: 0.3rem 0.55rem;
        border-radius: 6px;
        white-space: nowrap;
    }
    .bulk-badge-cdn { background: rgba(0, 240, 255, 0.14); color: #00f0ff; border: 1px solid rgba(0, 240, 255, 0.4); }
    .bulk-badge-cloud { background: rgba(59, 130, 246, 0.16); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.4); }
    .bulk-badge-dc { background: rgba(168, 85, 247, 0.16); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.4); }
    .bulk-badge-hosting { background: rgba(0, 255, 102, 0.14); color: #00ff66; border: 1px solid rgba(0, 255, 102, 0.4); }
    .bulk-badge-private { background: rgba(245, 158, 11, 0.16); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); }
    .bulk-badge-unresolved { background: rgba(244, 63, 94, 0.14); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.35); }

    [data-bs-theme="light"] .bulk-badge-cdn { background: #ecfeff; color: #0e7490; border-color: #67e8f9; }
    [data-bs-theme="light"] .bulk-badge-cloud { background: #eff6ff; color: #1d4ed8; border-color: #93c5fd; }
    [data-bs-theme="light"] .bulk-badge-dc { background: #faf5ff; color: #7e22ce; border-color: #d8b4fe; }
    [data-bs-theme="light"] .bulk-badge-hosting { background: #f0fdf4; color: #15803d; border-color: #86efac; }
    [data-bs-theme="light"] .bulk-badge-private { background: #fffbeb; color: #b45309; border-color: #fcd34d; }
    [data-bs-theme="light"] .bulk-badge-unresolved { background: #fff1f2; color: #be123c; border-color: #fda4af; }

    .bulk-shared-pill {
        background: rgba(245, 158, 11, 0.16);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.45);
        font-size: 1rem;
        padding: 0.22rem 0.55rem;
        border-radius: 6px;
    }
    .bulk-shared-pill:hover {
        background: rgba(245, 158, 11, 0.28);
        color: #fde68a;
    }
    [data-bs-theme="light"] .bulk-shared-pill {
        background: #fef3c7;
        color: #92400e;
        border-color: #f59e0b;
    }

    .bulk-cc-yes-pill {
        background: rgba(0, 240, 255, 0.14);
        color: #00f0ff;
        border: 1px solid rgba(0, 240, 255, 0.4);
        font-size: 1rem;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
    }
    [data-bs-theme="light"] .bulk-cc-yes-pill {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #38bdf8;
    }

    .bulk-domain-pill {
        background: rgba(33, 54, 8, 0.45);
        border: 1px solid rgba(0, 255, 102, 0.25);
        color: #e4f5e7;
        padding: 0.28rem 0.65rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .bulk-domain-pill:hover {
        border-color: #00ff66;
        color: #00ff66;
    }
    [data-bs-theme="light"] .bulk-domain-pill {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    [data-bs-theme="light"] .bulk-domain-pill:hover {
        border-color: #15803d;
        color: #15803d;
    }

    .bulk-toast {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        z-index: 1080;
        background: rgba(9, 18, 6, 0.94);
        color: #e4f5e7;
        border: 1px solid #00ff66;
        backdrop-filter: blur(12px);
        max-width: 380px;
    }
    [data-bs-theme="light"] .bulk-toast {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.14);
    }

    .bulk-subtab-btn.active {
        background: #00ff66 !important;
        color: #050804 !important;
        border-color: #00ff66 !important;
        font-weight: 700;
    }
    [data-bs-theme="light"] .bulk-subtab-btn.active {
        background: #15803d !important;
        color: #ffffff !important;
        border-color: #15803d !important;
    }
    .bulk-tab-count {
        background: rgba(5, 8, 4, 0.38);
        color: #ffffff;
    }
    [data-bs-theme="light"] .bulk-nav-tab:not(.active) .bulk-tab-count {
        background: #cbd5e1;
        color: #0f172a;
    }
    .page-item.active .page-link {
        background-color: #00ff66;
        border-color: #00ff66;
        color: #050804;
        font-weight: 700;
    }
    [data-bs-theme="light"] .page-item.active .page-link {
        background-color: #15803d;
        border-color: #15803d;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
    <!-- Hero Banner -->
    <section class="hero-banner py-4 py-md-5 mb-4">
        <div class="container position-relative" style="z-index: 1;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 font-mono">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('services.index') }}" class="text-decoration-none">Services</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bulk Website IP Checker</li>
                </ol>
            </nav>

            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="telemetry-tag"><i class="bi bi-hdd-network"></i> INFRASTRUCTURE INTEL // 10,000+ DOMAINS</span>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 font-mono">
                    <span class="cyber-beacon me-1"></span> PARALLEL BATCH ENGINE
                </span>
            </div>

            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <h1 class="fw-bold display-5 font-cyber mb-2">
                        Bulk Website <span class="text-gradient">IP &amp; Class-C Checker</span>
                    </h1>
                    <p class="text-body-secondary mb-0 leading-relaxed" style="max-width: 780px;">
                        Resolve IPv4 &amp; IPv6 addresses for up to <strong>10,000+ websites</strong> simultaneously with zero timeouts. Classify CDN/Proxy, Cloud, VPS/Datacenter, and Hosting IP types, inspect ASN/Org/PTR telemetry, and automatically detect <strong>Same-IP</strong> and <strong>Same Class-C (/24)</strong> hosting footprints.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="d-inline-flex flex-wrap gap-2 font-mono">
                        <span class="badge bg-body-tertiary text-body border px-2 py-1"><i class="bi bi-check2-circle text-success me-1"></i> IPv4 + IPv6</span>
                        <span class="badge bg-body-tertiary text-body border px-2 py-1"><i class="bi bi-diagram-3 text-info me-1"></i> Class-C Clustering</span>
                        <span class="badge bg-body-tertiary text-body border px-2 py-1"><i class="bi bi-filetype-csv text-warning me-1"></i> CSV &amp; JSON</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container pb-5">
        <!-- Status Banner -->
        <div id="statusBanner" class="d-none" role="alert"></div>

        <!-- Input & Queue Config Panel -->
        <div class="bulk-hud-panel p-3 p-md-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h2 class="h5 fw-bold font-cyber mb-0">
                        <i class="bi bi-terminal text-success me-2"></i> Target Domains / URLs Input
                    </h2>
                    <p class="text-body-secondary font-mono mb-0">
                        Paste up to 10,000+ domains, full URLs, or drop a <code>.txt</code> / <code>.csv</code> file.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <input type="file" id="fileInput" accept=".txt,.csv,text/plain,text/csv" class="d-none">
                    <button type="button" id="browseFileBtn" class="btn btn-sm btn-tactical font-mono">
                        <i class="bi bi-upload me-1"></i> Import .txt / .csv
                    </button>
                    <button type="button" id="loadSampleBtn" class="btn btn-sm btn-outline-success font-mono">
                        <i class="bi bi-lightning-charge me-1"></i> Load Sample URLs
                    </button>
                    <button type="button" id="clearInputBtn" class="btn btn-sm btn-outline-secondary font-mono">
                        <i class="bi bi-trash me-1"></i> Clear
                    </button>
                </div>
            </div>

            <!-- Drag & Drop + Textarea -->
            <div id="fileDropZone" class="bulk-dropzone p-2 p-md-3 mb-3">
                <label for="domainsInput" class="visually-hidden">Enter websites, URLs, or domains (one per line or comma-separated)</label>
                <textarea
                    id="domainsInput"
                    class="form-control bulk-textarea font-mono text-body"
                    rows="9"
                    spellcheck="false"
                    placeholder="Enter websites, URLs, or domains (one per line, comma-separated, or drop a .txt / .csv file here)...&#10;Example:&#10;shrawaneffects.com&#10;www.shrawaneffects.com&#10;https://cloudflare.com/dns/&#10;google.com&#10;ipv6.google.com"
                ></textarea>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 mt-2 border-top border-secondary border-opacity-25 px-2 font-mono text-body-secondary">
                    <span><i class="bi bi-file-earmark-arrow-up text-success me-1"></i> Drag &amp; drop <code>.txt</code> or <code>.csv</code> anywhere in this box</span>
                    <div class="d-flex flex-wrap gap-3">
                        <span>Lines/Tokens: <strong id="statRawLines" class="text-body">0</strong></span>
                        <span>Unique Hosts: <strong id="statUniqueHosts" class="text-success">0</strong></span>
                        <span>Est. Batches: <strong id="statEstBatches" class="text-info">0</strong></span>
                    </div>
                </div>
            </div>

            <!-- Options & Run Controls -->
            <div class="row g-3 align-items-center">
                <div class="col-lg-7">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="deduplicateToggle" checked>
                            <label class="form-check-label font-mono" for="deduplicateToggle">Deduplicate hosts</label>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="enrichMetadataToggle" checked>
                            <label class="form-check-label font-mono" for="enrichMetadataToggle">Enrich ASN / Org / PTR</label>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="batchSizeSelect" class="font-mono text-body-secondary mb-0">Batch:</label>
                            <select id="batchSizeSelect" class="form-select form-select-sm font-mono w-auto">
                                <option value="15">15</option>
                                <option value="30" {{ $bulkDefaultBatchSize === 30 ? 'selected' : '' }}>30 (Optimal)</option>
                                <option value="50" {{ $bulkDefaultBatchSize === 50 ? 'selected' : '' }}>50 (Max)</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="concurrencySelect" class="font-mono text-body-secondary mb-0">Workers:</label>
                            <select id="concurrencySelect" class="form-select form-select-sm font-mono w-auto">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3" {{ $bulkDefaultConcurrency === 3 ? 'selected' : '' }}>3 (Balanced)</option>
                                <option value="4" {{ $bulkDefaultConcurrency === 4 ? 'selected' : '' }}>4 (Fast)</option>
                                <option value="6">6 (Turbo)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                        <button type="button" id="startScanBtn" class="btn btn-gradient px-4 py-2">
                            <i class="bi bi-play-fill me-1"></i> Run Bulk IP Check
                        </button>
                        <button type="button" id="pauseScanBtn" class="btn btn-tactical px-3 py-2 d-none">
                            <i class="bi bi-pause-fill me-1"></i> Pause
                        </button>
                        <button type="button" id="resumeScanBtn" class="btn btn-gradient-cyan px-3 py-2 d-none">
                            <i class="bi bi-play-circle-fill me-1"></i> Resume
                        </button>
                        <button type="button" id="stopScanBtn" class="btn btn-outline-danger font-mono px-3 py-2 d-none">
                            <i class="bi bi-stop-fill me-1"></i> Stop
                        </button>
                        <button type="button" id="retryFailedBtn" class="btn btn-outline-warning font-mono px-3 py-2 d-none">
                            Retry Failed
                        </button>
                        <button type="button" id="resetResultsBtn" class="btn btn-outline-secondary font-mono px-3 py-2 d-none">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Results
                        </button>
                    </div>
                </div>
            </div>

            <!-- Live Progress Telemetry -->
            <div id="progressSection" class="mt-4 pt-3 border-top border-secondary border-opacity-25 d-none">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 font-mono">
                    <div class="d-flex align-items-center gap-2">
                        <span id="workerStatusBadge" class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50">READY</span>
                        <span id="progressText" class="fw-semibold text-body">0 / 0 hosts (0%)</span>
                    </div>
                    <div class="d-flex flex-wrap gap-3 text-body-secondary">
                        <span id="batchProgressText">Batch 0 / 0</span>
                        <span id="speedText" class="text-success">0.0 domains/sec</span>
                        <span id="etaText" class="text-info">Calculating ETA...</span>
                    </div>
                </div>
                <div class="progress bg-body-tertiary border border-success border-opacity-25" style="height: 18px; border-radius: 10px;">
                    <div id="progressBar" class="progress-bar bg-success font-mono fw-bold" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

        <!-- KPI Summary Strip -->
        <div id="kpiSection" class="row g-3 mb-4 d-none">
            <div class="col-6 col-lg-3">
                <div class="bulk-kpi-card p-3 h-100">
                    <div class="font-mono text-body-secondary text-uppercase mb-1">Processed Hosts</div>
                    <div id="kpiProcessed" class="bulk-kpi-num">0</div>
                    <div id="kpiProcessedSub" class="font-mono text-body-secondary mt-1">0 resolved</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="bulk-kpi-card p-3 h-100">
                    <div class="font-mono text-body-secondary text-uppercase mb-1">Unique IPs</div>
                    <div id="kpiUniqueIps" class="bulk-kpi-num">0</div>
                    <div id="kpiInfraMix" class="font-mono text-info mt-1">0 / 0</div>
                    <div id="kpiInfraMixSub" class="font-mono text-body-secondary" style="font-size: 1rem;">CDN/Proxy vs Cloud/Hosting</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="bulk-kpi-card p-3 h-100">
                    <div class="font-mono text-body-secondary text-uppercase mb-1">Shared IP Clusters</div>
                    <div id="kpiSharedIps" class="bulk-kpi-num text-warning">0</div>
                    <div id="kpiSharedIpsSub" class="font-mono text-body-secondary mt-1">0 websites share an IP</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="bulk-kpi-card p-3 h-100">
                    <div class="font-mono text-body-secondary text-uppercase mb-1">Shared Class C (/24)</div>
                    <div id="kpiSharedClassC" class="bulk-kpi-num text-info">0</div>
                    <div id="kpiSharedClassCSub" class="font-mono text-body-secondary mt-1">0 total /24 subnets</div>
                </div>
            </div>
        </div>

        <!-- Workspace Tabs, Exports, Results Table & Shared Subnet Report -->
        <div id="workspaceSection" class="d-none">
            <!-- Top Navigation Tabs + Export Toolbar -->
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3 mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" id="tabResultsBtn" class="bulk-nav-tab active">
                        <i class="bi bi-table"></i>
                        <span>All Results</span>
                        <span id="tabResultsCount" class="badge bulk-tab-count font-mono">0</span>
                    </button>
                    <button type="button" id="tabClustersBtn" class="bulk-nav-tab">
                        <i class="bi bi-diagram-3-fill"></i>
                        <span>Same IP &amp; Shared Subnet Report</span>
                        <span id="tabClustersCount" class="badge bulk-tab-count font-mono">0</span>
                    </button>
                </div>

                <!-- Export Tools -->
                <div class="d-flex flex-wrap gap-2">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-tactical dropdown-toggle font-mono" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-download me-1"></i> Export CSV
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow border border-success border-opacity-25">
                            <li>
                                <button type="button" id="exportCsvAllBtn" class="dropdown-item font-mono py-2">
                                    <i class="bi bi-filetype-csv text-success me-2"></i> CSV — All Results
                                </button>
                            </li>
                            <li>
                                <button type="button" id="exportCsvSharedBtn" class="dropdown-item font-mono py-2">
                                    <i class="bi bi-diagram-2 text-warning me-2"></i> CSV — Shared IPs Only
                                </button>
                            </li>
                            <li>
                                <button type="button" id="exportCsvClustersBtn" class="dropdown-item font-mono py-2">
                                    <i class="bi bi-layers text-info me-2"></i> CSV — Cluster Summary
                                </button>
                            </li>
                        </ul>
                    </div>
                    <button type="button" id="exportJsonBtn" class="btn btn-sm btn-outline-info font-mono">
                        <i class="bi bi-filetype-json me-1"></i> JSON
                    </button>
                    <button type="button" id="copySharedBtn" class="btn btn-sm btn-outline-warning font-mono">
                        <i class="bi bi-clipboard-check me-1"></i> Copy Shared IPs
                    </button>
                    <button type="button" id="copyDomainsTxtBtn" class="btn btn-sm btn-outline-secondary font-mono">
                        <i class="bi bi-list-ul me-1"></i> Copy Domains
                    </button>
                </div>
            </div>

            <!-- TAB 1: Paginated Results Table -->
            <div id="resultsTabPane" class="bulk-hud-panel p-3 p-md-4">
                <!-- Search + Filter Chips + Per Page -->
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-6 col-lg-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="tableSearchInput" class="form-control font-mono" placeholder="Search host, IP, /24, ASN, org...">
                            <button type="button" id="clearSearchBtn" class="btn btn-outline-secondary" title="Clear search">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-7 d-flex flex-wrap justify-content-md-end align-items-center gap-2">
                        <label for="perPageSelect" class="font-mono text-body-secondary mb-0">Rows/page:</label>
                        <select id="perPageSelect" class="form-select form-select-sm font-mono w-auto">
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                            <option value="250">250</option>
                        </select>
                    </div>
                </div>

                <!-- Filter Chips -->
                <div id="filterChipsRow" class="d-flex flex-wrap gap-2 mb-3"></div>

                <!-- Active IP / Class C Drilldown Banner -->
                <div id="activeDrilldownBar" class="alert alert-info py-2 px-3 d-flex justify-content-between align-items-center font-mono mb-3 d-none">
                    <span id="activeDrilldownText"></span>
                    <button type="button" id="clearDrilldownBtn" class="btn btn-sm btn-outline-info py-0 px-2 font-mono">
                        <i class="bi bi-x-circle me-1"></i> Clear Filter
                    </button>
                </div>

                <!-- Results Table -->
                <div class="table-responsive">
                    <table class="table bulk-table table-hover align-middle mb-0">
                        <thead id="resultsTableHead">
                            <tr>
                                <th data-sort="seqIndex"># <i class="bi bi-caret-up-fill sort-indicator ms-1"></i></th>
                                <th data-sort="host">Website / Host <i class="bi bi-arrow-down-up sort-indicator ms-1 opacity-50"></i></th>
                                <th data-sort="ipv4">IPv4 <i class="bi bi-arrow-down-up sort-indicator ms-1 opacity-50"></i></th>
                                <th data-sort="ipv6">IPv6 <i class="bi bi-arrow-down-up sort-indicator ms-1 opacity-50"></i></th>
                                <th data-sort="ip_type">IP Type <i class="bi bi-arrow-down-up sort-indicator ms-1 opacity-50"></i></th>
                                <th data-sort="ip_class">Class / Ver <i class="bi bi-arrow-down-up sort-indicator ms-1 opacity-50"></i></th>
                                <th data-sort="class_c">Class C (/24) <i class="bi bi-arrow-down-up sort-indicator ms-1 opacity-50"></i></th>
                                <th data-sort="org">ASN / ISP / Org <i class="bi bi-arrow-down-up sort-indicator ms-1 opacity-50"></i></th>
                                <th data-sort="globalSameIpCount">Same IP <i class="bi bi-arrow-down-up sort-indicator ms-1 opacity-50"></i></th>
                                <th data-sort="globalSameClassCCount" class="text-center">Same Class C <i class="bi bi-arrow-down-up sort-indicator ms-1 opacity-50"></i></th>
                            </tr>
                        </thead>
                        <tbody id="resultsTableBody"></tbody>
                    </table>
                </div>

                <!-- Empty State for Table Filter -->
                <div id="resultsEmptyState" class="text-center py-5 d-none">
                    <i class="bi bi-search fs-2 text-body-secondary d-block mb-2"></i>
                    <p class="font-mono text-body-secondary mb-0">No rows match your current search or filter chip.</p>
                </div>

                <!-- Pagination Footer -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-3 pt-3 border-top border-secondary border-opacity-25">
                    <div id="paginationSummary" class="font-mono text-body-secondary"></div>
                    <nav aria-label="Results pagination">
                        <ul id="paginationControls" class="pagination pagination-sm mb-0 flex-wrap"></ul>
                    </nav>
                </div>
            </div>

            <!-- TAB 2: Same IP & Shared Subnet Report -->
            <div id="clustersTabPane" class="bulk-hud-panel p-3 p-md-4 d-none">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4 pb-3 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" id="clusterByIpBtn" class="btn btn-sm btn-tactical bulk-subtab-btn active font-mono">
                            <i class="bi bi-hdd-network me-1"></i> Exact Same IP
                            <span id="clusterByIpBadge" class="badge bg-warning text-dark ms-1">0</span>
                        </button>
                        <button type="button" id="clusterByClassCBtn" class="btn btn-sm btn-tactical bulk-subtab-btn font-mono">
                            <i class="bi bi-diagram-3 me-1"></i> Same Class C (/24)
                            <span id="clusterByClassCBadge" class="badge bg-info text-dark ms-1">0</span>
                        </button>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="input-group input-group-sm" style="max-width: 280px;">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="clusterSearchInput" class="form-control font-mono" placeholder="Filter clusters by IP, /24, host...">
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="showUniqueClustersToggle">
                            <label class="form-check-label font-mono" for="showUniqueClustersToggle">Show unique (1 site)</label>
                        </div>
                    </div>
                </div>

                <div id="clustersContainer"></div>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-3 pt-3 border-top border-secondary border-opacity-25">
                    <div id="clusterPaginationSummary" class="font-mono text-body-secondary"></div>
                    <nav aria-label="Clusters pagination">
                        <ul id="clusterPaginationControls" class="pagination pagination-sm mb-0 flex-wrap"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Toast -->
    <div id="actionToast" class="bulk-toast rounded-pill px-4 py-2 shadow-lg d-none" role="status" aria-live="polite">
        <div class="d-flex align-items-center gap-2 font-mono">
            <i class="bi bi-check-circle-fill text-success"></i>
            <span id="actionToastMsg"></span>
        </div>
    </div>

    <!-- Tool In-Depth Article Guide, Table of Contents, FAQs & Related Widgets -->
    @include('frontend.services.partials.tool-guide', [
        'tool' => $tool ?? null,
        'relatedTools' => $relatedTools ?? null,
        'relatedPosts' => $relatedPosts ?? null,
    ])
@endsection

@push('scripts')
<script>
    window.BULK_IP_CHECKER_CONFIG = {
        resolveUrl: @json($bulkResolveUrl),
        maxBatchSize: {{ (int) $bulkMaxBatchSize }},
        defaultBatchSize: {{ (int) $bulkDefaultBatchSize }},
        defaultConcurrency: {{ (int) $bulkDefaultConcurrency }},
        sampleDomains: @json($bulkSampleDomains),
        ipTypeLabels: @json($bulkIpTypeLabels),
        csrfToken: @json(csrf_token())
    };
</script>
<script src="{{ asset('js/bulk-ip-checker.js') }}?v={{ $jsVersion }}" defer></script>
@endpush

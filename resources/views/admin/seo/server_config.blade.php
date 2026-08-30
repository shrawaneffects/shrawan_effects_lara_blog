@extends('layouts.admin')

@section('title', 'Server Configuration & SEO Files (.htaccess & robots.txt)')
@section('page_title', 'Robots.txt & .htaccess Manager')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/trending-seo.css') }}">
<style>
    .code-editor-area {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
        font-size: 0.9rem;
        line-height: 1.5;
        background-color: #1e1e2e;
        color: #cdd6f4;
        border: 1px solid rgba(99, 102, 241, 0.25);
        border-radius: 12px;
        padding: 1rem;
        tab-size: 4;
        resize: vertical;
        min-height: 280px;
    }
    .code-editor-area:focus {
        background-color: #181825;
        color: #f5e0dc;
        border-color: #89b4fa;
        outline: none;
        box-shadow: 0 0 0 3px rgba(137, 180, 250, 0.25);
    }
</style>
@endpush

@section('content')
    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h4 class="fw-bold mb-0">Server Configuration & SEO Files</h4>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">Direct Server I/O</span>
            </div>
            <p class="text-body-secondary small mb-0">Directly modify your search engine crawling instructions (<kbd>robots.txt</kbd>) and Apache web server directives (<kbd>.htaccess</kbd>) with automated backups.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.seo.clear-cache') }}" onclick="return confirm('Clear all system and SEO caches?');" class="btn btn-outline-danger rounded-pill px-3 shadow-sm" title="Clear all temporary caches">
                <i class="bi bi-trash3-fill me-1"></i> Clear Cache
            </a>
            <a href="{{ route('admin.seo.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-graph-up-arrow me-1"></i> SEO Health
            </a>
            <a href="{{ route('admin.seo.grader') }}" class="btn btn-outline-primary rounded-pill px-3">
                <i class="bi bi-award-fill me-1"></i> SEO Grader
            </a>
            <a href="{{ route('admin.seo.redirects') }}" class="btn btn-outline-info rounded-pill px-3">
                <i class="bi bi-signpost-split me-1"></i> 301 Redirects
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2 flex-shrink-0"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2 flex-shrink-0"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- 1. ROBOTS.TXT CARD -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-body glass-panel">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-robot me-2"></i> robots.txt</h5>
                            @if($robotsWritable)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Writable</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Read Only</span>
                            @endif
                        </div>
                        <small class="text-body-secondary">Controls crawler access and informs search engines of your XML sitemap URL.</small>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ url('/robots.txt') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill" title="View live in browser">
                            <i class="bi bi-box-arrow-up-right me-1"></i> View Live
                        </a>
                    </div>
                </div>

                <!-- Status Metadata Bar -->
                <div class="p-2.5 rounded-3 bg-body-tertiary border d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 small">
                    <div><span class="text-muted">Size:</span> <strong>{{ number_format($robotsSize) }} bytes</strong></div>
                    <div><span class="text-muted">Modified:</span> <strong>{{ $robotsModified }}</strong></div>
                    <div>
                        <span class="text-muted">Backup:</span> 
                        <strong>{{ $hasRobotsBackup ? 'Available' : 'None' }}</strong>
                    </div>
                </div>

                <!-- Quick Snippets Toolbar -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-body-secondary d-block mb-1.5"><i class="bi bi-magic me-1"></i> Quick Directives Insertion:</label>
                    <div class="d-flex flex-wrap gap-1.5">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-0.5 px-2.5" onclick="insertRobotsDirective('sitemap')">
                            + Sitemap Directive
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-2.5" onclick="insertRobotsDirective('disallow_admin')">
                            + Disallow Admin
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-2.5" onclick="insertRobotsDirective('allow_all')">
                            + Allow All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-2.5" onclick="insertRobotsDirective('crawl_delay')">
                            + Crawl-Delay
                        </button>
                    </div>
                </div>

                <!-- Robots Form -->
                <form action="{{ route('admin.seo.server-config.robots') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="content" id="robotsTextarea" class="form-control code-editor-area" rows="12" spellcheck="false">{{ old('content', $robotsContent) }}</textarea>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="document.getElementById('restoreRobotsForm').submit()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> {{ $hasRobotsBackup ? 'Restore Backup' : 'Reset to Default' }}
                        </button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" {{ !$robotsWritable ? 'disabled' : '' }}>
                            <i class="bi bi-check2 me-1"></i> Save robots.txt
                        </button>
                    </div>
                </form>

                <form id="restoreRobotsForm" action="{{ route('admin.seo.server-config.restore-robots') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>

        <!-- 2. .HTACCESS CARD -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-body glass-panel">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-hdd-network me-2"></i> .htaccess</h5>
                            @if($htaccessWritable)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Writable</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Read Only</span>
                            @endif
                        </div>
                        <small class="text-body-secondary">Apache web server configuration, URL rewrite rules, HTTP redirects, and caching headers.</small>
                    </div>
                </div>

                <!-- Status Metadata Bar -->
                <div class="p-2.5 rounded-3 bg-body-tertiary border d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 small">
                    <div><span class="text-muted">Size:</span> <strong>{{ number_format($htaccessSize) }} bytes</strong></div>
                    <div><span class="text-muted">Modified:</span> <strong>{{ $htaccessModified }}</strong></div>
                    <div>
                        <span class="text-muted">Backup:</span> 
                        <strong>{{ $hasHtaccessBackup ? 'Available' : 'None' }}</strong>
                    </div>
                </div>

                <!-- Quick Snippets Toolbar -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-body-secondary d-block mb-1.5"><i class="bi bi-magic me-1"></i> Quick Snippets Insertion:</label>
                    <div class="d-flex flex-wrap gap-1.5">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-0.5 px-2.5" onclick="insertHtaccessSnippet('gzip')">
                            + Gzip Compression
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-2.5" onclick="insertHtaccessSnippet('cache')">
                            + Browser Cache Expiry
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-2.5" onclick="insertHtaccessSnippet('https')">
                            + Force HTTPS
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-2.5" onclick="insertHtaccessSnippet('security')">
                            + Security Headers
                        </button>
                    </div>
                </div>

                <!-- .htaccess Form -->
                <form action="{{ route('admin.seo.server-config.htaccess') }}" method="POST" onsubmit="return confirmSaveHtaccess()">
                    @csrf
                    <div class="mb-3">
                        <textarea name="content" id="htaccessTextarea" class="form-control code-editor-area" rows="12" spellcheck="false">{{ old('content', $htaccessContent) }}</textarea>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="document.getElementById('restoreHtaccessForm').submit()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> {{ $hasHtaccessBackup ? 'Restore Backup' : 'Reset to Laravel Default' }}
                        </button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" {{ !$htaccessWritable ? 'disabled' : '' }}>
                            <i class="bi bi-check2 me-1"></i> Save .htaccess
                        </button>
                    </div>
                </form>

                <form id="restoreHtaccessForm" action="{{ route('admin.seo.server-config.restore-htaccess') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function insertRobotsDirective(type) {
        const area = document.getElementById('robotsTextarea');
        if (!area) return;

        let snippet = '';
        if (type === 'sitemap') {
            snippet = "\n# Dynamic XML Sitemap\nSitemap: {{ url('/sitemap.xml') }}\n";
        } else if (type === 'disallow_admin') {
            snippet = "\n# Disallow Admin & Auth Endpoints\nUser-agent: *\nDisallow: /admin/\nDisallow: /login\nDisallow: /register\nDisallow: /password/\n";
        } else if (type === 'allow_all') {
            snippet = "\nUser-agent: *\nDisallow:\n";
        } else if (type === 'crawl_delay') {
            snippet = "\n# Rate limit aggressive bots\nUser-agent: *\nCrawl-delay: 5\n";
        }

        area.value += snippet;
        area.scrollTop = area.scrollHeight;
    }

    function insertHtaccessSnippet(type) {
        const area = document.getElementById('htaccessTextarea');
        if (!area) return;

        let snippet = '';
        if (type === 'gzip') {
            snippet = `\n# Enable Gzip / Deflate Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain text/html text/xml text/css application/xml application/xhtml+xml application/rss+xml application/javascript application/x-javascript application/json image/svg+xml
</IfModule>\n`;
        } else if (type === 'cache') {
            snippet = `\n# Browser Caching Headers
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>\n`;
        } else if (type === 'https') {
            snippet = `\n# Force HTTPS SSL Connection
<IfModule mod_rewrite.c>
    RewriteCond %{HTTPS} !=on
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>\n`;
        } else if (type === 'security') {
            snippet = `\n# Advanced Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>\n`;
        }

        area.value += snippet;
        area.scrollTop = area.scrollHeight;
    }

    function confirmSaveHtaccess() {
        return confirm('Are you sure you want to update .htaccess? A backup of the current file will automatically be created at .htaccess.backup before saving.');
    }
</script>
@endpush
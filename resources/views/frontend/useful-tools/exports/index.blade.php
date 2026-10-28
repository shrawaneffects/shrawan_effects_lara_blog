@extends('layouts.app')

@section('title', 'Export Center - Useful Tools | ' . config('app.name', 'Shrawan Effects'))
@section('meta_description', 'Secure download archives for Business Leads, SEO Rankings, and Traffic Intelligence reports.')
@section('canonical_url', route('useful-tools.exports.index'))

@section('content')
<div class="container py-4 my-2">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2 font-mono small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Useful Tools</a></li>
            <li class="breadcrumb-item active" aria-current="page">Export Center</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="telemetry-tag"><i class="bi bi-cloud-arrow-down"></i> DATA ARCHIVES</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono small">Formula Protection Active</span>
            </div>
            <h1 class="fw-bold mb-1 display-6 font-cyber">Export <span class="text-gradient">Center</span></h1>
            <p class="text-body-secondary small mb-0 font-mono">
                Encrypted single-user download vaults for business listings, SERP rank matrices, and traffic analytics.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('useful-tools.business-scraper.index') }}" class="btn btn-tactical rounded-pill px-3 py-2 small">
                <i class="bi bi-building me-1"></i> Business Scraper
            </a>
            <a href="{{ route('useful-tools.seo-traffic.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small">
                <i class="bi bi-graph-up-arrow me-1"></i> SEO Analyzer
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

    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
            <h5 class="fw-bold mb-0 font-cyber">
                <i class="bi bi-file-earmark-arrow-down text-gradient me-2"></i> Generated Exports History
            </h5>
            <span class="text-body-secondary small font-mono">
                Downloads automatically expire after 24 hours
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 font-mono" style="font-size: 0.85rem;">
                <thead class="table-light text-uppercase" style="font-size: 0.72rem;">
                    <tr>
                        <th>Archive Filename</th>
                        <th>Tool Source</th>
                        <th>Format</th>
                        <th>Records</th>
                        <th>File Size</th>
                        <th>Expires In</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exports as $ex)
                        <tr>
                            <td>
                                <div class="fw-bold text-body">
                                    <i class="bi bi-file-earmark-text text-primary me-1"></i> {{ $ex->filename }}
                                </div>
                                <div class="small text-muted" style="font-size: 0.72rem;">Created {{ $ex->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <span class="badge bg-body-secondary text-body border rounded-pill px-2.5 py-0.5">
                                    {{ strtoupper(str_replace('_', ' ', $ex->tool_type)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $ex->format === 'csv' ? 'bg-success' : 'bg-info' }} rounded-pill px-2 py-0.5 text-uppercase">
                                    {{ $ex->format }}
                                </span>
                            </td>
                            <td>{{ number_format($ex->total_records) }} rows</td>
                            <td>{{ number_format($ex->file_size_bytes / 1024, 1) }} KB</td>
                            <td>
                                @if($ex->isExpired())
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0.5">Expired</span>
                                @else
                                    <span class="text-success small"><i class="bi bi-clock me-1"></i> {{ $ex->expires_at->diffForHumans() }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if(!$ex->isExpired())
                                    <a href="{{ route('useful-tools.exports.download', $ex->download_token) }}" class="btn btn-sm btn-gradient rounded-pill px-3 py-1 me-1">
                                        <i class="bi bi-download me-1"></i> Download
                                    </a>
                                @endif
                                <form action="{{ route('useful-tools.exports.destroy', $ex->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this export archive?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-cloud-arrow-down fs-1 text-body-tertiary mb-3 d-block"></i>
                                <h6 class="fw-bold font-cyber">No Export Archives Generated Yet</h6>
                                <p class="small font-mono text-body-secondary mb-0">
                                    Use Business Data Scraper or Website SEO Analyzer to export CSV or JSON datasets.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exports->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $exports->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection

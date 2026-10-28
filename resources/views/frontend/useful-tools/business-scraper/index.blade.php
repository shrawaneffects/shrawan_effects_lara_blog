@extends('layouts.app')

@section('title', 'Business Data Scraper - Useful Tools | ' . config('app.name', 'Shrawan Effects'))
@section('meta_description', 'Search, extract, filter, and export verified business listings across Google Places, Search, Yelp, IndiaMART, and Justdial.')
@section('canonical_url', route('useful-tools.business-scraper.index'))

@section('content')
<div class="container py-4 my-2">
    <!-- Breadcrumb & Header -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2 font-mono small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Useful Tools</a></li>
            <li class="breadcrumb-item active" aria-current="page">Business Data Scraper</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="telemetry-tag"><i class="bi bi-building"></i> LEAD DISCOVERY ENGINE</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill font-mono small">v2.0 Active</span>
            </div>
            <h1 class="fw-bold mb-1 display-6 font-cyber">Business <span class="text-gradient">Data Scraper</span></h1>
            <p class="text-body-secondary small mb-0 font-mono">
                Extract, verify, deduplicate, and export authorized commercial business profiles with phone numbers and geo-coordinates.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('useful-tools.exports.index') }}" class="btn btn-tactical rounded-pill px-3 py-2 small">
                <i class="bi bi-cloud-arrow-down me-1"></i> Export Center
            </a>
            <a href="{{ route('useful-tools.seo-traffic.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small">
                <i class="bi bi-graph-up-arrow me-1"></i> SEO & Traffic Analyzer
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
    @if(session('warning'))
        <div class="alert alert-warning d-flex align-items-center rounded-3 mb-4 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('warning') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center rounded-3 mb-4 shadow-sm">
            <i class="bi bi-shield-slash-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info d-flex align-items-center rounded-3 mb-4 shadow-sm">
            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
            <div>{{ session('info') }}</div>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold font-mono">Total Collected</span>
                        <h3 class="fw-bold mb-0 text-gradient font-cyber mt-1">{{ number_format($totalBusinesses) }}</h3>
                    </div>
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-4" style="width: 48px; height: 48px;">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold font-mono">Verified Phones</span>
                        <h3 class="fw-bold mb-0 text-success font-cyber mt-1">{{ number_format($verifiedContacts) }}</h3>
                    </div>
                    <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-4" style="width: 48px; height: 48px;">
                        <i class="bi bi-telephone-fill fs-4 text-gradient-emerald"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold font-mono">Scraping Tasks</span>
                        <h3 class="fw-bold mb-0 text-body font-cyber mt-1">{{ number_format($totalJobs) }}</h3>
                    </div>
                    <div class="d-inline-flex align-items-center justify-content-center bg-info-subtle text-info rounded-4" style="width: 48px; height: 48px;">
                        <i class="bi bi-cpu fs-4 text-gradient-cyan"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold font-mono">Deduplication</span>
                        <h3 class="fw-bold mb-0 text-warning font-cyber mt-1">Active</h3>
                    </div>
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-4" style="width: 48px; height: 48px;">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid: Scraper Form & Results Table -->
    <div class="row g-4">
        <!-- Left Column: Search & Extraction Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4 sticky-top-widget">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h5 class="fw-bold mb-0 font-cyber">
                        <i class="bi bi-search text-gradient me-2"></i> New Search Job
                    </h5>
                    <span class="telemetry-tag"><i class="bi bi-radar"></i> QUERY</span>
                </div>

                <form action="{{ route('useful-tools.business-scraper.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keyword / Product / Service *</label>
                        <input type="text" name="keyword" class="form-control rounded-pill px-3 py-2 font-mono small" placeholder="e.g. Crane Motor Manufacturer, Web Studio" value="{{ old('keyword', 'Website Development') }}" required>
                        @error('keyword') <small class="text-danger font-mono">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Industry / Category (Optional)</label>
                        <input type="text" name="category" class="form-control rounded-pill px-3 py-2 small" placeholder="e.g. Information Technology, Heavy Engineering" value="{{ old('category') }}">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">City *</label>
                            <input type="text" name="city" class="form-control rounded-pill px-3 py-2 small" placeholder="Faridabad" value="{{ old('city', 'Faridabad') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">State</label>
                            <input type="text" name="state" class="form-control rounded-pill px-3 py-2 small" placeholder="Haryana" value="{{ old('state', 'Haryana') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Country</label>
                        <input type="text" name="country" class="form-control rounded-pill px-3 py-2 small" placeholder="India" value="{{ old('country', 'India') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Data Source Provider *</label>
                        <select name="provider" class="form-select rounded-pill px-3 py-2 small" required>
                            @foreach($providers as $pKey => $p)
                                <option value="{{ $pKey }}" {{ (old('provider') === $pKey || ($pKey === 'google_places' && !old('provider'))) ? 'selected' : '' }}>
                                    {{ $p['name'] }} {{ $p['is_configured'] ? '✓ (Active)' : '⚠ (Requires Key)' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="mt-1 small font-mono text-body-secondary" style="font-size: 0.72rem;">
                            <i class="bi bi-info-circle me-1 text-success"></i> Official API gateways prevent CAPTCHAs & bans.
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Max Results</label>
                            <select name="max_results" class="form-select rounded-pill px-3 py-2 small">
                                <option value="10">10 records</option>
                                <option value="20" selected>20 records</option>
                                <option value="50">50 records</option>
                                <option value="60">60 records</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Execution</label>
                            <select name="run_mode" class="form-select rounded-pill px-3 py-2 small">
                                <option value="sync" selected>Immediate</option>
                                <option value="async">Queue Job</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gradient w-100 py-2.5 rounded-pill shadow-sm fw-bold font-cyber">
                        <i class="bi bi-play-circle-fill me-2"></i> INITIATE SCRAPER
                    </button>
                </form>

                <!-- Providers Live Diagnostic Panel -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold small font-cyber text-body-secondary mb-2">PROVIDER STATUS</h6>
                    <div class="d-flex flex-column gap-2 small">
                        @foreach($providers as $pKey => $p)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-body-tertiary">
                                <div>
                                    <span class="fw-semibold">{{ $p['name'] }}</span>
                                    <div class="text-muted font-mono" style="font-size: 0.68rem;">{{ Str::limit($p['description'], 45) }}</div>
                                </div>
                                <x-useful-tools.provider-status-badge :isConfigured="$p['is_configured']" :name="$p['name']" :description="$p['description']" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Results Table, Filters & Bulk Export -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 border-bottom pb-3">
                    <div>
                        <h5 class="fw-bold mb-0 font-cyber">
                            <i class="bi bi-table text-gradient me-2"></i> Collected Business Records
                        </h5>
                        <span class="text-body-secondary small font-mono">
                            Showing {{ $businesses->firstItem() ?? 0 }}–{{ $businesses->lastItem() ?? 0 }} of {{ $businesses->total() }} records
                        </span>
                    </div>

                    <!-- Export Actions -->
                    <form action="{{ route('useful-tools.business-scraper.export') }}" method="POST" id="bulkExportForm" class="d-flex gap-2">
                        @csrf
                        <input type="hidden" name="city" value="{{ request('city') }}">
                        <input type="hidden" name="provider" value="{{ request('provider') }}">
                        <button type="submit" name="format" value="csv" class="btn btn-sm btn-success rounded-pill px-3">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
                        </button>
                        <button type="submit" name="format" value="json" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-filetype-json me-1"></i> JSON
                        </button>
                    </form>
                </div>

                <!-- Filters Bar -->
                <form action="{{ route('useful-tools.business-scraper.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search business, phone, or tech..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <select name="city" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Cities</option>
                            @foreach($cities as $c)
                                <option value="{{ $c }}" {{ request('city') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <select name="has_phone" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Phone: Any</option>
                            <option value="1" {{ request('has_phone') ? 'selected' : '' }}>Has Phone</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100">Filter</button>
                        @if(request()->hasAny(['search', 'city', 'has_phone', 'provider']))
                            <a href="{{ route('useful-tools.business-scraper.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Reset Filters"><i class="bi bi-x"></i></a>
                        @endif
                    </div>
                </form>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 font-mono" style="font-size: 0.82rem;">
                        <thead class="table-light text-uppercase" style="font-size: 0.72rem;">
                            <tr>
                                <th style="width: 30px;">
                                    <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                                </th>
                                <th>Business Name</th>
                                <th>Category & Location</th>
                                <th>Contact Information</th>
                                <th>Rating</th>
                                <th>Source</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($businesses as $b)
                                <tr>
                                    <td>
                                        <input class="form-check-input row-select" type="checkbox" name="selected_ids[]" value="{{ $b->id }}" form="bulkExportForm">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-body font-cyber" style="font-size: 0.88rem;">{{ $b->name }}</div>
                                        @if($b->website)
                                            <a href="{{ $b->website }}" target="_blank" rel="noopener noreferrer" class="text-success small text-decoration-none text-truncate d-inline-block" style="max-width: 200px;">
                                                <i class="bi bi-link-45deg"></i> {{ parse_url($b->website, PHP_URL_HOST) ?: $b->website }}
                                            </a>
                                        @elseif($b->listing_url)
                                            <a href="{{ $b->listing_url }}" target="_blank" rel="noopener noreferrer" class="text-muted small text-decoration-none">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> Listing Page
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-body-secondary text-body border rounded-pill px-2 py-0.5 small mb-1">
                                            {{ $b->category ?: 'General Business' }}
                                        </span>
                                        <div class="small text-body-secondary text-truncate" style="max-width: 220px;" title="{{ $b->address }}">
                                            <i class="bi bi-geo-alt me-1 text-danger"></i> {{ $b->city ?: 'Faridabad' }}{{ $b->state ? ', ' . $b->state : '' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($b->phone)
                                            <a href="tel:{{ $b->phone }}" class="fw-bold text-success text-decoration-none d-block">
                                                <i class="bi bi-telephone me-1"></i> {{ $b->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted small">Phone N/A</span>
                                        @endif
                                        @if($b->email)
                                            <a href="mailto:{{ $b->email }}" class="text-body-secondary small text-decoration-none d-block">
                                                <i class="bi bi-envelope me-1"></i> {{ $b->email }}
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($b->rating)
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-0.5">
                                                ★ {{ number_format($b->rating, 1) }} ({{ $b->review_count ?: 0 }})
                                            </span>
                                        @else
                                            <span class="text-muted small">No reviews</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5">
                                            {{ strtoupper(str_replace('_', ' ', $b->source_platform)) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('useful-tools.business-scraper.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Delete this business lead?');">
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
                                    <td colspan="7" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="bi bi-search fs-1 text-body-tertiary mb-3 d-block"></i>
                                            <h6 class="fw-bold font-cyber">No Business Records Collected Yet</h6>
                                            <p class="text-body-secondary small font-mono mb-3" style="max-width: 480px; margin: 0 auto;">
                                                Use the search form on the left to extract commercial records from Google Places, Yelp, IndiaMART, or Google Search.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($businesses->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $businesses->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            <!-- Recent Scraping Jobs Tracker -->
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h6 class="fw-bold mb-0 font-cyber">
                        <i class="bi bi-clock-history text-gradient me-2"></i> Recent Scraping Tasks
                    </h6>
                    <span class="telemetry-tag"><i class="bi bi-cpu"></i> BACKGROUND WORKER</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle font-mono small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Target Query</th>
                                <th>Provider</th>
                                <th>Status</th>
                                <th>Collected</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentJobs as $job)
                                <tr>
                                    <td>#{{ $job->id }}</td>
                                    <td>
                                        <strong>{{ $job->query_keyword }}</strong>
                                        <span class="text-muted">({{ $job->city ?: 'All' }})</span>
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $job->provider)) }}</td>
                                    <td>
                                        @if($job->status === 'completed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5">Completed</span>
                                        @elseif($job->status === 'running')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5"><i class="bi bi-arrow-repeat bi-spin me-1"></i> Running</span>
                                        @elseif($job->status === 'failed')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" title="{{ $job->error_message }}">Failed</span>
                                        @elseif($job->status === 'cancelled')
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5">Cancelled</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-0.5">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $job->total_collected }} / {{ $job->total_found }}</td>
                                    <td>{{ $job->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No background jobs registered yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAllCheckbox');
        const rowCheckboxes = document.querySelectorAll('.row-select');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            });
        }
    });
</script>
@endsection

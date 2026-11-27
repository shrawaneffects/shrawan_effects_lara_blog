@extends('layouts.admin')

@section('title', 'Manage Tools & Content - Admin Panel')
@section('page_title', 'Tools Management')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Web Tools & Utilities</h4>
        <p class="text-body-secondary mb-0" style="font-size: 1rem;">Manage interactive tools, online text converters, bulk network checkers, and their full article & guide content.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.tools.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> New Tool Content
        </a>
        <a href="{{ route('services.convertcase') }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3">
            <i class="bi bi-type me-1"></i> Open Convert Case
        </a>
        <a href="{{ route('services.bulk-ip-checker') }}" target="_blank" class="btn btn-outline-info rounded-pill px-3">
            <i class="bi bi-hdd-network me-1"></i> Open Bulk IP Checker
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Overview Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-body-secondary fw-semibold">Total Tools</span>
                    <h4 class="fw-bold mb-0 text-gradient mt-1">{{ number_format($totalTools) }}</h4>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-4">
                    <i class="bi bi-tools fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-body-secondary fw-semibold">Active & Live</span>
                    <h4 class="fw-bold mb-0 text-success mt-1">{{ number_format($activeTools) }}</h4>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-4">
                    <i class="bi bi-check2-circle fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-body-secondary fw-semibold">Public Services</span>
                    <h4 class="fw-bold mb-0 text-info mt-1">{{ number_format($frontendTools) }}</h4>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-4">
                    <i class="bi bi-globe fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-body-secondary fw-semibold">Admin Suite Tools</span>
                    <h4 class="fw-bold mb-0 text-warning mt-1">{{ number_format($adminTools) }}</h4>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-4">
                    <i class="bi bi-sliders fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.tools.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-body border-end-0"><i class="bi bi-search text-body-secondary"></i></span>
                    <input type="text" name="search" class="form-control bg-body border-start-0" placeholder="Search tools by title, slug, or summary..." value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select bg-body">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ ($selectedCategory ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select bg-body">
                    <option value="">All Statuses</option>
                    <option value="active" {{ ($selectedStatus ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($selectedStatus ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-3">Filter</button>
                @if(!empty($search) || !empty($selectedCategory) || !empty($selectedStatus))
                    <a href="{{ route('admin.tools.index') }}" class="btn btn-outline-secondary rounded-3" title="Clear Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tools List Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 50px;">Icon</th>
                    <th scope="col">Tool Name & Info</th>
                    <th scope="col">Category</th>
                    <th scope="col">Access URL</th>
                    <th scope="col" class="text-center" style="width: 110px;">Status</th>
                    <th scope="col" class="text-end" style="width: 220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tools as $tool)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-3 border" style="width: 44px; height: 44px;">
                                <i class="bi {{ $tool->icon ?: 'bi-tools' }} fs-5 text-success"></i>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold fs-6 text-body">{{ $tool->title }}</span>
                                @if(!empty($tool->badge))
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 font-mono" style="font-size: 0.8rem;">
                                        {{ $tool->badge }}
                                    </span>
                                @endif
                                @if(($tool->type ?? '') === 'admin')
                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 font-mono" style="font-size: 0.8rem;">
                                        ADMIN
                                    </span>
                                @endif
                            </div>
                            <div class="text-body-secondary small" style="max-width: 550px;">
                                {{ Str::limit($tool->summary ?: 'No summary provided.', 95) }}
                            </div>
                            @if(!empty($tool->content))
                                <div class="mt-1">
                                    <span class="badge bg-info-subtle text-info border border-info border-opacity-25 font-mono" style="font-size: 0.75rem;">
                                        <i class="bi bi-file-earmark-richtext me-1"></i> Has Custom Content
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-body-secondary text-body border">
                                {{ $tool->category ?: 'General' }}
                            </span>
                        </td>
                        <td>
                            <code class="text-body-secondary font-mono" style="font-size: 0.85rem;">
                                {{ $tool->url ?: ('/services/' . $tool->slug) }}
                            </code>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.tools.toggle', $tool->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="id" value="{{ $tool->id }}">
                                <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Click to Toggle Status">
                                    @if($tool->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                            <i class="bi bi-check-circle me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                            <i class="bi bi-dash-circle me-1"></i> Inactive
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <!-- Edit Full Content & SEO -->
                                <a href="{{ route('admin.tools.edit', $tool->id) }}" class="btn btn-sm btn-primary rounded-pill px-3" title="Edit Tool Content & Guide">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </a>

                                <!-- Direct Open Link -->
                                <a href="{{ $tool->live_url }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 32px; height: 32px; padding: 4px;" title="Open Tool in New Tab">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>

                                <!-- Delete Tool -->
                                <form action="{{ route('admin.tools.destroy', $tool->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete tool: {{ $tool->title }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 32px; height: 32px; padding: 4px;" title="Delete Tool">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-body-secondary">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <span class="fw-semibold">No tools match your search criteria.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tools->hasPages())
        <div class="card-footer bg-body border-0 py-3 d-flex justify-content-center">
            {{ $tools->links() }}
        </div>
    @endif
</div>
@endsection

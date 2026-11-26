@extends('layouts.admin')

@section('title', 'Manage Tools - Admin Panel')
@section('page_title', 'Tools Management')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Web Tools & Utilities</h4>
        <p class="text-body-secondary small mb-0">Manage frontend services, online text utilities, bulk network checkers, and diagnostic tools.</p>
    </div>
    <div class="d-flex gap-2">
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
                    <span class="text-body-secondary small fw-semibold">Total Tools</span>
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
                    <span class="text-body-secondary small fw-semibold">Active & Live</span>
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
                    <span class="text-body-secondary small fw-semibold">Public Services</span>
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
                    <span class="text-body-secondary small fw-semibold">Admin Suite Tools</span>
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
                    <input type="text" name="search" class="form-control bg-body border-start-0" placeholder="Search tools by title, slug, or description..." value="{{ $search ?? '' }}">
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

<!-- Tools List Table / Cards -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 50px;">Icon</th>
                    <th scope="col">Tool Name & Info</th>
                    <th scope="col">Category</th>
                    <th scope="col">Access URL</th>
                    <th scope="col" class="text-center">Status</th>
                    <th scope="col" class="text-end" style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tools as $tool)
                    <tr>
                        <td>
                            <div class="rounded-3 d-flex align-items-center justify-content-center p-2 {{ ($tool['type'] ?? '') === 'frontend' ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }}" style="width: 42px; height: 42px;">
                                <i class="bi {{ $tool['icon'] ?? 'bi-tools' }} fs-5"></i>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold text-body fs-6">{{ $tool['title'] }}</span>
                                @if(!empty($tool['badge']))
                                    <span class="badge {{ $tool['badge'] === 'NEW' ? 'bg-success' : ($tool['badge'] === 'POPULAR' ? 'bg-info text-dark' : 'bg-primary') }} rounded-pill" style="font-size: 0.65rem;">
                                        {{ $tool['badge'] }}
                                    </span>
                                @endif
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.65rem;">
                                    {{ strtoupper($tool['type'] ?? 'TOOL') }}
                                </span>
                            </div>
                            <p class="text-body-secondary small mb-0" style="max-width: 480px;">
                                {{ $tool['description'] }}
                            </p>
                        </td>
                        <td>
                            <span class="badge bg-body-tertiary text-body border rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.75rem;">
                                {{ $tool['category'] }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ url($tool['url']) }}" target="_blank" class="font-mono text-decoration-none small text-primary d-inline-flex align-items-center gap-1">
                                {{ $tool['url'] }}
                                <i class="bi bi-box-arrow-up-right" style="font-size: 0.75rem;"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            @if(!empty($tool['is_active']))
                                <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">
                                    <i class="bi bi-dot"></i> Active
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">
                                    <i class="bi bi-dot"></i> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <!-- Status Toggle Form -->
                                <form action="{{ route('admin.tools.toggle') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $tool['id'] }}">
                                    <button type="submit" class="btn btn-sm btn-icon {{ !empty($tool['is_active']) ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ !empty($tool['is_active']) ? 'Deactivate Tool' : 'Activate Tool' }}">
                                        <i class="bi {{ !empty($tool['is_active']) ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                    </button>
                                </form>

                                <!-- Edit Modal Trigger -->
                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $tool['id'] }}" title="Edit Details">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Direct Open Link -->
                                <a href="{{ url($tool['url']) }}" target="_blank" class="btn btn-sm btn-icon btn-outline-primary" title="Open Tool in New Tab">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade text-start" id="editModal{{ $tool['id'] }}" tabindex="-1" aria-labelledby="editModalLabel{{ $tool['id'] }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <form action="{{ route('admin.tools.update') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $tool['id'] }}">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold" id="editModalLabel{{ $tool['id'] }}">Edit Tool: {{ $tool['title'] }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Title</label>
                                                    <input type="text" name="title" class="form-control" value="{{ $tool['title'] }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Category</label>
                                                    <input type="text" name="category" class="form-control" value="{{ $tool['category'] }}" required>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Badge (Optional)</label>
                                                        <input type="text" name="badge" class="form-control" value="{{ $tool['badge'] ?? '' }}" placeholder="e.g. NEW, PRO, POPULAR">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Bootstrap Icon</label>
                                                        <input type="text" name="icon" class="form-control" value="{{ $tool['icon'] ?? 'bi-tools' }}" placeholder="e.g. bi-type">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Description</label>
                                                    <textarea name="description" class="form-control" rows="3" required>{{ $tool['description'] }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0 pt-0">
                                                <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Manage Tools - Admin Panel')
@section('page_title', 'Tools Management')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Web Tools & Utilities</h4>
        <p class="text-body-secondary mb-0">Manage frontend services, online text utilities, bulk network checkers, guides, and diagnostic tools.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.tools.create') }}" class="btn btn-primary rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i> Add New Tool
        </a>
        <a href="{{ route('services.convertcase') }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3">
            <i class="bi bi-type me-1"></i> Convert Case
        </a>
        <a href="{{ route('services.bulk-ip-checker') }}" target="_blank" class="btn btn-outline-info rounded-pill px-3">
            <i class="bi bi-hdd-network me-1"></i> Bulk IP Checker
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
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search by title, description, slug..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill">Filter</button>
                @if($search || $selectedCategory || $selectedStatus)
                    <a href="{{ route('admin.tools.index') }}" class="btn btn-outline-secondary rounded-pill" title="Reset Filters"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tools List Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Tool & URL</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Content & Features</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tools as $tool)
                    @php
                        $isModel = is_object($tool);
                        $toolId = $isModel ? $tool->id : ($tool['id'] ?? $tool['slug']);
                        $toolSlug = $isModel ? $tool->slug : ($tool['slug'] ?? $toolId);
                        $toolTitle = $isModel ? $tool->title : ($tool['title'] ?? '');
                        $toolCategory = $isModel ? $tool->category : ($tool['category'] ?? 'Utilities');
                        $toolIcon = $isModel ? ($tool->icon ?? 'bi-tools') : ($tool['icon'] ?? 'bi-tools');
                        $toolBadge = $isModel ? $tool->badge : ($tool['badge'] ?? '');
                        $toolType = $isModel ? ($tool->type ?? 'frontend') : ($tool['type'] ?? 'frontend');
                        $toolActive = $isModel ? $tool->is_active : !empty($tool['is_active']);
                        $toolSummary = $isModel ? ($tool->summary ?? '') : ($tool['description'] ?? '');
                        $toolUrl = $isModel ? $tool->live_url : ($tool['url'] ?? '/services/' . $toolSlug);
                        $hasToc = $isModel ? $tool->enable_toc : true;
                        $faqCount = $isModel && is_array($tool->faqs) ? count($tool->faqs) : 0;
                        $hasContent = $isModel && !empty($tool->content);
                    @endphp
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary-subtle text-primary p-2 rounded-3 text-center" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="bi {{ $toolIcon }} fs-5"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fw-bold mb-0 text-body">
                                            <a href="{{ route('admin.tools.edit', $toolId) }}" class="text-decoration-none text-body hover-gradient">
                                                {{ $toolTitle }}
                                            </a>
                                        </h6>
                                        @if(!empty($toolBadge))
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0" style="font-size: 0.75rem;">{{ $toolBadge }}</span>
                                        @endif
                                    </div>
                                    <span class="text-muted small d-block font-mono">{{ $toolUrl }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-body-secondary rounded-pill px-3 py-1">{{ $toolCategory }}</span>
                        </td>
                        <td>
                            @if($toolType === 'frontend')
                                <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1"><i class="bi bi-globe me-1"></i> Public Frontend</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1"><i class="bi bi-sliders me-1"></i> Admin Suite</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @if($hasContent)
                                    <span class="badge bg-success-subtle text-success rounded-pill" title="Article content configured">
                                        <i class="bi bi-file-text me-1"></i> Guide Added
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted rounded-pill" title="No article content yet">
                                        <i class="bi bi-file-earmark me-1"></i> No Content
                                    </span>
                                @endif

                                @if($hasToc)
                                    <span class="badge bg-primary-subtle text-primary rounded-pill" title="Auto Table of Contents active">
                                        <i class="bi bi-list-nested me-1"></i> TOC
                                    </span>
                                @endif

                                @if($faqCount > 0)
                                    <span class="badge bg-purple-subtle text-purple rounded-pill" style="background-color: rgba(147, 51, 234, 0.12); color: #9333ea;" title="{{ $faqCount }} FAQs configured">
                                        <i class="bi bi-question-circle me-1"></i> {{ $faqCount }} FAQs
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <form action="{{ route('admin.tools.toggle-status', $toolId) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm rounded-pill px-3 {{ $toolActive ? 'btn-success' : 'btn-outline-secondary' }}" title="Click to toggle status">
                                    <i class="bi {{ $toolActive ? 'bi-check-circle-fill' : 'bi-x-circle' }} me-1"></i>
                                    {{ $toolActive ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ $toolUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="View Tool Frontend">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <a href="{{ route('admin.tools.edit', $toolId) }}" class="btn btn-sm btn-primary rounded-pill px-3" title="Edit Tool Content & FAQs">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </a>
                                @if($isModel && !in_array($tool->slug, ['convertcase', 'bulk-website-ip-checker']))
                                    <form action="{{ route('admin.tools.destroy', $tool->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tool?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Delete Tool">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-tools fs-1 d-block mb-2 text-muted"></i>
                            <p class="mb-0">No tools found matching your search.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tools instanceof \Illuminate\Pagination\LengthAwarePaginator && $tools->hasPages())
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $tools->links() }}
        </div>
    @endif
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Manage Pages')
@section('page_title', 'Custom Pages')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Custom Dynamic Pages</h4>
            <p class="text-body-secondary small mb-0">Create and manage static/content pages (About Us, Privacy Policy, Terms, Landing Pages).</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-circle me-1"></i> Create New Page
        </a>
    </div>

    <!-- Overview Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Total Pages</span>
                        <h4 class="fw-bold mb-0 text-gradient mt-1">{{ number_format($totalPages) }}</h4>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-4">
                        <i class="bi bi-file-earmark-richtext fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Published Pages</span>
                        <h4 class="fw-bold mb-0 text-success mt-1">{{ number_format($publishedPages) }}</h4>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-4">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Draft Pages</span>
                        <h4 class="fw-bold mb-0 text-secondary mt-1">{{ number_format($draftPages) }}</h4>
                    </div>
                    <div class="bg-secondary-subtle text-secondary p-3 rounded-4">
                        <i class="bi bi-pencil-square fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="card border-0 shadow-sm p-3 mb-4 rounded-3 bg-body">
        <form action="{{ route('admin.pages.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-body-tertiary"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search pages by title or slug..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3 col-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div class="col-md-3 col-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Pages Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-body overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Image</th>
                        <th>Page Title & URL</th>
                        <th>Status</th>
                        <th>Navigation Placement</th>
                        <th>Order</th>
                        <th>Last Updated</th>
                        <th style="width: 140px;" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                        <tr>
                            <td>
                                @if($page->image_url)
                                    <img src="{{ $page->image_url }}" alt="{{ $page->title }}" class="rounded-3 object-fit-cover shadow-sm" style="width: 50px; height: 40px;">
                                @else
                                    <div class="bg-body-secondary text-body-secondary rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 40px;">
                                        <i class="bi bi-file-earmark-text fs-5"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.pages.edit', $page->id) }}" class="fw-bold text-decoration-none text-body">
                                    {{ $page->title }}
                                </a>
                                <div class="text-muted small font-monospace">
                                    /page/{{ $page->slug }}
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('admin.pages.toggle-status', $page->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($page->status === 'published')
                                        <button type="submit" class="badge bg-success-subtle text-success border-0 rounded-pill px-3 py-1 cursor-pointer" title="Click to unpublish">
                                            <i class="bi bi-check-circle me-1"></i> Published
                                        </button>
                                    @else
                                        <button type="submit" class="badge bg-secondary-subtle text-secondary border-0 rounded-pill px-3 py-1 cursor-pointer" title="Click to publish">
                                            <i class="bi bi-clock me-1"></i> Draft
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($page->show_in_navbar)
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1" title="Visible in top navigation">
                                            <i class="bi bi-layout-text-window-reverse me-1"></i> Navbar
                                        </span>
                                    @endif
                                    @if($page->show_in_footer)
                                        <span class="badge bg-info-subtle text-info rounded-pill px-2 py-1" title="Visible in footer links">
                                            <i class="bi bi-layout-text-sidebar-reverse me-1"></i> Footer
                                        </span>
                                    @endif
                                    @if(!$page->show_in_navbar && !$page->show_in_footer)
                                        <span class="text-muted small">Hidden</span>
                                    @endif
                                </div>
                            </td>
                            <td class="small fw-semibold">{{ $page->order }}</td>
                            <td class="small text-muted">{{ $page->updated_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="btn btn-outline-secondary" title="View Page">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-outline-primary" title="Edit Page">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePageModal-{{ $page->id }}" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade text-start" id="deletePageModal-{{ $page->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-danger">
                                                    <i class="bi bi-exclamation-triangle me-2"></i> Delete Page
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete page <strong>"{{ $page->title }}"</strong>? This action cannot be undone.
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Confirm Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                                No pages found. Click "Create New Page" to add your first custom page!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pages->hasPages())
            <div class="card-footer bg-transparent py-3 d-flex justify-content-center">
                {{ $pages->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection

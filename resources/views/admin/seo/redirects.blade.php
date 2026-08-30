@extends('layouts.admin')

@section('title', '301 SEO URL Redirects')
@section('page_title', '301 URL Redirects')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">301 Permanent Redirects Manager</h4>
            <p class="text-body-secondary small mb-0">Preserve search rankings and backlink authority when URL slugs or legacy links change.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.seo.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to SEO Dashboard
            </a>
            <button type="button" class="btn btn-primary rounded-pill px-3.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#createRedirectModal">
                <i class="bi bi-plus-lg me-1"></i> Add Custom Redirect
            </button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Active Redirect Rules</span>
                        <h3 class="fw-bold mb-0 text-primary mt-1">{{ $redirects->total() }}</h3>
                    </div>
                    <div class="p-3 bg-primary-subtle text-primary rounded-4">
                        <i class="bi bi-signpost-split-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Total Redirect Hits Logged</span>
                        <h3 class="fw-bold mb-0 text-success mt-1">{{ number_format($totalHits) }}</h3>
                    </div>
                    <div class="p-3 bg-success-subtle text-success rounded-4">
                        <i class="bi bi-cursor-fill fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="card border-0 shadow-sm p-3 rounded-4 bg-body mb-4">
        <form action="{{ route('admin.seo.redirects') }}" method="GET" class="row g-2">
            <div class="col-md-10">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-body-tertiary"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by old URL or target URL..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3">Search</button>
            </div>
        </form>
    </div>

    <!-- Redirects Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-body overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Old URL (Source)</th>
                        <th>Target URL (Destination)</th>
                        <th>Type</th>
                        <th>Hits</th>
                        <th>Last Used</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($redirects as $r)
                        <tr>
                            <td>
                                <code class="text-danger fw-semibold">{{ $r->old_url }}</code>
                            </td>
                            <td>
                                <code class="text-success fw-semibold">{{ $r->new_url }}</code>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5">
                                    {{ $r->status_code }} Permanent
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $r->hits }} hits
                                </span>
                            </td>
                            <td>
                                <small class="text-body-secondary">
                                    {{ $r->last_used_at ? $r->last_used_at->diffForHumans() : 'Never' }}
                                </small>
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.seo.redirects.destroy', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this redirect rule?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete Redirect">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-signpost-split fs-1 d-block mb-2 text-muted"></i>
                                No custom 301 redirect rules configured yet. When you change an article's slug, 301 redirects are created automatically.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($redirects->hasPages())
            <div class="p-3 border-top bg-body-tertiary">
                {{ $redirects->links() }}
            </div>
        @endif
    </div>

    <!-- Create Redirect Modal -->
    <div class="modal fade" id="createRedirectModal" tabindex="-1" aria-labelledby="createRedirectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('admin.seo.redirects.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold" id="createRedirectModalLabel"><i class="bi bi-signpost-split text-primary me-2"></i> Add 301 Redirect Rule</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-2">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Old URL Path *</label>
                            <input type="text" name="old_url" class="form-control" placeholder="/blog/old-article-slug" required>
                            <small class="text-body-secondary">The relative path that search engines or old backlinks visit.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Target URL Path or External Link *</label>
                            <input type="text" name="new_url" class="form-control" placeholder="/blog/new-article-slug" required>
                            <small class="text-body-secondary">Where the user & search engines should be redirected.</small>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small">HTTP Status Code</label>
                            <select name="status_code" class="form-select">
                                <option value="301" selected>301 Moved Permanently (Passes SEO authority & PageRank)</option>
                                <option value="302">302 Found / Temporary (For temporary maintenance)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Save Redirect</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.admin')

@section('title', 'Manage Articles')
@section('page_title', 'All Articles')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Articles & Posts</h4>
            <p class="text-body-secondary small mb-0">Create, edit, and organize publications.</p>
        </div>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Article
        </a>
    </div>

    <!-- SEO Internal Linking Stats Overview -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Total Articles</span>
                        <h4 class="fw-bold mb-0 text-gradient mt-1">{{ number_format($totalArticlesCount) }}</h4>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-4">
                        <i class="bi bi-journal-richtext fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Well-Linked Articles</span>
                        <h4 class="fw-bold mb-0 text-success mt-1">{{ number_format($linkedArticlesCount) }}</h4>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-4">
                        <i class="bi bi-link-45deg fs-4"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.posts.index', ['link_status' => 'inlinked']) }}" class="text-decoration-none small text-success fw-semibold">
                        View inlinked &rarr;
                    </a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100 {{ $orphanArticlesCount > 0 ? 'border-start border-warning border-4' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Orphan Articles (0 Inlinks)</span>
                        <h4 class="fw-bold mb-0 text-warning mt-1">{{ number_format($orphanArticlesCount) }}</h4>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-4">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('admin.posts.index', ['link_status' => 'orphan']) }}" class="text-decoration-none small text-warning fw-semibold">
                        Filter orphan posts &rarr;
                    </a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">SEO Link Health</span>
                        <h4 class="fw-bold mb-0 text-info mt-1">
                            {{ $totalArticlesCount > 0 ? round(($linkedArticlesCount / $totalArticlesCount) * 100) : 100 }}%
                        </h4>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-4">
                        <i class="bi bi-speedometer2 fs-4"></i>
                    </div>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $totalArticlesCount > 0 ? ($linkedArticlesCount / $totalArticlesCount) * 100 : 100 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="card border-0 shadow-sm p-3 mb-4 rounded-3 bg-body">
        <form action="{{ route('admin.posts.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-body-tertiary"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by title..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-2 col-6">
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 col-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div class="col-md-3 col-6">
                <select name="link_status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Link Statuses</option>
                    <option value="orphan" {{ request('link_status') === 'orphan' ? 'selected' : '' }}>⚠️ Orphan (0 Inlinks)</option>
                    <option value="inlinked" {{ request('link_status') === 'inlinked' ? 'selected' : '' }}>🔗 Inlinked (&ge; 1 Inlinks)</option>
                    <option value="no_outlinks" {{ request('link_status') === 'no_outlinks' ? 'selected' : '' }}>🚫 No Outgoing Links</option>
                </select>
            </div>

            <div class="col-md-2 col-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                @if(request()->hasAny(['search', 'category', 'status', 'link_status']))
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Posts Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">Image</th>
                        <th>Article Details</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Links (SEO)</th>
                        <th>Status</th>
                        <th>Badges</th>
                        <th>Views</th>
                        <th style="width: 140px;" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>
                                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="rounded-3 object-fit-cover shadow-sm" style="width: 54px; height: 44px;">
                            </td>
                            <td>
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="fw-bold text-decoration-none text-body">
                                    {{ Str::limit($post->title, 45) }}
                                </a>
                                <div class="text-muted small">
                                    {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Created ' . $post->created_at->format('M d, Y') }} &bull; {{ $post->reading_time }} min read
                                </div>
                            </td>
                            <td>
                                @if($post->category)
                                    <span class="badge" style="background-color: {{ $post->category->color }};">{{ $post->category->name }}</span>
                                @else
                                    <span class="text-muted small">None</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1 small">
                                    <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="rounded-circle" width="20" height="20">
                                    <span>{{ $post->author->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center gap-1">
                                        <!-- Incoming Links Badge -->
                                        @if($post->incoming_links_count > 0)
                                            <a href="#inlinksModal-{{ $post->id }}" data-bs-toggle="modal" class="badge bg-primary-subtle text-primary text-decoration-none rounded-pill px-2 py-1" title="Click to view {{ $post->incoming_links_count }} incoming link sources">
                                                <i class="bi bi-box-arrow-in-down-right me-1"></i>{{ $post->incoming_links_count }} In
                                            </a>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1" title="0 incoming internal links">
                                                <i class="bi bi-box-arrow-in-down-right me-1"></i>0 In
                                            </span>
                                        @endif

                                        <!-- Outgoing Links Badge -->
                                        <span class="badge bg-body-secondary text-body border rounded-pill px-2 py-1" title="Outgoing links in content: {{ $post->outgoing_internal_count }} internal, {{ $post->outgoing_external_count }} external">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>{{ $post->outgoing_links_count }} Out
                                        </span>
                                    </div>

                                    <!-- Orphan Badge -->
                                    @if($post->is_orphan)
                                        <div>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill small px-2 py-1" title="Orphan Article: No other articles link to this post">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Orphan
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Inlinks Source Modal -->
                                @if($post->incoming_links_count > 0)
                                    <div class="modal fade text-start" id="inlinksModal-{{ $post->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0 shadow">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold">
                                                        <i class="bi bi-link-45deg me-2 text-primary"></i> Incoming Links for "{{ Str::limit($post->title, 35) }}"
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="small text-muted mb-3">The following articles contain links directing to this post:</p>
                                                    <div class="list-group list-group-flush rounded-3 border">
                                                        @foreach($post->incoming_links as $src)
                                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                <span class="small fw-semibold text-truncate me-2">{{ $src['title'] }}</span>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="{{ route('blog.show', $src['slug']) }}" target="_blank" class="btn btn-outline-secondary btn-sm py-0 px-2" title="View"><i class="bi bi-eye"></i></a>
                                                                    <a href="{{ route('admin.posts.edit', $src['id']) }}" class="btn btn-outline-primary btn-sm py-0 px-2" title="Edit"><i class="bi bi-pencil"></i></a>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($post->status === 'published')
                                    <span class="badge bg-success-subtle text-success">Published</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <form action="{{ route('admin.posts.toggle-featured', $post->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm p-1 {{ $post->is_featured ? 'text-warning' : 'text-muted' }}" title="Toggle Featured">
                                            <i class="bi bi-star{{ $post->is_featured ? '-fill' : '' }} fs-6"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.posts.toggle-trending', $post->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm p-1 {{ $post->is_trending ? 'text-danger' : 'text-muted' }}" title="Toggle Trending">
                                            <i class="bi bi-fire fs-6"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="small fw-semibold">{{ number_format($post->views_count) }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn btn-outline-secondary" title="View Article"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-outline-primary" title="Edit Article"><i class="bi bi-pencil"></i></a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePostModal-{{ $post->id }}" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade text-start" id="deletePostModal-{{ $post->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle me-2"></i> Delete Article</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete <strong>"{{ $post->title }}"</strong>? This action cannot be undone.
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST">
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
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                                No articles found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
            <div class="card-footer bg-transparent py-3 d-flex justify-content-center">
                {{ $posts->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection

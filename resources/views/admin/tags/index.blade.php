@extends('layouts.admin')

@section('title', 'Manage Tags')
@section('page_title', 'Tags')

@section('content')
    <div class="row g-4">
        <!-- Add Tag Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                <h5 class="fw-bold mb-3"><i class="bi bi-tag me-2 text-primary"></i> Add New Tag</h5>
                <form action="{{ route('admin.tags.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tag Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Next.js" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Slug (Optional)</label>
                        <input type="text" name="slug" class="form-control" placeholder="auto-generated-if-empty">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Create Tag
                    </button>
                </form>
            </div>
        </div>

        <!-- Tags List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-body">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-tags me-2 text-primary"></i> Existing Tags</h6>
                    <span class="badge bg-secondary-subtle text-secondary">{{ $tags->total() }} Total</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tag Name</th>
                                <th>Slug</th>
                                <th>Articles Count</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tags as $tag)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fs-6">#{{ $tag->name }}</span>
                                    </td>
                                    <td class="small text-muted font-monospace">{{ $tag->slug }}</td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary">{{ $tag->posts_count }} posts</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTagModal-{{ $tag->id }}" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteTagModal-{{ $tag->id }}" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade text-start" id="editTagModal-{{ $tag->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.tags.update', $tag->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Edit Tag</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold">Name *</label>
                                                                <input type="text" name="name" class="form-control" value="{{ $tag->name }}" required>
                                                            </div>
                                                            <div class="mb-0">
                                                                <label class="form-label small fw-semibold">Slug</label>
                                                                <input type="text" name="slug" class="form-control" value="{{ $tag->slug }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade text-start" id="deleteTagModal-{{ $tag->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle me-2"></i> Delete Tag</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete tag <strong>"#{{ $tag->name }}"</strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST">
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
                                    <td colspan="4" class="text-center py-4 text-muted">No tags created yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tags->hasPages())
                    <div class="card-footer bg-transparent py-3 d-flex justify-content-center">
                        {{ $tags->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

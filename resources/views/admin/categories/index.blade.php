@extends('layouts.admin')

@section('title', 'Manage Categories')
@section('page_title', 'Categories')

@section('content')
    <div class="row g-4">
        <!-- Add Category Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                <h5 class="fw-bold mb-3"><i class="bi bi-folder-plus me-2 text-primary"></i> Add New Category</h5>
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Category Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Artificial Intelligence" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Slug (Optional)</label>
                        <input type="text" name="slug" class="form-control" placeholder="auto-generated-if-empty">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Color Theme</label>
                        <input type="color" name="color" class="form-control form-control-color w-100" value="#0d6efd" title="Choose color">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief category description"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Image URL (Optional)</label>
                        <input type="url" name="image" class="form-control" placeholder="https://images.unsplash.com/...">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Create Category
                    </button>
                </form>
            </div>
        </div>

        <!-- Categories List -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-body">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-folder2 me-2 text-primary"></i> Existing Categories</h6>
                    <span class="badge bg-secondary-subtle text-secondary">{{ $categories->total() }} Total</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name & Slug</th>
                                <th>Color</th>
                                <th>Articles</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-body">{{ $category->name }}</div>
                                        <div class="text-muted small">/category/{{ $category->slug }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="d-inline-block rounded" style="width: 20px; height: 20px; background-color: {{ $category->color }};"></span>
                                            <span class="small font-monospace">{{ $category->color }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">{{ $category->posts_count }} posts</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal-{{ $category->id }}" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal-{{ $category->id }}" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade text-start" id="editCategoryModal-{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Edit Category</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold">Name *</label>
                                                                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold">Slug</label>
                                                                <input type="text" name="slug" class="form-control" value="{{ $category->slug }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold">Color</label>
                                                                <input type="color" name="color" class="form-control form-control-color w-100" value="{{ $category->color }}">
                                                            </div>
                                                            <div class="mb-0">
                                                                <label class="form-label small fw-semibold">Description</label>
                                                                <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
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
                                        <div class="modal fade text-start" id="deleteCategoryModal-{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle me-2"></i> Delete Category</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete category <strong>"{{ $category->name }}"</strong>? Posts under this category will have their category unlinked.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST">
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
                                    <td colspan="4" class="text-center py-4 text-muted">No categories created yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                    <div class="card-footer bg-transparent py-3 d-flex justify-content-center">
                        {{ $categories->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

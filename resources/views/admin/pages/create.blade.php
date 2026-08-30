@extends('layouts.admin')

@section('title', 'Create Page')
@section('page_title', 'Create Custom Page')

@push('styles')
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <style>
        .note-editor.note-frame {
            border-radius: 0.75rem;
            border-color: rgba(var(--bs-border-color-rgb), 0.8);
            overflow: hidden;
        }
        .note-toolbar {
            background: var(--bs-tertiary-bg) !important;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">Create Custom Page</h4>
            <p class="text-body-secondary small mb-0">Design content pages like About Us, Privacy Policy, Terms of Service, or custom landing pages.</p>
        </div>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Pages
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i> Please correct the following errors:</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.pages.store') }}" method="POST" enctype="multipart/form-data" id="pageForm">
        @csrf
        <div class="row g-4">
            <!-- Left Main Column: Page Content -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <!-- Page Title -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Page Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="pageTitle" class="form-control form-control-lg rounded-3 @error('title') is-invalid @enderror" placeholder="e.g. About Our Platform, Privacy Policy..." value="{{ old('title') }}" required autofocus>
                    </div>

                    <!-- Custom URL Slug -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">URL Slug <span class="text-muted">(Leave blank to auto-generate)</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-body-tertiary font-monospace">/page/</span>
                            <input type="text" name="slug" id="pageSlug" class="form-control font-monospace @error('slug') is-invalid @enderror" placeholder="about-us" value="{{ old('slug') }}">
                        </div>
                    </div>

                    <!-- Rich Content Editor -->
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Page Content <span class="text-danger">*</span></label>
                        <textarea name="content" id="summernoteEditor" class="form-control @error('content') is-invalid @enderror" rows="14">{{ old('content') }}</textarea>
                    </div>
                </div>

                <!-- SEO & Meta Card -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-search me-2 text-primary"></i> SEO Optimization</h5>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control rounded-pill" placeholder="Custom browser tab and search title" value="{{ old('meta_title') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Meta Description</label>
                        <textarea name="meta_description" class="form-control rounded-4" rows="2" placeholder="Brief summary for search engine result snippets">{{ old('meta_description') }}</textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control rounded-pill" placeholder="about, team, mission, privacy" value="{{ old('meta_keywords') }}">
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Publishing -->
            <div class="col-lg-4">
                <!-- Publication Controls -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-send me-2 text-primary"></i> Publication</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Status</label>
                        <select name="status" class="form-select rounded-pill">
                            <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Live immediately)</option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Private save)</option>
                        </select>
                    </div>

                    <hr class="my-3 opacity-25">

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm mb-2">
                        <i class="bi bi-check-lg me-1"></i> Save & Publish Page
                    </button>
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary w-100 rounded-pill">Cancel</a>
                </div>

                <!-- Navigation & Placement Options -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-layout-text-window me-2 text-primary"></i> Navigation Placement</h5>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="show_in_navbar" value="1" id="showInNavbar" {{ old('show_in_navbar') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small" for="showInNavbar">Show in Top Navbar</label>
                        <div class="text-muted small">Adds link to the public website main header navigation</div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="show_in_footer" value="1" id="showInFooter" {{ old('show_in_footer', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small" for="showInFooter">Show in Footer</label>
                        <div class="text-muted small">Displays link in the footer navigation</div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Display Order Index</label>
                        <input type="number" name="order" class="form-control rounded-pill" value="{{ old('order', 0) }}" min="0">
                        <small class="text-muted">Lower numbers appear first in menus.</small>
                    </div>
                </div>

                <!-- Featured Header Image -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-image me-2 text-primary"></i> Header Banner Image</h5>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Upload Image</label>
                        <input type="file" name="featured_image" class="form-control form-control-sm rounded-pill" accept="image/*">
                    </div>

                    <div class="text-center text-muted small my-2">-- OR --</div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold">External Image URL</label>
                        <input type="url" name="image_url" class="form-control form-control-sm rounded-pill" placeholder="https://images.unsplash.com/..." value="{{ old('image_url') }}">
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <!-- jQuery and Summernote JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#summernoteEditor').summernote({
                height: 360,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Auto slug generator from title
            $('#pageTitle').on('keyup', function() {
                var title = $(this).val();
                var slug = title.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
                $('#pageSlug').val(slug);
            });
        });
    </script>
@endpush

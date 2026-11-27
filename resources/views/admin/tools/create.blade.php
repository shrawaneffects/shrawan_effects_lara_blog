@extends('layouts.admin')

@section('title', 'Create Tool Content')
@section('page_title', 'Create New Tool')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">
@endpush

@section('content')
    <form action="{{ route('admin.tools.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Main Content & Editor -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tool Title *</label>
                        <input type="text" name="title" id="toolTitle" class="form-control form-control-lg fw-bold" placeholder="e.g. Bulk Website IP Checker & Subnet Analyzer" value="{{ old('title') }}" required autofocus>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">URL Slug (Auto-generated if empty)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-body-tertiary">/services/</span>
                                <input type="text" name="slug" id="toolSlug" class="form-control" placeholder="tool-slug" value="{{ old('slug') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted">Direct URL / Path (Optional override)</label>
                            <input type="text" name="url" class="form-control form-control-sm" placeholder="e.g. /services/bulk-website-ip-checker" value="{{ old('url') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Summary / Short Description</label>
                        <textarea name="summary" class="form-control" rows="2" placeholder="A concise 1-2 sentence description explaining what this tool does...">{{ old('summary') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold mb-0">Tool Article & Guide Content (Renders below tool)</label>
                            <span class="text-body-secondary small"><i class="bi bi-info-circle me-1"></i>Appears directly underneath the interactive tool widget</span>
                        </div>
                        <textarea name="content" id="summernoteEditor" class="form-control" rows="16" placeholder="Write full guides, documentation, FAQs, usage steps, and technical explanations here...">{{ old('content') }}</textarea>
                    </div>
                </div>

                <!-- Primary Meta Title, Meta Description & Canonical Tag Card -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4" id="primarySeoCard" style="border-left: 4px solid #00ff66 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-success bg-opacity-10 text-success rounded-3">
                                <i class="bi bi-search fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Primary Meta & Canonical SEO Settings</h5>
                                <small class="text-muted">Targeted meta tags used by Google, Bing, and Social Crawlers</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0" for="metaTitleInput">Meta Title (Browser Tab & Search Snippet)</label>
                            <small class="text-muted"><span id="metaTitleCount">0</span>/60 characters (Optimal: 50-60)</small>
                        </div>
                        <input type="text" name="meta_title" id="metaTitleInput" class="form-control" maxlength="70" placeholder="e.g. Bulk Website IP Checker & Subnet Clustering Tool | Shrawan Effects" value="{{ old('meta_title') }}">
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0" for="metaDescInput">Meta Description (Search Engine SERP Snippet)</label>
                            <small class="text-muted"><span id="metaDescCount">0</span>/160 characters (Optimal: 140-160)</small>
                        </div>
                        <textarea name="meta_description" id="metaDescInput" class="form-control" rows="3" maxlength="200" placeholder="e.g. Resolve up to 10,000+ domains to IPv4 & IPv6 addresses instantly. Detect Cloudflare/CDN proxies, datacenter hosting, and Class-C network footprints.">{{ old('meta_description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="metaKeywordsInput">Meta Keywords (Comma-separated)</label>
                        <input type="text" name="meta_keywords" id="metaKeywordsInput" class="form-control" placeholder="e.g. bulk ip checker, dns lookup, subnet analyzer, ip resolver" value="{{ old('meta_keywords') }}">
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold mb-1" for="canonicalUrlInput">Canonical URL (rel="canonical")</label>
                        <input type="url" name="canonical_url" id="canonicalUrlInput" class="form-control font-mono" placeholder="https://shrawaneffects.com/services/..." value="{{ old('canonical_url') }}">
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Classification -->
            <div class="col-lg-4">
                <div class="sticky-top-widget d-flex flex-column gap-4">
                    <!-- Publish Card -->
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                        <h5 class="fw-bold mb-3">Publish Settings</h5>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActiveSwitch">Active & Visible to Public</label>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="isFeaturedSwitch" value="1" {{ old('is_featured') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isFeaturedSwitch">Featured on Homepage / Nav</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-semibold shadow-sm">
                                <i class="bi bi-cloud-arrow-up me-1"></i> Save Tool Content
                            </button>
                            <a href="{{ route('admin.tools.index') }}" class="btn btn-outline-secondary rounded-pill py-2">
                                Cancel
                            </a>
                        </div>
                    </div>

                    <!-- Classification Card -->
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                        <h5 class="fw-bold mb-3">Tool Classification</h5>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <input type="text" name="category" list="categoriesList" class="form-control" placeholder="e.g. Text & Content Utilities" value="{{ old('category', 'Utilities') }}">
                            <datalist id="categoriesList">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}"></option>
                                @endforeach
                                <option value="Text & Content Utilities"></option>
                                <option value="Network & Infrastructure"></option>
                                <option value="SEO & Audit"></option>
                                <option value="Developer Tools"></option>
                            </datalist>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Bootstrap Icon</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-tools" id="iconPreview"></i></span>
                                    <input type="text" name="icon" id="iconInput" class="form-control" placeholder="bi-tools" value="{{ old('icon', 'bi-tools') }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Badge Tag</label>
                                <input type="text" name="badge" class="form-control" placeholder="e.g. NEW, PRO" value="{{ old('badge') }}">
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Tool Type</label>
                                <select name="type" class="form-select">
                                    <option value="frontend" {{ old('type', 'frontend') === 'frontend' ? 'selected' : '' }}>Public Frontend</option>
                                    <option value="admin" {{ old('type') === 'admin' ? 'selected' : '' }}>Admin Suite</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Display Order</label>
                                <input type="number" name="order" class="form-control" value="{{ old('order', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Centralized Media Library Integration Modals -->
    @include('admin.posts.partials.editor_media_modals')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
<script src="{{ asset('js/summernote-media-caption.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize Summernote with Media Library Picker
        if (typeof initSummernoteWithMedia === 'function') {
            initSummernoteWithMedia('#summernoteEditor');
        } else {
            $('#summernoteEditor').summernote({
                height: 420,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }

        // Auto-slug generator
        $('#toolTitle').on('keyup change', function() {
            if (!$('#toolSlug').val() || $('#toolSlug').data('manual') !== true) {
                let slug = $(this).val().toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#toolSlug').val(slug);
            }
        });

        $('#toolSlug').on('input', function() {
            $(this).data('manual', true);
        });

        // Icon preview
        $('#iconInput').on('input', function() {
            let iconClass = $(this).val().trim();
            if (iconClass) {
                $('#iconPreview').attr('class', 'bi ' + iconClass);
            }
        });

        // SEO Character counters
        function updateCounters() {
            let tLen = $('#metaTitleInput').val().length;
            $('#metaTitleCount').text(tLen).toggleClass('text-danger', tLen > 60);

            let dLen = $('#metaDescInput').val().length;
            $('#metaDescCount').text(dLen).toggleClass('text-danger', dLen > 160);
        }

        $('#metaTitleInput, #metaDescInput').on('input', updateCounters);
        updateCounters();
    });
</script>
@endpush

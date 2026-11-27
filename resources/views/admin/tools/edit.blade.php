@extends('layouts.admin')

@section('title', 'Edit Tool - ' . $tool->title)
@section('page_title', 'Edit Tool')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Edit Tool: {{ $tool->title }}</h4>
        <p class="text-body-secondary mb-0">Update guide content, Table of Contents, FAQs, and SEO configuration.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ $tool->live_url }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3">
            <i class="bi bi-box-arrow-up-right me-1"></i> View Live Tool
        </a>
        <a href="{{ route('admin.tools.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Tools
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <strong>Please resolve the following errors:</strong>
        </div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('admin.tools.update', $tool->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Left Column: Main Content & FAQs -->
        <div class="col-lg-8">
            <!-- Tool Basics & Rich Content -->
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tool Title *</label>
                    <input type="text" name="title" id="toolTitle" class="form-control form-control-lg fw-bold" placeholder="e.g. Bulk Website IP Checker & Subnet Analyzer" value="{{ old('title', $tool->title) }}" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted">URL Slug</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-tertiary">{{ url('/services') }}/</span>
                        <input type="text" name="slug" id="toolSlug" class="form-control font-mono" placeholder="tool-slug" value="{{ old('slug', $tool->slug) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Short Summary / Teaser</label>
                    <textarea name="summary" class="form-control" rows="2" placeholder="A concise 1-2 sentence description explaining what this tool does...">{{ old('summary', $tool->summary) }}</textarea>
                </div>

                <div class="mb-0">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0">Tool Guide & Documentation Content (Displayed directly below tool widget)</label>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3">Live Article Guide</span>
                    </div>
                    <textarea name="content" id="summernoteEditor" class="form-control" rows="14" placeholder="Write comprehensive guide content with H2/H3 headings, instructions, tables, and architecture explanations...">{{ old('content', $tool->content) }}</textarea>
                </div>
            </div>

            <!-- Frequently Asked Questions (FAQs) -->
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-question-circle-fill text-primary me-2"></i> Frequently Asked Questions (FAQs)
                        </h5>
                        <p class="text-body-secondary mb-0 small">Add Q&A pairs. These render in a clean accordion below the article and empower rich SEO snippets.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" onclick="addFaqRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add FAQ
                        </button>
                    </div>
                </div>

                <div id="faqsContainer" class="d-flex flex-column gap-3">
                    <!-- Dynamic FAQ rows will be appended here -->
                </div>
            </div>

            <!-- Primary SEO Meta Tags -->
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                <h5 class="fw-bold mb-3"><i class="bi bi-search me-2 text-primary"></i> Primary Search Engine Optimization (SEO)</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control rounded-pill" placeholder="Custom SEO title (falls back to tool title if empty)" value="{{ old('meta_title', $tool->meta_title) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Meta Description</label>
                        <textarea name="meta_description" class="form-control rounded-3" rows="3" placeholder="Brief search engine snippet describing features, speed, and privacy...">{{ old('meta_description', $tool->meta_description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control rounded-pill" placeholder="ip checker, bulk dns, subnet analyzer" value="{{ old('meta_keywords', $tool->meta_keywords) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Canonical URL</label>
                        <input type="url" name="canonical_url" class="form-control rounded-pill" placeholder="https://shrawaneffects.com/services/..." value="{{ old('canonical_url', $tool->canonical_url) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings & Selectors -->
        <div class="col-lg-4">
            <!-- Publishing & Visibility -->
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-toggles me-2 text-primary"></i> Status & Visibility</h5>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActiveCheck" {{ old('is_active', $tool->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="isActiveCheck">Live / Active Tool</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeaturedCheck" {{ old('is_featured', $tool->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="isFeaturedCheck">Feature on Homepage / Menu</label>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Display Order Priority</label>
                    <input type="number" name="order" class="form-control rounded-pill" value="{{ old('order', $tool->order) }}" min="0">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tool Environment</label>
                    <select name="type" class="form-select rounded-pill">
                        <option value="frontend" {{ old('type', $tool->type) === 'frontend' ? 'selected' : '' }}>Public Frontend Service</option>
                        <option value="admin" {{ old('type', $tool->type) === 'admin' ? 'selected' : '' }}>Admin Suite Utility</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-gradient w-100 py-2 fw-semibold rounded-pill shadow-sm">
                    <i class="bi bi-cloud-arrow-up me-2"></i> Update Tool
                </button>
            </div>

            <!-- Table of Contents (TOC) Settings -->
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-list-nested me-2 text-primary"></i> Table of Contents
                    </h5>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="enable_toc" value="1" id="enableTocCheck" {{ old('enable_toc', $tool->enable_toc) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="enableTocCheck">
                        Enable Table of Contents
                    </label>
                </div>
                <p class="text-body-secondary small mb-3">
                    Automatically generates an interactive Table of Contents from all &lt;h2&gt; and &lt;h3&gt; subheadings in your tool guide content.
                </p>

                <!-- Live TOC Headings Inspector -->
                <div class="p-3 bg-body-tertiary rounded-3 border">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-body small"><i class="bi bi-eye me-1"></i> Detected Headings:</span>
                        <span class="badge bg-primary rounded-pill" id="detectedHeadingsCount">0 found</span>
                    </div>
                    <ul class="list-unstyled mb-0 text-body-secondary small" id="detectedHeadingsList" style="max-height: 140px; overflow-y: auto;">
                        <li class="fst-italic text-muted">Scanning content for &lt;h2&gt; and &lt;h3&gt;...</li>
                    </ul>
                </div>
            </div>

            <!-- Classification & Appearance -->
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-palette me-2 text-primary"></i> Classification & Styling</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Category</label>
                    <input type="text" name="category" class="form-control rounded-pill" list="categorySuggestions" placeholder="e.g. Network & Infrastructure" value="{{ old('category', $tool->category) }}">
                    <datalist id="categorySuggestions">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Bootstrap Icon Class</label>
                    <div class="input-group">
                        <span class="input-group-text"><i id="iconPreview" class="bi {{ old('icon', $tool->icon ?? 'bi-tools') }}"></i></span>
                        <input type="text" name="icon" id="toolIconInput" class="form-control" placeholder="bi-tools" value="{{ old('icon', $tool->icon ?? 'bi-tools') }}">
                    </div>
                    <span class="text-muted small">e.g. bi-hdd-network, bi-type, bi-sliders, bi-shield-check</span>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Badge Label (Optional)</label>
                    <input type="text" name="badge" class="form-control rounded-pill" placeholder="e.g. POPULAR, NEW, PRO" value="{{ old('badge', $tool->badge) }}">
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold">Custom Target URL / Route (Optional)</label>
                    <input type="text" name="url" class="form-control rounded-pill" placeholder="/services/your-tool-slug" value="{{ old('url', $tool->url) }}">
                </div>
            </div>

            <!-- Related Tools Selector -->
            @php
                $selectedRelatedTools = old('related_tools', $tool->related_tools ?? []);
                if (!is_array($selectedRelatedTools)) $selectedRelatedTools = [];
            @endphp
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                <h5 class="fw-bold mb-2"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i> Related Tools</h5>
                <p class="text-body-secondary small mb-3">Select other tools to display in the "Related Tools" sidebar widget.</p>
                <div class="d-flex flex-column gap-2" style="max-height: 200px; overflow-y: auto;">
                    @forelse($allTools as $otherTool)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="related_tools[]" value="{{ $otherTool->id }}" id="rel-tool-{{ $otherTool->id }}" {{ in_array($otherTool->id, $selectedRelatedTools) || in_array($otherTool->slug, $selectedRelatedTools) ? 'checked' : '' }}>
                            <label class="form-check-label text-truncate" for="rel-tool-{{ $otherTool->id }}">
                                {{ $otherTool->title }} <span class="text-muted small">({{ $otherTool->category }})</span>
                            </label>
                        </div>
                    @empty
                        <span class="text-muted small">No other tools available.</span>
                    @endforelse
                </div>
            </div>

            <!-- Related Posts Selector -->
            @php
                $selectedRelatedPosts = old('related_posts', $tool->related_posts ?? []);
                if (!is_array($selectedRelatedPosts)) $selectedRelatedPosts = [];
            @endphp
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                <h5 class="fw-bold mb-2"><i class="bi bi-newspaper me-2 text-primary"></i> Related Articles</h5>
                <p class="text-body-secondary small mb-3">Select relevant blog articles to feature in the sidebar of this tool.</p>
                <div class="d-flex flex-column gap-2" style="max-height: 220px; overflow-y: auto;">
                    @forelse($allPosts as $post)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="related_posts[]" value="{{ $post->id }}" id="rel-post-{{ $post->id }}" {{ in_array($post->id, $selectedRelatedPosts) ? 'checked' : '' }}>
                            <label class="form-check-label text-truncate" for="rel-post-{{ $post->id }}">
                                {{ $post->title }}
                            </label>
                        </div>
                    @empty
                        <span class="text-muted small">No published blog articles yet.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Image Alt, Title & Caption Editor + Media Library Asset Inserter Modals -->
@include('admin.posts.partials.editor_media_modals')

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
<script src="{{ asset('js/summernote-media-caption.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize full-featured Summernote Rich Editor with Media Library integration
        initSummernoteWithMedia('#summernoteEditor');

        // Hook into Summernote change event for live TOC preview
        $('#summernoteEditor').on('summernote.change', function() {
            updateTocHeadingsPreview($(this).summernote('code'));
        });

        // Icon preview update
        $('#toolIconInput').on('input', function() {
            const iconClass = $(this).val().trim();
            $('#iconPreview').attr('class', 'bi ' + (iconClass || 'bi-tools'));
        });

        // Populate existing FAQs
        @if(!empty($tool->faqs) && is_array($tool->faqs))
            const existingFaqs = @json($tool->faqs);
            existingFaqs.forEach(faq => {
                addFaqRow(faq.question, faq.answer);
            });
        @endif

        // Trigger initial heading scan
        setTimeout(function() {
            const initialCode = $('#summernoteEditor').summernote('code') || $('#summernoteEditor').val();
            updateTocHeadingsPreview(initialCode);
        }, 300);
    });

    // Real-time TOC Headings Inspector
    function updateTocHeadingsPreview(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html || '';
        const headings = tempDiv.querySelectorAll('h2, h3');
        const list = $('#detectedHeadingsList');
        const countBadge = $('#detectedHeadingsCount');

        if (!list.length || !countBadge.length) return;

        countBadge.text(headings.length + ' found');

        if (headings.length === 0) {
            list.html('<li class="fst-italic text-muted">Add &lt;h2&gt; or &lt;h3&gt; headings in the editor to see live TOC preview...</li>');
            return;
        }

        let itemsHtml = '';
        headings.forEach(h => {
            const tag = h.tagName.toLowerCase();
            const text = h.innerText?.trim() || h.textContent?.trim() || 'Untitled Heading';
            const isH3 = tag === 'h3';
            itemsHtml += `<li class="${isH3 ? 'ps-3 text-truncate' : 'fw-semibold text-truncate'}"><span class="badge bg-secondary-subtle text-body-secondary me-1 py-0 px-1 font-mono">${tag.toUpperCase()}</span> ${escapeHtml(text)}</li>`;
        });
        list.html(itemsHtml);
    }

    // Dynamic FAQ Repeater
    let faqCount = 0;

    function addFaqRow(question = '', answer = '') {
        const container = document.getElementById('faqsContainer');
        const rowId = 'faq-row-' + faqCount;
        const index = faqCount;

        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'faq-item card border p-3 rounded-4 bg-body-tertiary shadow-none position-relative';
        div.innerHTML = `
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge bg-primary text-white rounded-pill px-3 py-1">
                    <i class="bi bi-question-circle me-1"></i> FAQ #<span class="faq-number">${index + 1}</span>
                </span>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle p-1" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;" onclick="removeFaqRow('${rowId}')" title="Delete FAQ">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold small mb-1">Question *</label>
                <input type="text" name="faqs[${index}][question]" class="form-control form-control-sm rounded-pill" placeholder="e.g. Is this IP checker free to use?" value="${escapeHtml(question)}" required>
            </div>
            <div>
                <label class="form-label fw-semibold small mb-1">Answer *</label>
                <textarea name="faqs[${index}][answer]" class="form-control form-control-sm rounded-3" rows="2" placeholder="e.g. Yes, 100% free with unlimited batch DNS resolving..." required>${escapeHtml(answer)}</textarea>
            </div>
        `;
        container.appendChild(div);
        faqCount++;
        renumberFaqs();
    }

    function removeFaqRow(rowId) {
        const el = document.getElementById(rowId);
        if (el) {
            el.remove();
            renumberFaqs();
        }
    }

    function renumberFaqs() {
        const rows = document.querySelectorAll('#faqsContainer .faq-item');
        rows.forEach((row, idx) => {
            const numBadge = row.querySelector('.faq-number');
            if (numBadge) numBadge.textContent = (idx + 1);
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>
@endpush
@endsection

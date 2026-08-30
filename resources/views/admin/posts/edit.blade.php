@extends('layouts.admin')

@section('title', 'Edit Article: ' . $post->title)
@section('page_title', 'Edit Article')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">
@endpush

@section('content')
    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Column: Main Editor -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Article Title *</label>
                        <input type="text" name="title" id="postTitle" class="form-control form-control-lg fw-bold" value="{{ old('title', $post->title) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">URL Slug</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-body-tertiary">{{ url('/blog') }}/</span>
                            <input type="text" name="slug" id="postSlug" class="form-control" value="{{ old('slug', $post->slug) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Excerpt / Short Summary</label>
                        <textarea name="excerpt" class="form-control" rows="2">{{ old('excerpt', $post->excerpt) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Article Content *</label>
                        <textarea name="content" id="summernoteEditor" class="form-control" rows="14" required>{{ old('content', $post->content) }}</textarea>
                    </div>
                </div>

                <!-- 2026 On-Page SEO Management Suite -->
                @include('admin.posts.partials.seo_panel')

                <!-- Custom FAQs Card -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-question-circle-fill me-2 text-primary"></i> Frequently Asked Questions (FAQs)
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="loadSampleFaqs()">
                                <i class="bi bi-lightning-charge me-1"></i> Sample FAQs
                            </button>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" onclick="addFaqRow()">
                                <i class="bi bi-plus-lg me-1"></i> Add FAQ
                            </button>
                        </div>
                    </div>
                    <p class="text-body-secondary small mb-3">
                        Add Question & Answer pairs. These will be displayed in an interactive accordion on the article and automatically indexed in Google FAQ Schema.
                    </p>

                    <div id="faqsContainer" class="d-flex flex-column gap-3">
                        <!-- FAQ rows dynamically added here -->
                    </div>
                </div>

                <!-- Schema.org Structured Data Card -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-diagram-3-fill me-2 text-primary"></i> Schema.org Structured Data
                        </h5>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3">SEO Rich Snippets</span>
                    </div>
                    <p class="text-body-secondary small mb-3">
                        Google uses Schema.org JSON-LD to display rich results (breadcrumbs, author, carousels, FAQs).
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Schema Type</label>
                        <select name="schema_type" id="schemaTypeSelect" class="form-select rounded-pill">
                            <option value="BlogPosting" {{ old('schema_type', $post->schema_type ?? 'BlogPosting') === 'BlogPosting' ? 'selected' : '' }}>BlogPosting (Standard blog post)</option>
                            <option value="Article" {{ old('schema_type', $post->schema_type) === 'Article' ? 'selected' : '' }}>Article (General news or technical article)</option>
                            <option value="TechArticle" {{ old('schema_type', $post->schema_type) === 'TechArticle' ? 'selected' : '' }}>TechArticle (Developer tutorials, API guides)</option>
                            <option value="NewsArticle" {{ old('schema_type', $post->schema_type) === 'NewsArticle' ? 'selected' : '' }}>NewsArticle (Time-sensitive news)</option>
                            <option value="HowTo" {{ old('schema_type', $post->schema_type) === 'HowTo' ? 'selected' : '' }}>HowTo (Step-by-step guides)</option>
                            <option value="FAQPage" {{ old('schema_type', $post->schema_type) === 'FAQPage' ? 'selected' : '' }}>FAQPage (Question & Answer pages)</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label fw-semibold small mb-0">Custom JSON-LD Schema (Optional Override / Additional Schema)</label>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary btn-sm py-0" onclick="insertFaqTemplate()">+ FAQ Template</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm py-0" onclick="insertHowToTemplate()">+ HowTo Template</button>
                            </div>
                        </div>
                        <textarea name="custom_schema" id="customSchemaTextarea" class="form-control font-monospace small rounded-3" rows="6" placeholder="// Leave blank for automatic Google-compliant BlogPosting Schema, or paste custom JSON-LD...">{{ old('custom_schema', $post->custom_schema) }}</textarea>
                        <small class="text-muted">If left blank, a valid Schema is automatically generated from post data.</small>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Publishing -->
            <div class="col-lg-4">
                <!-- Publishing Controls -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-send me-2 text-primary"></i> Publication</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Status</label>
                        <select name="status" class="form-select rounded-pill">
                            <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published (Live)</option>
                            <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                        </select>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeaturedCheck" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small" for="isFeaturedCheck">Mark as Featured (Hero section)</label>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="is_trending" value="1" id="isTrendingCheck" {{ old('is_trending', $post->is_trending) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small" for="isTrendingCheck">Mark as Trending</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-gradient flex-grow-1 py-2 fw-semibold rounded-pill shadow-sm">
                            <i class="bi bi-check-lg me-1"></i> Update Post
                        </button>
                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn btn-outline-secondary py-2 rounded-circle" title="View live">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Table of Contents (TOC) Settings -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-list-nested me-2 text-primary"></i> Table of Contents
                        </h5>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="enable_toc" value="1" id="enableTocCheck" {{ old('enable_toc', $post->enable_toc ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small" for="enableTocCheck">
                            Enable Table of Contents (TOC)
                        </label>
                    </div>
                    <p class="text-body-secondary small mb-3">
                        Automatically parses &lt;h2&gt; and &lt;h3&gt; subheadings in your article to build an interactive navigation box with smooth scrolling.
                    </p>

                    <!-- Real-time TOC Headings Inspector -->
                    <div class="p-3 bg-body-tertiary rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-body"><i class="bi bi-eye me-1"></i> Detected Headings:</span>
                            <span class="badge bg-primary rounded-pill" id="detectedHeadingsCount">0 found</span>
                        </div>
                        <ul class="list-unstyled mb-0 small text-body-secondary" id="detectedHeadingsList" style="max-height: 140px; overflow-y: auto;">
                            <li class="fst-italic text-muted">Type &lt;h2&gt; or &lt;h3&gt; in editor to preview headings...</li>
                        </ul>
                    </div>
                </div>

                <!-- Category -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-folder2 me-2 text-primary"></i> Category</h5>
                    <select name="category_id" class="form-select rounded-pill" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Featured Image -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-image me-2 text-primary"></i> Cover Image</h5>
                    <div class="mb-3 text-center">
                        <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="img-fluid rounded-3 mb-2" style="max-height: 140px; object-fit: cover;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Replace Image File</label>
                        <input type="file" name="featured_image" class="form-control rounded-pill" accept="image/*">
                        <small class="text-muted">JPG, PNG, WebP (Max 2MB)</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Or Image URL</label>
                        <input type="url" name="image_url" class="form-control rounded-pill" placeholder="https://..." value="{{ old('image_url', str_starts_with($post->featured_image ?? '', 'http') ? $post->featured_image : '') }}">
                    </div>
                </div>

                <!-- Tags Selection -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-tags me-2 text-primary"></i> Tags</h5>
                    @php $postTagIds = $post->tags->pluck('id')->toArray(); @endphp
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Select Tags</label>
                        <div class="d-flex flex-wrap gap-2" style="max-height: 160px; overflow-y: auto;">
                            @foreach($tags as $t)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $t->id }}" id="tag-{{ $t->id }}" {{ in_array($t->id, old('tags', $postTagIds)) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="tag-{{ $t->id }}">{{ $t->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Add New Tags</label>
                        <input type="text" name="new_tags" class="form-control rounded-pill" placeholder="Comma separated">
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#summernoteEditor').summernote({
                height: 350,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onChange: function(contents) {
                        updateDetectedHeadings(contents);
                    }
                }
            });

            function updateDetectedHeadings(html) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                const headings = tempDiv.querySelectorAll('h2, h3');
                const list = $('#detectedHeadingsList');
                const countBadge = $('#detectedHeadingsCount');

                countBadge.text(headings.length + ' found');

                if (headings.length === 0) {
                    list.html('<li class="fst-italic text-muted">No &lt;h2&gt; or &lt;h3&gt; headings detected yet.</li>');
                    return;
                }

                let itemsHtml = '';
                headings.forEach((h, index) => {
                    const tag = h.tagName.toLowerCase();
                    const text = h.innerText.trim() || 'Untitled Heading';
                    const indent = tag === 'h3' ? 'ps-3 text-muted' : 'fw-semibold text-primary';
                    itemsHtml += `<li class="mb-1 text-truncate ${indent}"><code>&lt;${tag}&gt;</code> ${text}</li>`;
                });
                list.html(itemsHtml);
            }

            // Initial scan
            setTimeout(() => {
                updateDetectedHeadings($('#summernoteEditor').val());
            }, 500);

            // Pre-populate existing FAQs
            @if(!empty($post->faqs) && is_array($post->faqs))
                const existingFaqs = @json($post->faqs);
                existingFaqs.forEach(faq => {
                    addFaqRow(faq.question, faq.answer);
                });
            @endif
        });

        // FAQ Management
        let faqCount = 0;

        function addFaqRow(question = '', answer = '') {
            const container = document.getElementById('faqsContainer');
            const rowId = 'faq-row-' + faqCount;
            const index = faqCount;

            const div = document.createElement('div');
            div.className = 'faq-item card border p-3 rounded-4 bg-body-tertiary shadow-none position-relative';
            div.id = rowId;
            div.innerHTML = `
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">
                        <i class="bi bi-question-circle me-1"></i> FAQ #<span class="faq-number">${index + 1}</span>
                    </span>
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle p-1" onclick="removeFaqRow('${rowId}')" title="Delete FAQ">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Question</label>
                    <input type="text" name="faqs[${index}][question]" class="form-control form-control-sm rounded-pill" placeholder="e.g. What are the system requirements?" value="${escapeHtml(question)}" required>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Answer</label>
                    <textarea name="faqs[${index}][answer]" class="form-control form-control-sm rounded-3" rows="2" placeholder="e.g. PHP 8.2 or higher with MySQL 8.0..." required>${escapeHtml(answer)}</textarea>
                </div>
            `;
            container.appendChild(div);
            faqCount++;
            renumberFaqs();
        }

        function removeFaqRow(rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                row.remove();
                renumberFaqs();
            }
        }

        function renumberFaqs() {
            const rows = document.querySelectorAll('#faqsContainer .faq-item');
            rows.forEach((row, i) => {
                const numBadge = row.querySelector('.faq-number');
                if (numBadge) numBadge.textContent = (i + 1);
            });
        }

        function loadSampleFaqs() {
            addFaqRow('What is the main advantage of this approach?', 'It provides high scalability, minimal overhead, and clean maintainable code.');
            addFaqRow('Is this compatible with modern browsers and mobile devices?', 'Yes, fully tested on all modern evergreen browsers including Chrome, Safari, and Firefox.');
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function insertFaqTemplate() {
            const faq = {
                ['@' + 'context']: "https://schema.org",
                ['@' + 'type']: "FAQPage",
                "mainEntity": [
                    {
                        ['@' + 'type']: "Question",
                        "name": "What is the key takeaway of this article?",
                        "acceptedAnswer": {
                            ['@' + 'type']: "Answer",
                            "text": "Write the concise answer here."
                        }
                    },
                    {
                        ['@' + 'type']: "Question",
                        "name": "How do I get started?",
                        "acceptedAnswer": {
                            ['@' + 'type']: "Answer",
                            "text": "Follow step 1 and step 2 outlined above."
                        }
                    }
                ]
            };
            $('#customSchemaTextarea').val(JSON.stringify(faq, null, 2));
        }

        function insertHowToTemplate() {
            const howTo = {
                ['@' + 'context']: "https://schema.org",
                ['@' + 'type']: "HowTo",
                "name": "How to Complete This Tutorial",
                "step": [
                    {
                        ['@' + 'type']: "HowToStep",
                        "name": "Step 1: Environment Setup",
                        "text": "Install prerequisites and prepare dependencies."
                    },
                    {
                        ['@' + 'type']: "HowToStep",
                        "name": "Step 2: Implementation",
                        "text": "Implement the core logic."
                    }
                ]
            };
            $('#customSchemaTextarea').val(JSON.stringify(howTo, null, 2));
        }
    </script>
    @endpush
@endsection

@php
    $postSeo = isset($post) && $post->seo ? $post->seo : null;
    $initialScore = $postSeo ? $postSeo->seo_score : 0;
    $initialStatus = $postSeo ? $postSeo->seo_status : 'needs_review';
    $secKeywordsStr = $postSeo ? $postSeo->secondaryKeywords->pluck('keyword')->implode(', ') : '';
    $entitiesStr = $postSeo ? $postSeo->entities->pluck('entity')->implode(', ') : '';
@endphp

<div class="card border-0 shadow-sm rounded-4 bg-body mb-4 overflow-hidden" id="seoManagementPanel">
    <!-- Panel Header with Realtime Score -->
    <div class="p-4 bg-body-tertiary border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-3 shadow-sm" style="width: 44px; height: 44px;">
                <i class="bi bi-graph-up-arrow fs-5"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0">2026 On-Page SEO Suite</h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5">Pro Engine</span>
                </div>
                <small class="text-body-secondary">Real-time keyword placement, Google SERP preview, search intent, and technical audits.</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <div class="d-flex align-items-center gap-2 justify-content-end">
                    <span class="text-body-secondary small fw-semibold">SEO Quality Score:</span>
                    <span id="seoScoreBadge" class="badge fs-6 rounded-pill px-3 py-1 bg-{{ $initialScore >= 75 ? 'success' : ($initialScore >= 50 ? 'warning' : 'danger') }}">
                        <span id="seoScoreValue">{{ $initialScore }}</span>/100
                    </span>
                </div>
                <small id="seoStatusLabel" class="text-body-secondary d-block mt-0.5" style="font-size: 0.78rem;">
                    {{ $initialStatus === 'good' ? 'Optimized' : ($initialStatus === 'critical' ? 'Critical Issues' : 'Recommendations Available') }}
                </small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="triggerLiveSeoAnalysis()">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh Audit
            </button>
        </div>
    </div>

    <!-- SEO Nav Tabs -->
    <div class="px-4 pt-3 border-bottom bg-body">
        <ul class="nav nav-pills nav-fill flex-column flex-sm-row gap-1" id="seoTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active py-2 rounded-3 text-start text-sm-center fw-semibold small" id="tab-keywords" data-bs-toggle="pill" data-bs-target="#panel-keywords" type="button" role="tab">
                    <i class="bi bi-bullseye me-1"></i> Keywords & Intent
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-2 rounded-3 text-start text-sm-center fw-semibold small" id="tab-serp" data-bs-toggle="pill" data-bs-target="#panel-serp" type="button" role="tab">
                    <i class="bi bi-google me-1"></i> SERP Preview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-2 rounded-3 text-start text-sm-center fw-semibold small" id="tab-technical" data-bs-toggle="pill" data-bs-target="#panel-technical" type="button" role="tab">
                    <i class="bi bi-sliders me-1"></i> Technical & Robots
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-2 rounded-3 text-start text-sm-center fw-semibold small" id="tab-social" data-bs-toggle="pill" data-bs-target="#panel-social" type="button" role="tab">
                    <i class="bi bi-share me-1"></i> Social Sharing
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-2 rounded-3 text-start text-sm-center fw-semibold small" id="tab-audit" data-bs-toggle="pill" data-bs-target="#panel-audit" type="button" role="tab">
                    <i class="bi bi-clipboard-check me-1"></i> Audit Breakdown (<span id="tabAuditBadge">0</span>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-2 rounded-3 text-start text-sm-center fw-semibold small" id="tab-links" data-bs-toggle="pill" data-bs-target="#panel-links" type="button" role="tab">
                    <i class="bi bi-link-45deg me-1"></i> Link Suggestions
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-2 rounded-3 text-start text-sm-center fw-semibold small" id="tab-rel-links" data-bs-toggle="pill" data-bs-target="#panel-rel-links" type="button" role="tab">
                    <i class="bi bi-diagram-2 me-1"></i> Follow/Rel Links (<span id="tabRelLinksBadge">0</span>)
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Contents -->
    <div class="card-body p-4">
        <div class="tab-content" id="seoTabContent">

            <!-- 1. KEYWORDS & SEARCH INTENT TAB -->
            <div class="tab-pane fade show active" id="panel-keywords" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Primary Target Keyword / Topic</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary"><i class="bi bi-key-fill text-primary"></i></span>
                            <input type="text" name="primary_keyword" id="seoPrimaryKeyword" class="form-control" placeholder="e.g. laravel 12 tutorial" value="{{ old('primary_keyword', $postSeo->primary_keyword ?? '') }}">
                        </div>
                        <small class="text-body-secondary d-block mt-1">The central concept or query this article aims to answer.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Search Intent</label>
                        <select name="search_intent" id="seoSearchIntent" class="form-select">
                            <option value="informational" {{ old('search_intent', $postSeo->search_intent ?? 'informational') === 'informational' ? 'selected' : '' }}>
                                📘 Informational (Tutorials, explanations, guides, how-to)
                            </option>
                            <option value="commercial" {{ old('search_intent', $postSeo->search_intent ?? '') === 'commercial' ? 'selected' : '' }}>
                                ⚖️ Commercial (Product comparisons, pros/cons, reviews, pricing)
                            </option>
                            <option value="transactional" {{ old('search_intent', $postSeo->search_intent ?? '') === 'transactional' ? 'selected' : '' }}>
                                🛒 Transactional (Downloads, purchases, signup, coupon, order)
                            </option>
                            <option value="navigational" {{ old('search_intent', $postSeo->search_intent ?? '') === 'navigational' ? 'selected' : '' }}>
                                🧭 Navigational (Brand portal, official login/portal page)
                            </option>
                        </select>
                        <small class="text-body-secondary d-block mt-1">Ensures content structure matches user expectations and Google helpful content signals.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Secondary & LSI Keywords (Comma-separated)</label>
                        <input type="text" name="secondary_keywords" id="seoSecondaryKeywords" class="form-control" placeholder="e.g. web development, php 8.2, backend guide" value="{{ old('secondary_keywords', $secKeywordsStr) }}">
                        <small class="text-body-secondary d-block mt-1">Related synonyms and subtopics naturally woven into the article.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Named Entities (Comma-separated)</label>
                        <input type="text" name="seo_entities" id="seoEntities" class="form-control" placeholder="e.g. Taylor Otwell, Laravel, MySQL, Redis, AWS" value="{{ old('seo_entities', $entitiesStr) }}">
                        <small class="text-body-secondary d-block mt-1">Key recognized entities (people, frameworks, technologies, organizations).</small>
                    </div>

                    <!-- Cannibalization Alert Box -->
                    <div class="col-12 d-none" id="cannibalizationAlertBox">
                        <div class="alert alert-warning border-warning d-flex align-items-start gap-3 mb-0 rounded-4">
                            <i class="bi bi-exclamation-triangle-fill fs-4 text-warning flex-shrink-0"></i>
                            <div>
                                <h6 class="fw-bold mb-1" id="cannibalizationTitle">Keyword Cannibalization Warning</h6>
                                <p class="mb-0 small" id="cannibalizationMsg"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. SERP PREVIEW TAB -->
            <div class="tab-pane fade" id="panel-serp" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold small mb-0">SEO Title</label>
                                <span class="badge bg-body-tertiary text-body border" id="titleCharCounter">0 / 60 chars</span>
                            </div>
                            <input type="text" name="seo_title" id="seoTitleInput" class="form-control" placeholder="Descriptive headline for search results" value="{{ old('seo_title', $postSeo->seo_title ?? ($post->meta_title ?? '')) }}">
                            <div class="progress mt-1.5" style="height: 4px;">
                                <div id="titleProgressBar" class="progress-bar bg-success" style="width: 0%;"></div>
                            </div>
                            <small class="text-body-secondary d-block mt-1" id="titleAdvice">Recommended: ~50-60 characters for standard desktop SERP.</small>
                        </div>

                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold small mb-0">Meta Description</label>
                                <span class="badge bg-body-tertiary text-body border" id="descCharCounter">0 / 160 chars</span>
                            </div>
                            <textarea name="meta_description" id="seoDescInput" class="form-control" rows="3" placeholder="Engaging summary for search result snippets...">{{ old('meta_description', $postSeo->meta_description ?? ($post->meta_description ?? '')) }}</textarea>
                            <div class="progress mt-1.5" style="height: 4px;">
                                <div id="descProgressBar" class="progress-bar bg-success" style="width: 0%;"></div>
                            </div>
                            <small class="text-body-secondary d-block mt-1" id="descAdvice">Recommended: ~120-160 characters for complete snippet display.</small>
                        </div>
                    </div>

                    <!-- Google SERP Visual Mockup -->
                    <div class="col-lg-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-body-secondary"><i class="bi bi-google me-1"></i> Google Search SERP Mockup</span>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary active" id="btnSerpDesktop" onclick="setSerpView('desktop')">
                                    <i class="bi bi-display me-1"></i> Desktop
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="btnSerpMobile" onclick="setSerpView('mobile')">
                                    <i class="bi bi-phone me-1"></i> Mobile
                                </button>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-4 bg-body border shadow-sm" id="serpPreviewContainer" style="max-width: 600px; font-family: Arial, sans-serif;">
                            <!-- Breadcrumb URL -->
                            <div class="d-flex align-items-center gap-2 mb-1 text-truncate" style="font-size: 0.82rem;">
                                <div class="bg-body-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                                    <i class="bi bi-globe2 text-muted" style="font-size: 11px;"></i>
                                </div>
                                <span class="text-dark-emphasis text-truncate" id="serpUrlDisplay">{{ url('/blog') }}/{{ $post->slug ?? 'example-article-slug' }}</span>
                            </div>

                            <!-- Title -->
                            <h5 class="fw-normal mb-1 text-primary text-truncate" id="serpTitleDisplay" style="color: #1a0dab !important; cursor: pointer; font-size: 1.15rem; line-height: 1.3;">
                                {{ $postSeo->seo_title ?? ($post->title ?? 'Article Headline Will Appear Here in Google') }}
                            </h5>

                            <!-- Description -->
                            <p class="mb-0 text-secondary" id="serpDescDisplay" style="color: #4d5156 !important; font-size: 0.88rem; line-height: 1.45;">
                                {{ $postSeo->meta_description ?? ($post->excerpt ?? 'A captivating meta description that summarizes the article value proposition for search engines.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. TECHNICAL & ROBOTS TAB -->
            <div class="tab-pane fade" id="panel-technical" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Custom Canonical URL (Override)</label>
                        <input type="url" name="canonical_url" id="seoCanonicalUrl" class="form-control" placeholder="https://example.com/original-article" value="{{ old('canonical_url', $postSeo->canonical_url ?? '') }}">
                        <small class="text-body-secondary d-block mt-1">Leave blank to default to this article's own permanent URL (Recommended for original content).</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Schema.org Structured Data Type</label>
                        <select name="schema_type" class="form-select">
                            <option value="BlogPosting" {{ old('schema_type', $postSeo->schema_type ?? 'BlogPosting') === 'BlogPosting' ? 'selected' : '' }}>BlogPosting (Standard blog articles)</option>
                            <option value="Article" {{ old('schema_type', $postSeo->schema_type ?? '') === 'Article' ? 'selected' : '' }}>Article (General news & thought leadership)</option>
                            <option value="TechArticle" {{ old('schema_type', $postSeo->schema_type ?? '') === 'TechArticle' ? 'selected' : '' }}>TechArticle (Developer tutorials, APIs, code guides)</option>
                            <option value="NewsArticle" {{ old('schema_type', $postSeo->schema_type ?? '') === 'NewsArticle' ? 'selected' : '' }}>NewsArticle (Time-sensitive industry news)</option>
                        </select>
                        <small class="text-body-secondary d-block mt-1">Outputs valid JSON-LD structured data with breadcrumbs and author entities.</small>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-body-tertiary">
                            <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-1">
                                <label class="form-check-label fw-semibold small" for="seoRobotsIndex">
                                    <i class="bi bi-robot me-1 text-primary"></i> Allow Indexing (robots: index)
                                </label>
                                <input class="form-check-input ms-0" type="checkbox" name="robots_index" value="1" id="seoRobotsIndex" {{ old('robots_index', $postSeo->robots_index ?? true) ? 'checked' : '' }}>
                            </div>
                            <small class="text-muted d-block" style="font-size: 0.78rem;">Permits Google, Bing, and other search engines to index this page.</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-body-tertiary">
                            <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-1">
                                <label class="form-check-label fw-semibold small" for="seoRobotsFollow">
                                    <i class="bi bi-compass me-1 text-primary"></i> Follow Links (robots: follow)
                                </label>
                                <input class="form-check-input ms-0" type="checkbox" name="robots_follow" value="1" id="seoRobotsFollow" {{ old('robots_follow', $postSeo->robots_follow ?? true) ? 'checked' : '' }}>
                            </div>
                            <small class="text-muted d-block" style="font-size: 0.78rem;">Allows search engine crawlers to follow links on this page.</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-body-tertiary">
                            <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0 mb-1">
                                <label class="form-check-label fw-semibold small" for="seoIncludeSitemap">
                                    <i class="bi bi-diagram-3 me-1 text-primary"></i> Include in XML Sitemap
                                </label>
                                <input class="form-check-input ms-0" type="checkbox" name="include_in_sitemap" value="1" id="seoIncludeSitemap" {{ old('include_in_sitemap', $postSeo->include_in_sitemap ?? true) ? 'checked' : '' }}>
                            </div>
                            <small class="text-muted d-block" style="font-size: 0.78rem;">Adds article to <a href="{{ url('/sitemap.xml') }}" target="_blank" class="text-decoration-none">sitemap.xml</a>.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. SOCIAL SHARING TAB -->
            <div class="tab-pane fade" id="panel-social" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-facebook me-1"></i> OpenGraph (Facebook, LinkedIn, Discord)</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">OG Title</label>
                            <input type="text" name="og_title" class="form-control" placeholder="Defaults to SEO Title" value="{{ old('og_title', $postSeo->og_title ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">OG Description</label>
                            <textarea name="og_description" class="form-control" rows="2" placeholder="Defaults to Meta Description">{{ old('og_description', $postSeo->og_description ?? '') }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold small">Custom OG Image URL</label>
                            <input type="url" name="og_image" class="form-control" placeholder="https://example.com/custom-share-card.jpg" value="{{ old('og_image', $postSeo->og_image ?? '') }}">
                            <small class="text-body-secondary">Leave blank to use the article featured cover image.</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3 text-info"><i class="bi bi-twitter-x me-1"></i> Twitter / X Cards</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Twitter Card Type</label>
                            <select name="twitter_card" class="form-select">
                                <option value="summary_large_image" {{ old('twitter_card', $postSeo->twitter_card ?? 'summary_large_image') === 'summary_large_image' ? 'selected' : '' }}>summary_large_image (Large prominent banner)</option>
                                <option value="summary" {{ old('twitter_card', $postSeo->twitter_card ?? '') === 'summary' ? 'selected' : '' }}>summary (Compact thumbnail card)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Twitter Title</label>
                            <input type="text" name="twitter_title" class="form-control" placeholder="Defaults to OpenGraph/SEO Title" value="{{ old('twitter_title', $postSeo->twitter_title ?? '') }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold small">Twitter Description</label>
                            <textarea name="twitter_description" class="form-control" rows="2" placeholder="Defaults to OpenGraph Description">{{ old('twitter_description', $postSeo->twitter_description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. AUDIT BREAKDOWN TAB -->
            <div class="tab-pane fade" id="panel-audit" role="tabpanel">
                <!-- 7 Categories Progress Gauges -->
                <div class="row g-3 mb-4" id="categoryGaugesContainer">
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-body-tertiary text-center">
                            <small class="text-body-secondary d-block fw-semibold mb-1">Technical SEO</small>
                            <h5 class="fw-bold mb-0 text-primary" id="catScoreTechnical">-- / 25</h5>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-body-tertiary text-center">
                            <small class="text-body-secondary d-block fw-semibold mb-1">Content & Hierarchy</small>
                            <h5 class="fw-bold mb-0 text-primary" id="catScoreContent">-- / 25</h5>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-body-tertiary text-center">
                            <small class="text-body-secondary d-block fw-semibold mb-1">On-Page & Keyword</small>
                            <h5 class="fw-bold mb-0 text-primary" id="catScoreOnPage">-- / 20</h5>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-body-tertiary text-center">
                            <small class="text-body-secondary d-block fw-semibold mb-1">Internal Linking</small>
                            <h5 class="fw-bold mb-0 text-primary" id="catScoreLinks">-- / 10</h5>
                        </div>
                    </div>
                </div>

                <!-- Detailed Audit Rules List -->
                <div class="d-flex flex-column gap-2" id="auditRulesListContainer">
                    <div class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        <span>Evaluating SEO quality rules...</span>
                    </div>
                </div>
            </div>

            <!-- 6. LINK SUGGESTIONS TAB -->
            <div class="tab-pane fade" id="panel-links" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0">Recommended Internal Links</h6>
                        <small class="text-body-secondary">Topically relevant published articles you can link to from this content to boost PageRank distribution.</small>
                    </div>
                </div>

                <div class="d-flex flex-column gap-3" id="linkSuggestionsContainer">
                    <div class="text-center py-4 text-muted">
                        <span>Type content or click "Refresh Audit" to discover internal link opportunities.</span>
                    </div>
                </div>
            </div>

            <!-- 7. LINKS & REL FOLLOW/NOFOLLOW AUDITOR TAB -->
            <div class="tab-pane fade" id="panel-rel-links" role="tabpanel">
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-body-tertiary text-center">
                            <small class="text-body-secondary d-block fw-semibold mb-1">Internal Dofollow</small>
                            <h4 class="fw-bold mb-0 text-success" id="editorInternalDofollow">0</h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-body-tertiary text-center">
                            <small class="text-body-secondary d-block fw-semibold mb-1">Internal Nofollow</small>
                            <h4 class="fw-bold mb-0 text-warning" id="editorInternalNofollow">0</h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-body-tertiary text-center">
                            <small class="text-body-secondary d-block fw-semibold mb-1">External Dofollow</small>
                            <h4 class="fw-bold mb-0 text-primary" id="editorExternalDofollow">0</h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-body-tertiary text-center">
                            <small class="text-body-secondary d-block fw-semibold mb-1">External Nofollow/Rel</small>
                            <h4 class="fw-bold mb-0 text-info" id="editorExternalNofollow">0</h4>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-2">Detected Links in Article Body</h6>
                <div class="d-flex flex-column gap-2" id="editorLinksListContainer">
                    <div class="text-center py-4 text-muted border rounded-3 bg-body-tertiary">
                        No links detected yet in the content editor.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    let seoAnalysisDebounceTimer = null;

    function triggerLiveSeoAnalysis() {
        clearTimeout(seoAnalysisDebounceTimer);

        const titleVal = document.getElementById('postTitle')?.value || '';
        const slugVal = document.getElementById('postSlug')?.value || '';
        const contentVal = (typeof $ !== 'undefined' && $('#summernoteEditor').length) ? $('#summernoteEditor').summernote('code') : (document.getElementById('summernoteEditor')?.value || '');
        const seoTitleVal = document.getElementById('seoTitleInput')?.value || '';
        const seoDescVal = document.getElementById('seoDescInput')?.value || '';
        const keywordVal = document.getElementById('seoPrimaryKeyword')?.value || '';
        const intentVal = document.getElementById('seoSearchIntent')?.value || 'informational';
        const canonicalVal = document.getElementById('seoCanonicalUrl')?.value || '';
        const robotsIndexVal = document.getElementById('seoRobotsIndex')?.checked ? 1 : 0;
        const robotsFollowVal = document.getElementById('seoRobotsFollow')?.checked ? 1 : 0;
        const sitemapVal = document.getElementById('seoIncludeSitemap')?.checked ? 1 : 0;
        const schemaVal = document.querySelector('select[name="schema_type"]')?.value || 'BlogPosting';

        // Update local SERP text preview immediately
        updateSerpPreviewText(seoTitleVal || titleVal, seoDescVal, slugVal);

        // Send async analysis request
        fetch("{{ route('admin.seo.analyze') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                title: titleVal,
                slug: slugVal,
                content: contentVal,
                seo_title: seoTitleVal,
                meta_description: seoDescVal,
                primary_keyword: keywordVal,
                search_intent: intentVal,
                canonical_url: canonicalVal,
                robots_index: robotsIndexVal,
                robots_follow: robotsFollowVal,
                include_in_sitemap: sitemapVal,
                schema_type: schemaVal,
                post_id: "{{ isset($post) ? $post->id : '' }}",
                category_id: document.querySelector('select[name="category_id"]')?.value || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderSeoAuditResults(data.audit, data.link_suggestions, data.link_grader, data.letter_grade);
            }
        })
        .catch(err => {
            console.error('SEO Analysis error:', err);
        });
    }

    function renderSeoAuditResults(audit, linkSuggestions, linkGrader, letterGrade) {
        // 1. Score & Status Badge
        const scoreVal = document.getElementById('seoScoreValue');
        const scoreBadge = document.getElementById('seoScoreBadge');
        const statusLabel = document.getElementById('seoStatusLabel');
        const tabAuditBadge = document.getElementById('tabAuditBadge');
        const tabRelLinksBadge = document.getElementById('tabRelLinksBadge');

        if (scoreVal) scoreVal.textContent = audit.total_score;
        if (scoreBadge) {
            scoreBadge.className = `badge fs-6 rounded-pill px-3 py-1 bg-${audit.status_color}`;
            if (letterGrade) {
                scoreBadge.innerHTML = `<span id="seoScoreValue">${audit.total_score}</span>/100 (${letterGrade.grade})`;
            }
        }
        if (statusLabel) statusLabel.textContent = audit.status_label;
        if (tabAuditBadge) tabAuditBadge.textContent = `${(audit.summary?.warnings || 0) + (audit.summary?.critical || 0)} Issues`;
        if (tabRelLinksBadge && linkGrader) tabRelLinksBadge.textContent = linkGrader.total_links;

        // 2. Category Breakdown
        const breakdown = audit.breakdown || {};
        if (document.getElementById('catScoreTechnical')) {
            document.getElementById('catScoreTechnical').textContent = `${breakdown.technical?.score || 0} / 25`;
        }
        if (document.getElementById('catScoreContent')) {
            document.getElementById('catScoreContent').textContent = `${breakdown.content?.score || 0} / 25`;
        }
        if (document.getElementById('catScoreOnPage')) {
            document.getElementById('catScoreOnPage').textContent = `${breakdown.on_page?.score || 0} / 20`;
        }
        if (document.getElementById('catScoreLinks')) {
            document.getElementById('catScoreLinks').textContent = `${breakdown.internal_linking?.score || 0} / 10`;
        }

        // 3. Cannibalization Warning Box
        const canBox = document.getElementById('cannibalizationAlertBox');
        if (canBox) {
            if (audit.keyword_cannibalization && audit.keyword_cannibalization.has_cannibalization) {
                canBox.classList.remove('d-none');
                document.getElementById('cannibalizationMsg').textContent = audit.keyword_cannibalization.message;
            } else {
                canBox.classList.add('d-none');
            }
        }

        // 4. Render Rules List in Tab 5
        const rulesContainer = document.getElementById('auditRulesListContainer');
        if (rulesContainer) {
            let html = '';
            const rules = audit.rules || [];

            if (rules.length === 0) {
                html = '<div class="p-3 text-center text-muted">No rules to display.</div>';
            } else {
                rules.forEach(r => {
                    const sev = r.severity;
                    let icon = 'bi-check-circle-fill text-success';
                    let bg = 'bg-body border';
                    if (sev === 'critical') {
                        icon = 'bi-x-circle-fill text-danger';
                        bg = 'bg-danger-subtle border border-danger-subtle';
                    } else if (sev === 'warning') {
                        icon = 'bi-exclamation-triangle-fill text-warning';
                        bg = 'bg-warning-subtle border border-warning-subtle';
                    }

                    html += `
                        <div class="p-3 rounded-3 ${bg} d-flex align-items-start gap-3">
                            <i class="bi ${icon} fs-5 flex-shrink-0 mt-0.5"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-1 small">${escapeHtml(r.message)}</h6>
                                    ${r.deduction > 0 ? `<span class="badge bg-danger-subtle text-danger">-${r.deduction} pts</span>` : ''}
                                </div>
                                <p class="text-body-secondary mb-0" style="font-size: 0.82rem;">${escapeHtml(r.recommendation)}</p>
                            </div>
                        </div>
                    `;
                });
            }
            rulesContainer.innerHTML = html;
        }

        // 5. Render Link Suggestions in Tab 6
        const linksContainer = document.getElementById('linkSuggestionsContainer');
        if (linksContainer) {
            if (!linkSuggestions || linkSuggestions.length === 0) {
                linksContainer.innerHTML = '<div class="p-4 text-center text-muted border rounded-3 bg-body-tertiary">No specific internal link opportunities detected yet. Try adding more content or related terms.</div>';
            } else {
                let linkHtml = '';
                linkSuggestions.forEach(s => {
                    linkHtml += `
                        <div class="p-3 rounded-3 border bg-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-primary-subtle text-primary">${escapeHtml(s.category)}</span>
                                    <h6 class="fw-bold mb-0">${escapeHtml(s.title)}</h6>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-body-secondary small">
                                    <i class="bi bi-lightbulb text-warning"></i>
                                    <span>${escapeHtml(s.reasons.join(' • '))}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="copyLinkCode('${escapeHtml(s.html_snippet)}')">
                                    <i class="bi bi-clipboard me-1"></i> Copy Link Code
                                </button>
                                <a href="${s.url}" target="_blank" class="btn btn-sm btn-light border rounded-circle" title="Preview Article">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>
                        </div>
                    `;
                });
                linksContainer.innerHTML = linkHtml;
            }
        }

        // 6. Render Links in Tab 7
        if (linkGrader) {
            if (document.getElementById('editorInternalDofollow')) {
                document.getElementById('editorInternalDofollow').textContent = linkGrader.internal?.dofollow || 0;
            }
            if (document.getElementById('editorInternalNofollow')) {
                document.getElementById('editorInternalNofollow').textContent = linkGrader.internal?.nofollow || 0;
            }
            if (document.getElementById('editorExternalDofollow')) {
                document.getElementById('editorExternalDofollow').textContent = linkGrader.external?.dofollow || 0;
            }
            if (document.getElementById('editorExternalNofollow')) {
                const extRel = (linkGrader.external?.nofollow || 0) + (linkGrader.external?.sponsored || 0) + (linkGrader.external?.ugc || 0);
                document.getElementById('editorExternalNofollow').textContent = extRel;
            }

            const editorLinksList = document.getElementById('editorLinksListContainer');
            if (editorLinksList) {
                const links = linkGrader.links || [];
                if (links.length === 0) {
                    editorLinksList.innerHTML = '<div class="text-center py-4 text-muted border rounded-3 bg-body-tertiary">No hyperlinks detected yet in the content editor.</div>';
                } else {
                    let html = '';
                    links.forEach(l => {
                        let relBadge = '<span class="badge badge-follow">dofollow</span>';
                        if (l.is_sponsored) relBadge = '<span class="badge badge-sponsored">rel="sponsored"</span>';
                        else if (l.is_ugc) relBadge = '<span class="badge badge-ugc">rel="ugc"</span>';
                        else if (l.is_nofollow) relBadge = '<span class="badge badge-nofollow">rel="nofollow"</span>';

                        const typeBadge = l.type === 'internal' 
                            ? '<span class="badge bg-primary-subtle text-primary">Internal</span>'
                            : '<span class="badge bg-info-subtle text-info">External</span>';

                        let issueHtml = '';
                        if (l.issues && l.issues.length > 0) {
                            l.issues.forEach(iss => {
                                issueHtml += `<small class="text-warning d-block" style="font-size:0.75rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>${escapeHtml(iss.message)}</small>`;
                            });
                        }

                        html += `
                            <div class="p-2.5 rounded-3 border bg-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-0.5">
                                        ${typeBadge}
                                        ${relBadge}
                                        <strong class="small">${escapeHtml(l.anchor)}</strong>
                                    </div>
                                    <code class="small text-muted">${escapeHtml(l.href)}</code>
                                    ${issueHtml}
                                </div>
                                <div>
                                    <a href="${escapeHtml(l.href)}" target="_blank" class="btn btn-sm btn-light border rounded-circle"><i class="bi bi-box-arrow-up-right"></i></a>
                                </div>
                            </div>
                        `;
                    });
                    editorLinksList.innerHTML = html;
                }
            }
        }
    }

    function updateSerpPreviewText(title, desc, slug) {
        const titleDisp = document.getElementById('serpTitleDisplay');
        const descDisp = document.getElementById('serpDescDisplay');
        const urlDisp = document.getElementById('serpUrlDisplay');

        if (titleDisp) titleDisp.textContent = title || 'Article Headline Will Appear Here';
        if (descDisp) descDisp.textContent = desc || 'A captivating summary of your article will appear here in search engine results.';
        if (urlDisp) urlDisp.textContent = `{{ url('/blog') }}/${slug || 'article-slug'}`;

        // Update Char Counters
        const titleLen = (title || '').length;
        const descLen = (desc || '').length;

        const titleCounter = document.getElementById('titleCharCounter');
        const descCounter = document.getElementById('descCharCounter');
        const titleProgress = document.getElementById('titleProgressBar');
        const descProgress = document.getElementById('descProgressBar');

        if (titleCounter) titleCounter.textContent = `${titleLen} / 60 chars`;
        if (descCounter) descCounter.textContent = `${descLen} / 160 chars`;

        if (titleProgress) {
            const pct = Math.min(100, Math.round((titleLen / 60) * 100));
            titleProgress.style.width = `${pct}%`;
            titleProgress.className = `progress-bar bg-${titleLen > 70 ? 'danger' : (titleLen >= 30 ? 'success' : 'warning')}`;
        }
        if (descProgress) {
            const pct = Math.min(100, Math.round((descLen / 160) * 100));
            descProgress.style.width = `${pct}%`;
            descProgress.className = `progress-bar bg-${descLen > 165 ? 'danger' : (descLen >= 50 ? 'success' : 'warning')}`;
        }
    }

    function setSerpView(view) {
        const container = document.getElementById('serpPreviewContainer');
        const btnD = document.getElementById('btnSerpDesktop');
        const btnM = document.getElementById('btnSerpMobile');

        if (view === 'mobile') {
            container.style.maxWidth = '375px';
            container.classList.add('border-primary');
            btnM.classList.add('active');
            btnD.classList.remove('active');
        } else {
            container.style.maxWidth = '600px';
            container.classList.remove('border-primary');
            btnD.classList.add('active');
            btnM.classList.remove('active');
        }
    }

    function copyLinkCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            alert('HTML link code copied to clipboard! You can paste it into the article editor.');
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Attach debounced listeners on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        const watchedInputs = [
            'postTitle',
            'postSlug',
            'seoTitleInput',
            'seoDescInput',
            'seoPrimaryKeyword',
            'seoSearchIntent',
            'seoCanonicalUrl'
        ];

        watchedInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', () => {
                    clearTimeout(seoAnalysisDebounceTimer);
                    seoAnalysisDebounceTimer = setTimeout(triggerLiveSeoAnalysis, 400);
                });
                el.addEventListener('change', triggerLiveSeoAnalysis);
            }
        });

        // Hook Summernote change event if available
        if (typeof $ !== 'undefined' && $('#summernoteEditor').length) {
            $('#summernoteEditor').on('summernote.change', function() {
                clearTimeout(seoAnalysisDebounceTimer);
                seoAnalysisDebounceTimer = setTimeout(triggerLiveSeoAnalysis, 600);
            });
        }

        // Initial trigger
        setTimeout(triggerLiveSeoAnalysis, 500);
    });
</script>
@endpush
@php
    $curPost = isset($post) ? $post : null;
    $curSeo = isset($postSeo) ? $postSeo : ($curPost && $curPost->seo ? $curPost->seo : null);
    $metaTitleVal = old('meta_title', $curPost->meta_title ?? ($curSeo->seo_title ?? ''));
    $metaDescVal = old('meta_description', $curPost->meta_description ?? ($curSeo->meta_description ?? ''));
    $canonicalVal = old('canonical_url', $curSeo->canonical_url ?? ($curPost->canonical_url ?? ''));
    $slugVal = old('slug', $curPost->slug ?? 'article-slug');
@endphp

<!-- Primary Meta & Canonical SEO Settings Card -->
<div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4" id="primarySeoCard" style="border-left: 4px solid #00ff66 !important;">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <div class="p-2 bg-success bg-opacity-10 text-success rounded-3">
                <i class="bi bi-search fs-5"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0">Search Engine Metadata (Meta Title, Description &amp; Canonical)</h5>
                <small class="text-body-secondary">Control how Google SERP and search crawlers index and display this article</small>
            </div>
        </div>
        <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1">Google SERP Ready</span>
    </div>

    <!-- Meta Title -->
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label fw-bold small mb-0">
                Meta Title <span class="text-muted fw-normal">(Google SERP Headline)</span>
            </label>
            <span class="badge bg-body-tertiary text-body border font-monospace" id="metaTitleCharCounter">0 / 60</span>
        </div>
        <input type="text" name="meta_title" id="postMetaTitle" class="form-control rounded-3" placeholder="e.g. Building High-Scale Web Applications in Laravel 12 | Shrawan Effects" value="{{ $metaTitleVal }}">
        <div class="progress mt-1.5" style="height: 4px;">
            <div id="metaTitleProgressBar" class="progress-bar bg-success" style="width: 0%;"></div>
        </div>
        <small class="text-body-secondary d-block mt-1">
            Recommended: 50–60 characters. If left empty, automatically falls back to Article Title.
        </small>
    </div>

    <!-- Meta Description -->
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label fw-bold small mb-0">
                Meta Description <span class="text-muted fw-normal">(Google Snippet Summary)</span>
            </label>
            <span class="badge bg-body-tertiary text-body border font-monospace" id="metaDescCharCounter">0 / 160</span>
        </div>
        <textarea name="meta_description" id="postMetaDescription" class="form-control rounded-3" rows="3" placeholder="A captivating 1-2 sentence overview for Google search snippets (120–160 characters)...">{{ $metaDescVal }}</textarea>
        <div class="progress mt-1.5" style="height: 4px;">
            <div id="metaDescProgressBar" class="progress-bar bg-success" style="width: 0%;"></div>
        </div>
        <small class="text-body-secondary d-block mt-1">
            Recommended: 120–160 characters. If left empty, automatically falls back to the Excerpt.
        </small>
    </div>

    <!-- Canonical Tag / URL -->
    <div class="mb-3">
        <label class="form-label fw-bold small mb-1">
            Canonical URL <span class="text-muted fw-normal">(&lt;link rel="canonical"&gt; Tag)</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary"><i class="bi bi-link-45deg text-success fs-5"></i></span>
            <input type="url" name="canonical_url" id="postCanonicalUrl" class="form-control font-monospace" placeholder="https://shrawaneffects.com/blog/article-slug" value="{{ $canonicalVal }}">
        </div>
        <small class="text-body-secondary d-block mt-1">
            Specifies the primary canonical version (<code>&lt;link rel="canonical" href="..."&gt;</code>). Leave blank to default to this article's own permanent URL (<code>{{ url('/blog') }}/<span id="canonicalSlugPreview">{{ $slugVal }}</span></code>).
        </small>
    </div>

    <!-- Live Google SERP Mockup Box -->
    <div class="p-3 rounded-4 bg-body-tertiary border mt-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-google text-primary"></i>
                <span class="fw-semibold small">Google SERP Live Snippet Preview</span>
            </div>
            <span class="badge bg-body text-secondary border font-monospace" style="font-size: 0.7rem;">Search Preview</span>
        </div>
        <div class="p-3 bg-body rounded-3 border" style="font-family: Arial, sans-serif; max-width: 650px;">
            <div class="text-truncate mb-1" style="font-size: 0.82rem; color: #202124;">
                <span class="text-secondary">{{ url('/blog') }}/</span><span id="serpLiveSlug" class="fw-semibold">{{ $slugVal }}</span>
            </div>
            <h6 class="fw-normal mb-1 text-truncate" id="serpLiveTitle" style="color: #1a0dab !important; font-size: 1.15rem; line-height: 1.3; cursor: pointer;">
                {{ $metaTitleVal ?: ($curPost->title ?? 'Article Headline Will Appear Here') }}
            </h6>
            <p class="mb-0 text-truncate" id="serpLiveDesc" style="color: #4d5156; font-size: 0.88rem; line-height: 1.45;">
                {{ $metaDescVal ?: ($curPost->excerpt ?? 'Your meta description will appear here as the Google snippet preview.') }}
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const postTitle = document.getElementById('postTitle');
    const postSlug = document.getElementById('postSlug');
    const excerptEl = document.querySelector('textarea[name="excerpt"]');
    
    // Primary Card inputs
    const metaTitle = document.getElementById('postMetaTitle');
    const metaDesc = document.getElementById('postMetaDescription');
    const canonicalUrl = document.getElementById('postCanonicalUrl');

    // Advanced Panel inputs
    const seoTitle = document.getElementById('seoTitleInput');
    const seoDesc = document.getElementById('seoDescInput');
    const seoCanonical = document.getElementById('seoCanonicalUrl');

    // SERP Previews
    const serpLiveTitle = document.getElementById('serpLiveTitle');
    const serpLiveDesc = document.getElementById('serpLiveDesc');
    const serpLiveSlug = document.getElementById('serpLiveSlug');
    const canonicalSlugPreview = document.getElementById('canonicalSlugPreview');
    const metaTitleCharCounter = document.getElementById('metaTitleCharCounter');
    const metaTitleProgressBar = document.getElementById('metaTitleProgressBar');
    const metaDescCharCounter = document.getElementById('metaDescCharCounter');
    const metaDescProgressBar = document.getElementById('metaDescProgressBar');

    let metaTitleManuallyEdited = {{ !empty($metaTitleVal) ? 'true' : 'false' }};
    let metaDescManuallyEdited = {{ !empty($metaDescVal) ? 'true' : 'false' }};

    function syncSeoState() {
        const titleVal = (metaTitle && metaTitle.value.trim()) || (postTitle ? postTitle.value.trim() : '');
        const descVal = (metaDesc && metaDesc.value.trim()) || (excerptEl ? excerptEl.value.trim() : '');
        const slugVal = (postSlug && postSlug.value.trim()) || 'article-slug';
        const canonVal = (canonicalUrl && canonicalUrl.value.trim()) || '';

        // Character counts & bars
        if (metaTitleCharCounter && metaTitle) {
            const tLen = metaTitle.value.length;
            metaTitleCharCounter.textContent = `${tLen} / 60`;
            const tPct = Math.min(100, Math.round((tLen / 60) * 100));
            if (metaTitleProgressBar) {
                metaTitleProgressBar.style.width = `${tPct}%`;
                metaTitleProgressBar.className = (tLen >= 45 && tLen <= 65)
                    ? 'progress-bar bg-success'
                    : (tLen > 65 ? 'progress-bar bg-danger' : 'progress-bar bg-warning');
            }
        }

        if (metaDescCharCounter && metaDesc) {
            const dLen = metaDesc.value.length;
            metaDescCharCounter.textContent = `${dLen} / 160`;
            const dPct = Math.min(100, Math.round((dLen / 160) * 100));
            if (metaDescProgressBar) {
                metaDescProgressBar.style.width = `${dPct}%`;
                metaDescProgressBar.className = (dLen >= 115 && dLen <= 165)
                    ? 'progress-bar bg-success'
                    : (dLen > 165 ? 'progress-bar bg-danger' : 'progress-bar bg-warning');
            }
        }

        // Update live previews
        if (serpLiveTitle) {
            serpLiveTitle.textContent = titleVal || 'Article Headline Will Appear Here';
        }
        if (serpLiveDesc) {
            serpLiveDesc.textContent = descVal || 'Your meta description will appear here as the Google snippet preview.';
        }
        if (serpLiveSlug) {
            serpLiveSlug.textContent = slugVal;
        }
        if (canonicalSlugPreview) {
            canonicalSlugPreview.textContent = slugVal;
        }

        // Mirror to advanced panel preview if present
        const advTitle = document.getElementById('serpTitleDisplay');
        const advDesc = document.getElementById('serpDescDisplay');
        if (advTitle) advTitle.textContent = titleVal || 'Article Headline Will Appear Here';
        if (advDesc) advDesc.textContent = descVal || 'A captivating meta description that summarizes the article value proposition for search engines.';
    }

    // Two-way synchronization between Primary Card and Advanced Panel
    if (metaTitle) {
        metaTitle.addEventListener('input', function() {
            metaTitleManuallyEdited = true;
            if (seoTitle) seoTitle.value = metaTitle.value;
            syncSeoState();
        });
    }

    if (seoTitle) {
        seoTitle.addEventListener('input', function() {
            metaTitleManuallyEdited = true;
            if (metaTitle) metaTitle.value = seoTitle.value;
            syncSeoState();
        });
    }

    if (metaDesc) {
        metaDesc.addEventListener('input', function() {
            metaDescManuallyEdited = true;
            if (seoDesc) seoDesc.value = metaDesc.value;
            syncSeoState();
        });
    }

    if (seoDesc) {
        seoDesc.addEventListener('input', function() {
            metaDescManuallyEdited = true;
            if (metaDesc) metaDesc.value = seoDesc.value;
            syncSeoState();
        });
    }

    if (canonicalUrl) {
        canonicalUrl.addEventListener('input', function() {
            if (seoCanonical) seoCanonical.value = canonicalUrl.value;
            syncSeoState();
        });
    }

    if (seoCanonical) {
        seoCanonical.addEventListener('input', function() {
            if (canonicalUrl) canonicalUrl.value = seoCanonical.value;
            syncSeoState();
        });
    }

    if (postTitle) {
        postTitle.addEventListener('input', function() {
            if (!metaTitleManuallyEdited && metaTitle && !metaTitle.value) {
                // Keep preview synchronized with postTitle
            }
            syncSeoState();
        });
    }

    if (excerptEl) {
        excerptEl.addEventListener('input', function() {
            syncSeoState();
        });
    }

    if (postSlug) {
        postSlug.addEventListener('input', function() {
            syncSeoState();
        });
    }

    syncSeoState();
});
</script>

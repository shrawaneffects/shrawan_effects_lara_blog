{{--
    =============================================================================
    Tool In-Depth Guide, Table of Contents, FAQs & Related Widgets Partial
    Matches the user's reference layout (media_1795761742146_0e7a51b6.png):
    - Large Title (h1)
    - 2-Column: Col-8 (TOC, Processed Content, FAQs) + Col-4 (Search, Author, Related Tools, Related Posts)
    =============================================================================
--}}

@php
    $toolTitle = $tool->title ?? ($pageTitle ?? 'Online Tool Guide & Documentation');
    $enableToc = $tool->enable_toc ?? true;
    $tocItems = ($tool && method_exists($tool, 'getTableOfContentsAttribute')) ? $tool->table_of_contents : [];
    $processedContent = $tool->processed_content ?? ($tool->content ?? '');
    $faqs = ($tool && is_array($tool->faqs)) ? $tool->faqs : [];

    // Fallback author
    $authorName = 'Shrawan Choudhary';
    $authorAvatar = asset('storage/avatars/admin.png');
    $authorBio = 'Fullstack software engineer and tech analyst authoring deep-dive guides on web architecture, Laravel, and AI systems at Shrawan Effects.';
@endphp

@if(!empty($processedContent) || count($faqs) > 0 || !empty($toolTitle))
<section class="tool-guide-section pt-5 pb-5 mt-4 border-top border-secondary border-opacity-25" id="toolGuideSection" style="font-family: var(--font-main);">
    <div class="container-xl px-3 px-md-4">
        <!-- Tool Article Title -->
        <div class="mb-4">
            <h1 class="display-5 fw-bold text-body mb-3" style="letter-spacing: -0.02em; line-height: 1.2;">
                {{ $toolTitle }}
            </h1>
            @if(!empty($tool->summary))
                <p class="lead text-body-secondary mb-0" style="font-size: 1.15rem; line-height: 1.6;">
                    {{ $tool->summary }}
                </p>
            @endif
        </div>

        <div class="row g-4 g-xl-5">
            <!-- Left Column: Table of Contents, Rich Article Content & FAQs -->
            <div class="col-lg-8 col-xl-8">
                <!-- Table of Contents (TOC) Component -->
                @if($enableToc && count($tocItems) > 0)
                    <div class="toc-container card border-0 shadow-sm rounded-4 mb-5 p-4 bg-body hover-lift" style="border-left: 4px solid var(--neon-green, #00ff66) !important; background: rgba(9, 18, 6, 0.75);">
                        <div class="d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#toolTocCollapse" aria-expanded="true" style="cursor: pointer;">
                            <h5 class="fw-bold mb-0 d-flex align-items-center">
                                <i class="bi bi-list-nested text-success me-2 fs-4"></i>
                                <span>Table of <span class="text-success">Contents</span></span>
                            </h5>
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" type="button" style="font-size: 0.9rem;">
                                <i class="bi bi-chevron-down me-1"></i> <span>Toggle</span>
                            </button>
                        </div>
                        <div class="collapse show mt-3" id="toolTocCollapse">
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2 border-top border-secondary border-opacity-25 pt-3">
                                @foreach($tocItems as $item)
                                    <li class="{{ $item['level'] === 3 ? 'ps-4' : '' }}">
                                        <a href="#{{ $item['id'] }}" class="tool-toc-link text-decoration-none text-body d-inline-flex align-items-center" style="font-size: 0.95rem; line-height: 1.5;">
                                            <i class="bi bi-chevron-right me-2 text-primary" style="font-size: 0.85rem;"></i>
                                            <span>{{ $item['title'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Article Body Content -->
                @if(!empty($processedContent))
                    <div class="post-content tool-guide-content mb-5" style="font-size: 1.05rem; line-height: 1.75;">
                        {!! $processedContent !!}
                    </div>
                @endif

                <!-- Frequently Asked Questions (FAQs) Accordion -->
                @if(count($faqs) > 0)
                    <div class="mb-5 pt-4 border-top border-secondary border-opacity-25" id="toolFaqs">
                        <div class="d-flex align-items-center mb-4">
                            <h3 class="fw-bold mb-0">
                                <i class="bi bi-question-circle text-success me-2 fs-3"></i>
                                <span>Frequently Asked <span class="text-success">Questions</span></span>
                            </h3>
                        </div>

                        <div class="accordion accordion-flush d-flex flex-column gap-3" id="toolFaqAccordion">
                            @foreach($faqs as $index => $faq)
                                @if(!empty($faq['question']) && !empty($faq['answer']))
                                    <div class="accordion-item border rounded-4 bg-body shadow-sm overflow-hidden hover-lift" style="border-color: rgba(0, 255, 102, 0.15) !important;">
                                        <h2 class="accordion-header" id="toolFaqHeading{{ $index }}">
                                            <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }} fw-bold text-body bg-transparent p-4" type="button" data-bs-toggle="collapse" data-bs-target="#toolFaqCollapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="toolFaqCollapse{{ $index }}">
                                                <span class="badge bg-primary-subtle text-primary rounded-circle me-3 px-2 py-1" style="font-size: 0.9rem;">Q{{ $index + 1 }}</span>
                                                <span class="text-body">{{ $faq['question'] }}</span>
                                            </button>
                                        </h2>
                                        <div id="toolFaqCollapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="toolFaqHeading{{ $index }}" data-bs-parent="#toolFaqAccordion">
                                            <div class="accordion-body px-4 pb-4 pt-0 text-body-secondary" style="font-size: 0.98rem; line-height: 1.65;">
                                                {!! nl2br(e($faq['answer'])) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column Sidebar: Search, Author Profile, Related Tools, Related Posts -->
            <aside class="col-lg-4 col-xl-4 tool-sidebar">
                <div class="sticky-sidebar d-flex flex-column gap-4" style="font-family: var(--font-main);">
                    <!-- 1. SEARCH ARTICLES WIDGET -->
                    <div class="card border-0 shadow-sm p-3 p-xl-4 rounded-4 bg-body sidebar-widget hover-lift" style="border: 1px solid rgba(0, 255, 102, 0.15) !important;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                            <span class="text-success fw-bold" style="font-size: 0.95rem; letter-spacing: 0.05em;">
                                <i class="bi bi-search text-success me-1"></i> SEARCH ARTICLES
                            </span>
                        </div>
                        <form action="{{ route('blog.index') }}" method="GET" role="search">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control rounded-start-pill ps-3" placeholder="Search articles..." aria-label="Search articles" style="font-size: 0.95rem;">
                                <button class="btn btn-gradient rounded-end-pill px-3" type="submit" aria-label="Search">
                                    <i class="bi bi-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- 2. AUTHOR PROFILE WIDGET -->
                    <div class="card border-0 shadow-sm p-3 p-xl-4 rounded-4 bg-body sidebar-widget hover-lift" style="border: 1px solid rgba(0, 255, 102, 0.15) !important;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                            <span class="text-success fw-bold" style="font-size: 0.95rem; letter-spacing: 0.05em;">
                                <i class="bi bi-person-badge-fill me-1"></i> AUTHOR PROFILE
                            </span>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1" style="font-size: 0.85rem;">
                                Architect
                            </span>
                        </div>

                        <div class="text-center mb-3">
                            <a href="{{ url('/') }}" class="d-inline-block position-relative">
                                <img src="{{ $authorAvatar }}" alt="{{ $authorName }}" class="rounded-circle shadow-sm" width="84" height="84" style="object-fit: cover; border: 2px solid var(--neon-green, #00ff66); padding: 2px;">
                            </a>
                            <h5 class="fw-bold mt-2 mb-1">
                                <a href="{{ url('/') }}" class="text-decoration-none text-body hover-gradient">
                                    {{ $authorName }}
                                </a>
                            </h5>
                            <span class="text-body-secondary d-block small">
                                Fullstack Web Developer & AI Systems
                            </span>
                        </div>

                        <p class="text-body-secondary text-center mb-3 small" style="line-height: 1.55;">
                            {{ $authorBio }}
                        </p>

                        <div class="text-center mb-3">
                            <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2 hover-lift" style="font-size: 0.9rem;">
                                <i class="bi bi-globe2"></i>
                                <span>shrawaneffects.com</span>
                                <i class="bi bi-box-arrow-up-right small"></i>
                            </a>
                        </div>

                        @php
                            $authorTwitter = \App\Models\Setting::get('twitter_url');
                            $authorLinkedin = \App\Models\Setting::get('linkedin_url');
                            $authorGithub = \App\Models\Setting::get('github_url');
                            $authorYoutube = \App\Models\Setting::get('youtube_url');
                        @endphp
                        <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 pt-3 border-top border-secondary border-opacity-10">
                            @if(!empty($authorTwitter))
                                <a href="{{ $authorTwitter }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle hover-lift" title="X (Twitter)" style="width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-twitter-x"></i></a>
                            @endif
                            @if(!empty($authorLinkedin))
                                <a href="{{ $authorLinkedin }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle hover-lift" title="LinkedIn" style="width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-linkedin"></i></a>
                            @endif
                            @if(!empty($authorGithub))
                                <a href="{{ $authorGithub }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle hover-lift" title="GitHub" style="width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-github"></i></a>
                            @endif
                            @if(!empty($authorYoutube))
                                <a href="{{ $authorYoutube }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle hover-lift" title="YouTube" style="width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-youtube"></i></a>
                            @endif
                        </div>
                    </div>

                    <!-- 3. RELATED TOOLS WIDGET -->
                    @if(isset($relatedTools) && $relatedTools->count() > 0)
                        <div class="card border-0 shadow-sm p-3 p-xl-4 rounded-4 bg-body sidebar-widget hover-lift" style="border: 1px solid rgba(0, 255, 102, 0.15) !important;">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                                <span class="text-success fw-bold" style="font-size: 0.95rem; letter-spacing: 0.05em;">
                                    <i class="bi bi-tools text-success me-1"></i> RELATED TOOLS
                                </span>
                                <span class="badge bg-body-tertiary text-body-secondary rounded-pill small">Utilities</span>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                @foreach($relatedTools as $relTool)
                                    <div class="d-flex gap-3 align-items-center {{ !$loop->last ? 'pb-3 border-bottom border-secondary border-opacity-10' : '' }}">
                                        <div class="bg-primary-subtle text-primary rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                            <i class="bi {{ $relTool->icon ?? 'bi-tools' }} fs-5"></i>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <span class="badge bg-secondary-subtle text-body-secondary rounded-pill py-0 px-2" style="font-size: 0.75rem;">{{ $relTool->category ?? 'Tool' }}</span>
                                                @if(!empty($relTool->badge))
                                                    <span class="badge bg-danger text-white rounded-pill py-0 px-1" style="font-size: 0.7rem;">{{ $relTool->badge }}</span>
                                                @endif
                                            </div>
                                            <h6 class="mb-0 fw-semibold text-truncate" style="font-size: 0.92rem; line-height: 1.4;">
                                                <a href="{{ $relTool->live_url }}" class="text-decoration-none text-body hover-gradient">
                                                    {{ $relTool->title }}
                                                </a>
                                            </h6>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- 4. RELATED ARTICLES WIDGET -->
                    @if(isset($relatedPosts) && $relatedPosts->count() > 0)
                        <div class="card border-0 shadow-sm p-3 p-xl-4 rounded-4 bg-body sidebar-widget hover-lift" style="border: 1px solid rgba(0, 255, 102, 0.15) !important;">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                                <span class="text-success fw-bold" style="font-size: 0.95rem; letter-spacing: 0.05em;">
                                    <i class="bi bi-clock-history text-success me-1"></i> RELATED ARTICLES
                                </span>
                                <span class="badge bg-body-tertiary text-body-secondary rounded-pill small">Articles</span>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                @foreach($relatedPosts as $rPost)
                                    <div class="d-flex gap-3 align-items-start {{ !$loop->last ? 'pb-3 border-bottom border-secondary border-opacity-10' : '' }}">
                                        <a href="{{ route('blog.show', $rPost->slug) }}" class="flex-shrink-0 position-relative rounded-3 overflow-hidden shadow-sm" style="width: 60px; height: 60px;">
                                            <img src="{{ $rPost->image_url }}" alt="{{ $rPost->title }}" class="w-100 h-100 object-fit-cover hover-scale" loading="lazy">
                                        </a>
                                        <div class="flex-grow-1 overflow-hidden">
                                            @if($rPost->category)
                                                <a href="{{ route('blog.category', $rPost->category->slug) }}" class="badge rounded-pill text-decoration-none mb-1 text-truncate d-inline-block py-0 px-2" style="font-size: 0.75rem; background-color: {{ $rPost->category->color }}; color: #fff; max-width: 100%;">
                                                    {{ $rPost->category->name }}
                                                </a>
                                            @endif
                                            <h6 class="mb-1 fw-semibold line-clamp-2" style="font-size: 0.9rem; line-height: 1.4;">
                                                <a href="{{ route('blog.show', $rPost->slug) }}" class="text-decoration-none text-body hover-gradient">
                                                    {{ $rPost->title }}
                                                </a>
                                            </h6>
                                            <div class="d-flex align-items-center gap-2 text-body-secondary small">
                                                <span>{{ $rPost->published_at ? $rPost->published_at->format('M d') : 'Recent' }}</span>
                                                <span>&bull;</span>
                                                <span>{{ $rPost->reading_time ?? 5 }}m read</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Smooth scrolling for Tool TOC links
    document.querySelectorAll('.tool-toc-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElem = document.getElementById(targetId);
            if (targetElem) {
                const yOffset = -90; // offset for sticky navbar
                const y = targetElem.getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({ top: y, behavior: 'smooth' });
                history.pushState(null, null, '#' + targetId);
            }
        });
    });

    // Scroll spy active TOC item highlighting
    window.addEventListener('scroll', function() {
        const headings = document.querySelectorAll('.tool-guide-content h2, .tool-guide-content h3');
        let activeId = null;
        headings.forEach(h => {
            const rect = h.getBoundingClientRect();
            if (rect.top <= 140) {
                activeId = h.id;
            }
        });
        if (activeId) {
            document.querySelectorAll('.tool-toc-link').forEach(link => {
                if (link.getAttribute('href') === '#' + activeId) {
                    link.classList.add('text-success', 'fw-bold');
                    link.classList.remove('text-body');
                } else {
                    link.classList.remove('text-success', 'fw-bold');
                    link.classList.add('text-body');
                }
            });
        }
    });
</script>
@endpush
@endif

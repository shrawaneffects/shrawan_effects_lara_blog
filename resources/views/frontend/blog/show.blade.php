@extends('layouts.app')

@php
    $seo = $post->seo_data;
    $ogImg = $seo->og_image ? (str_starts_with($seo->og_image, 'http') ? $seo->og_image : asset('storage/' . $seo->og_image)) : $post->image_url;
    $twImg = $seo->twitter_image ? (str_starts_with($seo->twitter_image, 'http') ? $seo->twitter_image : asset('storage/' . $seo->twitter_image)) : $ogImg;
@endphp

@section('title', $seo->effective_title)
@section('meta_description', $seo->effective_description)
@section('meta_keywords', $seo->primary_keyword ? $seo->primary_keyword . ', ' . ($post->meta_keywords ?? '') : ($post->meta_keywords ?? ''))
@section('canonical_url', $seo->effective_canonical)
@section('robots_meta', $seo->robots_directive)

@section('og_title', $seo->og_title ?: $seo->effective_title)
@section('og_description', $seo->og_description ?: $seo->effective_description)
@section('og_image', $ogImg)
@section('og_type', 'article')

@section('twitter_card', $seo->twitter_card ?: 'summary_large_image')
@section('twitter_title', $seo->twitter_title ?: ($seo->og_title ?: $seo->effective_title))
@section('twitter_description', $seo->twitter_description ?: ($seo->og_description ?: $seo->effective_description))
@section('twitter_image', $twImg)

@push('schema')
{!! \App\Services\Seo\SeoSchemaService::generate($post) !!}
@endpush

@section('content')
    <article class="py-4">
        <!-- Post Header -->
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}" class="text-decoration-none">Articles</a></li>
                            @if($post->category)
                                <li class="breadcrumb-item"><a href="{{ route('blog.category', $post->category->slug) }}" class="text-decoration-none">{{ $post->category->name }}</a></li>
                            @endif
                        </ol>
                    </nav>

                    <div class="mb-3 d-flex align-items-center gap-2">
                        @if($post->category)
                            <a href="{{ route('blog.category', $post->category->slug) }}" class="badge badge-category text-white text-decoration-none" style="background-color: {{ $post->category->color }};">
                                {{ $post->category->name }}
                            </a>
                        @endif
                        @if($post->status === 'draft')
                            <span class="badge bg-secondary ms-1">Draft Preview</span>
                        @endif
                        @if($post->is_featured)
                            <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> Featured</span>
                        @endif
                    </div>

                    <h1 class="display-5 fw-bold mb-4">{{ $post->title }}</h1>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-4 mb-4 border-bottom">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('blog.author', $post->author->id) }}">
                                <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="author-avatar me-3">
                            </a>
                            <div>
                                <h6 class="mb-0 fw-bold">
                                    <a href="{{ route('blog.author', $post->author->id) }}" class="text-decoration-none text-body">
                                        {{ $post->author->name }}
                                    </a>
                                </h6>
                                <small class="text-body-secondary font-mono" style="font-size: 0.78rem;">
                                    {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Draft' }} &bull;
                                    <i class="bi bi-clock me-1 text-success"></i>{{ $post->reading_time }}m read &bull;
                                    <i class="bi bi-eye me-1 text-success"></i>{{ number_format($post->views_count) }} views
                                </small>
                            </div>
                        </div>

                        <!-- Social Share Buttons with Gradient Hover -->
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted d-none d-sm-inline fw-semibold">Share:</span>
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle p-2 hover-lift" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;" title="Share on X">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle p-2 hover-lift" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;" title="Share on Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->fullUrl()) }}&title={{ urlencode($post->title) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle p-2 hover-lift" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;" title="Share on LinkedIn">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <button class="btn btn-outline-secondary btn-sm rounded-circle p-2 hover-lift" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;" onclick="navigator.clipboard.writeText(window.location.href); alert('Article link copied to clipboard!');" title="Copy Link">
                                <i class="bi bi-link-45deg fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Top Article Ad Slot -->
                    @include('components.ad-slot', ['slotName' => 'post_top', 'label' => 'Article Top Leaderboard (Responsive)', 'minHeight' => '90px', 'class' => 'mb-4'])

                    <!-- Featured Image -->
                    <div class="mb-5 text-center post-thumb-container">
                        <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="img-fluid rounded-4 shadow-lg w-100 object-fit-cover hover-lift" style="max-height: 480px;">
                    </div>

                    <!-- Table of Contents (TOC) Component -->
                    @if($post->enable_toc && count($post->table_of_contents) > 0)
                        <div class="toc-container card border-0 shadow-sm rounded-4 mb-5 p-4 bg-body hover-lift" style="border-left: 4px solid #6366f1 !important;">
                            <div class="d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#tableOfContentsCollapse" aria-expanded="true" style="cursor: pointer;">
                                <h5 class="fw-bold mb-0 d-flex align-items-center">
                                    <i class="bi bi-list-nested text-gradient me-2 fs-4"></i>
                                    <span>Table of <span class="text-gradient">Contents</span></span>
                                </h5>
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" type="button">
                                    <i class="bi bi-chevron-down me-1"></i> <span class="small">Toggle</span>
                                </button>
                            </div>
                            <div class="collapse show mt-3" id="tableOfContentsCollapse">
                                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 border-top pt-3">
                                    @foreach($post->table_of_contents as $item)
                                        <li class="{{ $item['level'] === 3 ? 'ps-4' : '' }}">
                                            <a href="#{{ $item['id'] }}" class="toc-link text-decoration-none text-body small d-inline-flex align-items-center">
                                                <i class="bi bi-chevron-right me-2 text-primary" style="font-size: 0.7rem;"></i>
                                                <span>{{ $item['title'] }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Post Body Content -->
                    <div class="post-content mb-5">
                        {!! $post->processed_content !!}
                    </div>

                    <!-- Bottom Article Ad Slot -->
                    @include('components.ad-slot', ['slotName' => 'post_bottom', 'label' => 'Article Bottom Banner (Responsive)', 'minHeight' => '100px', 'class' => 'mb-5'])

                    <!-- Tags -->
                    @if($post->tags->count() > 0)
                        <div class="d-flex flex-wrap align-items-center gap-2 pt-4 border-top mb-5">
                            <span class="fw-bold text-muted me-2"><i class="bi bi-tags me-1 text-primary"></i> Tags:</span>
                            @foreach($post->tags as $tag)
                                <a href="{{ route('blog.tag', $tag->slug) }}" class="tag-pill">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Frequently Asked Questions (FAQs) -->
                    @if(!empty($post->faqs) && is_array($post->faqs) && count($post->faqs) > 0)
                        <div class="mb-5 pt-4 border-top" id="articleFaqs">
                            <div class="d-flex align-items-center mb-4">
                                <h3 class="fw-bold mb-0">
                                    <i class="bi bi-question-circle text-gradient me-2 fs-3"></i>
                                    <span>Frequently Asked <span class="text-gradient">Questions</span></span>
                                </h3>
                            </div>

                            <div class="accordion accordion-flush d-flex flex-column gap-3" id="faqAccordion">
                                @foreach($post->faqs as $index => $faq)
                                    @if(!empty($faq['question']) && !empty($faq['answer']))
                                        <div class="accordion-item border rounded-4 bg-body shadow-sm overflow-hidden hover-lift">
                                            <h2 class="accordion-header" id="faqHeading{{ $index }}">
                                                <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }} fw-bold text-body bg-transparent p-4" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faqCollapse{{ $index }}">
                                                    <span class="badge bg-primary-subtle text-primary rounded-circle me-3 px-2 py-1 small">Q{{ $index + 1 }}</span>
                                                    {{ $faq['question'] }}
                                                </button>
                                            </h2>
                                            <div id="faqCollapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="faqHeading{{ $index }}" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body px-4 pb-4 pt-0 text-body-secondary">
                                                    {{ $faq['answer'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Author Box -->
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift mb-5">
                        <div class="d-flex gap-4 align-items-center">
                            <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="author-avatar-lg">
                            <div>
                                <span class="badge bg-primary-subtle text-primary rounded-pill mb-1">Written by</span>
                                <h4 class="fw-bold mb-1">
                                    <a href="{{ route('blog.author', $post->author->id) }}" class="text-decoration-none text-body">
                                        {{ $post->author->name }}
                                    </a>
                                </h4>
                                <p class="text-body-secondary small mb-0">{{ $post->author->bio ?? 'Tech writer and software architect.' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Next & Previous Navigation -->
                    <div class="row g-3 mb-5">
                        <div class="col-sm-6">
                            @if(isset($previousPost) && $previousPost)
                                <div class="card h-100 border-0 shadow-sm p-3 rounded-4 bg-body hover-lift" style="border: 1px solid rgba(0,255,102,0.18) !important;">
                                    <small class="text-success font-mono"><i class="bi bi-arrow-left me-1"></i> // PREV TRANSMISSION</small>
                                    <h6 class="fw-bold mt-1 mb-0">
                                        <a href="{{ route('blog.show', $previousPost->slug) }}" class="text-decoration-none text-body">
                                            {{ Str::limit($previousPost->title, 45) }}
                                        </a>
                                    </h6>
                                </div>
                            @endif
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            @if(isset($nextPost) && $nextPost)
                                <div class="card h-100 border-0 shadow-sm p-3 rounded-4 bg-body hover-lift" style="border: 1px solid rgba(0,255,102,0.18) !important;">
                                    <small class="text-success font-mono">// NEXT TRANSMISSION <i class="bi bi-arrow-right ms-1"></i></small>
                                    <h6 class="fw-bold mt-1 mb-0">
                                        <a href="{{ route('blog.show', $nextPost->slug) }}" class="text-decoration-none text-body">
                                            {{ Str::limit($nextPost->title, 45) }}
                                        </a>
                                    </h6>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Article Internal Linking & Architecture Network (Excluding Media) -->
                    @php
                        $incomingPosts = $post->incoming_posts;
                        $outgoingPosts = $post->outgoing_posts;
                    @endphp

                    @if($incomingPosts->count() > 0 || $outgoingPosts->count() > 0)
                        <div class="card border-0 shadow-sm rounded-4 bg-body p-4 mb-5 border-start border-4 border-primary">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="fw-bold mb-0 d-flex align-items-center">
                                    <i class="bi bi-diagram-3-fill text-primary me-2"></i>
                                    <span>Knowledge Graph & <span class="text-gradient">Connected Guides</span></span>
                                </h5>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 small">
                                    {{ $incomingPosts->count() }} Inbound &bull; {{ $outgoingPosts->count() }} Outbound
                                </span>
                            </div>

                            <div class="row g-3">
                                <!-- Incoming Links / Referenced by -->
                                @if($incomingPosts->count() > 0)
                                    <div class="{{ $outgoingPosts->count() > 0 ? 'col-md-6' : 'col-12' }}">
                                        <div class="p-3 rounded-3 bg-body-tertiary h-100">
                                            <h6 class="fw-bold small text-muted text-uppercase mb-2">
                                                <i class="bi bi-arrow-down-left-square-fill text-success me-1"></i> Articles Linking Here (Inbound)
                                            </h6>
                                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                                @foreach($incomingPosts as $inPost)
                                                    <li>
                                                        <a href="{{ route('blog.show', $inPost->slug) }}" class="text-decoration-none text-body small fw-semibold d-flex align-items-center justify-content-between hover-lift">
                                                            <span class="text-truncate" style="max-width: 85%;">{{ $inPost->title }}</span>
                                                            <i class="bi bi-arrow-right-short text-primary"></i>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif

                                <!-- Outgoing Links / References in this article -->
                                @if($outgoingPosts->count() > 0)
                                    <div class="{{ $incomingPosts->count() > 0 ? 'col-md-6' : 'col-12' }}">
                                        <div class="p-3 rounded-3 bg-body-tertiary h-100">
                                            <h6 class="fw-bold small text-muted text-uppercase mb-2">
                                                <i class="bi bi-arrow-up-right-square-fill text-info me-1"></i> Referenced Guides (Outbound)
                                            </h6>
                                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                                @foreach($outgoingPosts as $outPost)
                                                    <li>
                                                        <a href="{{ route('blog.show', $outPost->slug) }}" class="text-decoration-none text-body small fw-semibold d-flex align-items-center justify-content-between hover-lift">
                                                            <span class="text-truncate" style="max-width: 85%;">{{ $outPost->title }}</span>
                                                            <i class="bi bi-arrow-right-short text-primary"></i>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Related Posts -->
                    @if($relatedPosts->count() > 0)
                        <div class="mb-5 pt-4 border-top">
                            <h3 class="fw-bold mb-4">
                                <i class="bi bi-collection text-gradient me-2"></i>
                                <span>You Might <span class="text-gradient">Also Like</span></span>
                            </h3>
                            <div class="row g-4">
                                @foreach($relatedPosts as $rPost)
                                    <div class="col-md-4">
                                        <div class="card card-post spotlight-card h-100 border-0 shadow-sm">
                                            <div class="post-thumb-container">
                                                <img src="{{ $rPost->image_url }}" class="card-img-top post-thumb" alt="{{ $rPost->title }}" style="height: 140px; object-fit: cover;">
                                            </div>
                                            <div class="card-body p-3 d-flex flex-column">
                                                <h6 class="card-title fw-bold mb-2">
                                                    <a href="{{ route('blog.show', $rPost->slug) }}" class="text-decoration-none text-body">
                                                        {{ Str::limit($rPost->title, 45) }}
                                                    </a>
                                                </h6>
                                                <div class="mt-auto small text-muted">
                                                    <i class="bi bi-clock me-1 text-primary"></i>{{ $rPost->reading_time }} min read
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Comments Section -->
                    <div class="pt-4 border-top" id="commentsSection">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h3 class="fw-bold mb-0">
                                <i class="bi bi-chat-left-text text-primary me-2"></i> Comments ({{ $comments->count() }})
                            </h3>
                        </div>

                        <!-- Post a Comment Form -->
                        <div class="card border-0 shadow-sm p-4 rounded-4 bg-body-tertiary mb-5">
                            <h5 class="fw-bold mb-3">Leave a Comment</h5>
                            <form action="{{ route('comments.store', $post->id) }}" method="POST">
                                @csrf
                                @guest
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Your Name *</label>
                                            <input type="text" name="guest_name" class="form-control" placeholder="Jane Doe" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Your Email *</label>
                                            <input type="email" name="guest_email" class="form-control" placeholder="jane@example.com" required>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle" width="28" height="28">
                                        <span class="small fw-semibold">Commenting as <strong>{{ auth()->user()->name }}</strong></span>
                                    </div>
                                @endguest

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Comment *</label>
                                    <textarea name="content" class="form-control rounded-4 p-3" rows="4" placeholder="Join the discussion... Share your insights or questions" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-gradient px-4 rounded-pill">
                                    <i class="bi bi-send-fill me-1"></i> Submit Comment
                                </button>
                            </form>
                        </div>

                        <!-- Comments List -->
                        <div class="d-flex flex-column gap-4">
                            @forelse($comments as $comment)
                                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body hover-lift" id="comment-{{ $comment->id }}">
                                    <div class="d-flex gap-3">
                                        <img src="{{ $comment->author_avatar }}" alt="{{ $comment->author_name }}" class="author-avatar">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <h6 class="fw-bold mb-0">{{ $comment->author_name }}</h6>
                                                <small class="text-body-secondary">{{ $comment->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="text-body mt-2 mb-2">{{ $comment->content }}</p>

                                            <!-- Reply Toggle Button -->
                                            <button class="btn btn-link btn-sm text-decoration-none p-0 text-primary fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#replyBox-{{ $comment->id }}">
                                                <i class="bi bi-reply me-1"></i> Reply
                                            </button>

                                            <!-- Nested Reply Form -->
                                            <div class="collapse mt-3" id="replyBox-{{ $comment->id }}">
                                                <div class="card card-body bg-body-tertiary border-0 rounded-4 p-3">
                                                    <form action="{{ route('comments.store', $post->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                        @guest
                                                            <div class="row g-2 mb-2">
                                                                <div class="col-6">
                                                                    <input type="text" name="guest_name" class="form-control form-control-sm rounded-pill" placeholder="Your Name" required>
                                                                </div>
                                                                <div class="col-6">
                                                                    <input type="email" name="guest_email" class="form-control form-control-sm rounded-pill" placeholder="Your Email" required>
                                                                </div>
                                                            </div>
                                                        @endguest
                                                        <div class="mb-2">
                                                            <textarea name="content" class="form-control form-control-sm rounded-3" rows="2" placeholder="Write your reply..." required></textarea>
                                                        </div>
                                                        <button type="submit" class="btn btn-gradient btn-sm rounded-pill px-3">Reply</button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Nested Replies Display -->
                                            @if($comment->replies->count() > 0)
                                                <div class="mt-4 pt-3 border-top d-flex flex-column gap-3">
                                                    @foreach($comment->replies as $reply)
                                                        <div class="d-flex gap-2">
                                                            <img src="{{ $reply->author_avatar }}" alt="{{ $reply->author_name }}" class="rounded-circle" width="32" height="32">
                                                            <div class="flex-grow-1 bg-body-tertiary p-3 rounded-3">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span class="fw-bold small">{{ $reply->author_name }}</span>
                                                                    <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                                                </div>
                                                                <p class="small mb-0">{{ $reply->content }}</p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center p-4 bg-body-tertiary rounded-4">
                                    <p class="text-muted mb-0">No comments yet. Be the first to start the conversation!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <!-- Output Google Schema.org JSON-LD Structured Data -->
    {!! $post->schema_json_ld !!}

    @push('scripts')
    <script>
        // Smooth scrolling for TOC links
        document.querySelectorAll('.toc-link').forEach(link => {
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
            const headings = document.querySelectorAll('.post-content h2, .post-content h3');
            let activeId = null;
            headings.forEach(h => {
                const rect = h.getBoundingClientRect();
                if (rect.top <= 140) {
                    activeId = h.id;
                }
            });
            if (activeId) {
                document.querySelectorAll('.toc-link').forEach(link => {
                    if (link.getAttribute('href') === '#' + activeId) {
                        link.classList.add('text-primary', 'fw-bold');
                        link.classList.remove('text-body');
                    } else {
                        link.classList.remove('text-primary', 'fw-bold');
                        link.classList.add('text-body');
                    }
                });
            }
        });
    </script>
    @endpush
@endsection

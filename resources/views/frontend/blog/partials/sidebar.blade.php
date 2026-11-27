<!-- ========================================================================= -->
<!-- Single Post Right Sidebar: Search, Author Bio, Recent Posts, Categories & Comments -->
<!-- ========================================================================= -->
<div class="sticky-sidebar d-flex flex-column gap-4" style="font-family: var(--font-main); font-size: 1rem;">

    <!-- 0. BLOG SEARCH WIDGET -->
    <div class="card border-0 shadow-sm p-3 p-xl-4 rounded-4 bg-body sidebar-widget hover-lift">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom border-secondary border-opacity-10">
            <span class="text-gradient fw-bold" style="font-family: var(--font-main); font-size: 1rem;">
                <i class="bi bi-search text-success me-1"></i> SEARCH ARTICLES
            </span>
        </div>
        <form action="{{ route('blog.index') }}" method="GET" role="search">
            <div class="input-group">
                <input type="text" name="search" class="form-control rounded-start-pill ps-3" placeholder="Search articles..." aria-label="Search articles" value="{{ request('search') }}" style="font-family: var(--font-main); font-size: 1rem;">
                <button class="btn btn-gradient rounded-end-pill px-3" type="submit" aria-label="Search">
                    <i class="bi bi-search" aria-hidden="true"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- 1. AUTHOR BIO WIDGET -->
    <div class="card border-0 shadow-sm p-3 p-xl-4 rounded-4 bg-body sidebar-widget hover-lift">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom border-secondary border-opacity-10">
            <span class="text-success fw-bold" style="font-family: var(--font-main); font-size: 1rem;">
                <i class="bi bi-person-badge-fill me-1"></i> AUTHOR PROFILE
            </span>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1" style="font-family: var(--font-main); font-size: 1rem;">
                {{ ucfirst($post->author->role ?? 'Author') }}
            </span>
        </div>

        <div class="text-center mb-3">
            <a href="{{ route('blog.author', $post->author->id) }}" class="d-inline-block position-relative">
                <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="rounded-circle shadow-sm" width="84" height="84" style="object-fit: cover; border: 2px solid var(--neon-green, #00ff66); padding: 2px;">
            </a>
            <h5 class="fw-bold mt-2 mb-1">
                <a href="{{ route('blog.author', $post->author->id) }}" class="text-decoration-none text-body hover-gradient">
                    {{ $post->author->name }}
                </a>
            </h5>
            <span class="text-body-secondary d-block" style="font-family: var(--font-main); font-size: 1rem;">
                {{ $post->author->posts_count ?? $post->author->posts()->published()->count() }} Published Articles
            </span>
        </div>

        <!-- Brief Description / Bio -->
        <p class="text-body-secondary text-center mb-3 line-clamp-3" style="font-family: var(--font-main); font-size: 1rem; line-height: 1.55;">
            {{ $post->author->bio ?: 'Fullstack software engineer and tech analyst authoring deep-dive guides on web architecture, Laravel, and AI systems at Shrawan Effects.' }}
        </p>

        <!-- Author Website Link -->
        <div class="text-center mb-3">
            <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2 hover-lift" title="Visit Official Website" style="font-family: var(--font-main); font-size: 1rem;">
                <i class="bi bi-globe2"></i>
                <span>shrawaneffects.com</span>
                <i class="bi bi-box-arrow-up-right" style="font-size: 1rem;"></i>
            </a>
        </div>

        <!-- Author / Brand Social Media Links -->
        @php
            $authorTwitter = \App\Models\Setting::get('twitter_url');
            $authorLinkedin = \App\Models\Setting::get('linkedin_url');
            $authorGithub = \App\Models\Setting::get('github_url');
            $authorInstagram = \App\Models\Setting::get('instagram_url');
            $authorYoutube = \App\Models\Setting::get('youtube_url');
            $authorFacebook = \App\Models\Setting::get('facebook_url');
            $authorWhatsapp = \App\Models\Setting::get('whatsapp_url');
        @endphp
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 pt-3 border-top border-secondary border-opacity-10">
            @if(!empty($authorTwitter))
                <a href="{{ $authorTwitter }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle hover-lift" title="X (Twitter)" style="width: 38px; height: 38px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="bi bi-twitter-x"></i></a>
            @endif
            @if(!empty($authorLinkedin))
                <a href="{{ $authorLinkedin }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle hover-lift" title="LinkedIn" style="width: 38px; height: 38px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="bi bi-linkedin"></i></a>
            @endif
            @if(!empty($authorGithub))
                <a href="{{ $authorGithub }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle hover-lift" title="GitHub" style="width: 38px; height: 38px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="bi bi-github"></i></a>
            @endif
            @if(!empty($authorInstagram))
                <a href="{{ $authorInstagram }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle hover-lift" title="Instagram" style="width: 38px; height: 38px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="bi bi-instagram"></i></a>
            @endif
            @if(!empty($authorYoutube))
                <a href="{{ $authorYoutube }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle hover-lift" title="YouTube" style="width: 38px; height: 38px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="bi bi-youtube"></i></a>
            @endif
            @if(!empty($authorFacebook))
                <a href="{{ $authorFacebook }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary rounded-circle hover-lift" title="Facebook" style="width: 38px; height: 38px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="bi bi-facebook"></i></a>
            @endif
            @if(!empty($authorWhatsapp))
                @php
                    $waClean = preg_replace('/[^0-9]/', '', $authorWhatsapp);
                    $waSidebarLink = str_starts_with($authorWhatsapp, 'http') ? $authorWhatsapp : 'https://wa.me/' . $waClean;
                @endphp
                <a href="{{ $waSidebarLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success rounded-circle hover-lift" title="WhatsApp" style="width: 38px; height: 38px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="bi bi-whatsapp"></i></a>
            @endif
        </div>
    </div>

    <!-- 2. RECENT POSTS WIDGET -->
    @if(isset($recentPosts) && $recentPosts->count() > 0)
        <div class="card border-0 shadow-sm p-3 p-xl-4 rounded-4 bg-body sidebar-widget hover-lift">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                <span class="text-gradient fw-bold" style="font-family: var(--font-main); font-size: 1rem;">
                    <i class="bi bi-clock-history text-primary me-1"></i> RECENT POSTS
                </span>
                <span class="badge bg-body-tertiary text-body-secondary rounded-pill" style="font-family: var(--font-main); font-size: 1rem;">
                    Latest
                </span>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach($recentPosts as $rPost)
                    <div class="d-flex gap-3 align-items-start {{ !$loop->last ? 'pb-3 border-bottom border-secondary border-opacity-10' : '' }}">
                        <a href="{{ route('blog.show', $rPost->slug) }}" class="flex-shrink-0 position-relative rounded-3 overflow-hidden shadow-sm" style="width: 64px; height: 64px;">
                            <img src="{{ $rPost->image_url }}" alt="{{ $rPost->title }}" class="w-100 h-100 object-fit-cover hover-scale" loading="lazy">
                        </a>
                        <div class="flex-grow-1 overflow-hidden">
                            @if($rPost->category)
                                <a href="{{ route('blog.category', $rPost->category->slug) }}" class="badge rounded-pill text-decoration-none mb-1 text-truncate d-inline-block" style="font-family: var(--font-main); font-size: 1rem; background-color: {{ $rPost->category->color }}; color: #fff; max-width: 100%;">
                                    {{ $rPost->category->name }}
                                </a>
                            @endif
                            <h6 class="mb-1 fw-semibold line-clamp-2" style="font-family: var(--font-main); font-size: 1rem; line-height: 1.4;">
                                <a href="{{ route('blog.show', $rPost->slug) }}" class="text-decoration-none text-body hover-gradient">
                                    {{ $rPost->title }}
                                </a>
                            </h6>
                            <div class="d-flex flex-wrap align-items-center gap-2 text-body-secondary" style="font-family: var(--font-main); font-size: 1rem;">
                                <span><i class="bi bi-calendar3 me-1"></i>{{ $rPost->published_at ? $rPost->published_at->format('M d') : 'Recent' }}</span>
                                <span>&bull;</span>
                                <span><i class="bi bi-clock me-1"></i>{{ $rPost->reading_time }}m</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 3. TOP CATEGORIES WIDGET -->
    @if(isset($topCategories) && $topCategories->count() > 0)
        <div class="card border-0 shadow-sm p-3 p-xl-4 rounded-4 bg-body sidebar-widget hover-lift">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                <span class="text-gradient fw-bold" style="font-family: var(--font-main); font-size: 1rem;">
                    <i class="bi bi-folder-fill text-success me-1"></i> TOP CATEGORIES
                </span>
                <span class="badge bg-body-tertiary text-body-secondary rounded-pill" style="font-family: var(--font-main); font-size: 1rem;">
                    Topics
                </span>
            </div>

            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                @foreach($topCategories as $cat)
                    <li>
                        <a href="{{ route('blog.category', $cat->slug) }}" class="d-flex justify-content-between align-items-center gap-2 p-2 rounded-3 text-decoration-none text-body bg-body-tertiary hover-lift border border-transparent" style="font-family: var(--font-main); font-size: 1rem;">
                            <div class="d-flex align-items-center gap-2 text-truncate" style="min-width: 0;">
                                <span class="rounded-circle d-inline-block flex-shrink-0" style="width: 10px; height: 10px; background-color: {{ $cat->color ?? '#00ff66' }};"></span>
                                <span class="fw-medium text-truncate" style="font-family: var(--font-main); font-size: 1rem;">{{ $cat->name }}</span>
                            </div>
                            <span class="badge rounded-pill bg-success-subtle text-success flex-shrink-0" style="font-family: var(--font-main); font-size: 1rem;">
                                {{ $cat->published_posts_count }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 4. TOP COMMENTS WIDGET -->
    @if(isset($topComments) && $topComments->count() > 0)
        <div class="card border-0 shadow-sm p-3 p-xl-4 rounded-4 bg-body sidebar-widget hover-lift">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                <span class="text-gradient fw-bold" style="font-family: var(--font-main); font-size: 1rem;">
                    <i class="bi bi-chat-left-quote-fill text-info me-1"></i> TOP COMMENTS
                </span>
                <span class="badge bg-body-tertiary text-body-secondary rounded-pill" style="font-family: var(--font-main); font-size: 1rem;">
                    Feedback
                </span>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach($topComments as $topC)
                    <div class="{{ !$loop->last ? 'pb-3 border-bottom border-secondary border-opacity-10' : '' }}">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <img src="{{ $topC->author_avatar }}" alt="{{ $topC->author_name }}" class="rounded-circle flex-shrink-0" width="26" height="26" style="object-fit: cover;">
                            <span class="fw-bold text-body text-truncate" style="font-family: var(--font-main); font-size: 1rem;">
                                {{ $topC->author_name }}
                            </span>
                            <span class="text-body-secondary ms-auto flex-shrink-0" style="font-family: var(--font-main); font-size: 1rem;">
                                {{ $topC->created_at->diffForHumans(null, true) }}
                            </span>
                        </div>
                        @if($topC->post)
                            <div class="text-muted mb-1 text-truncate" style="font-family: var(--font-main); font-size: 1rem;">
                                on <a href="{{ route('blog.show', $topC->post->slug) }}#comment-{{ $topC->id }}" class="text-decoration-none text-primary fw-medium">{{ Str::limit($topC->post->title, 28) }}</a>
                            </div>
                        @endif
                        <p class="text-body-secondary mb-0 fst-italic" style="font-family: var(--font-main); font-size: 1rem; line-height: 1.5;">
                            &ldquo;{{ Str::limit(strip_tags($topC->content), 75) }}&rdquo;
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

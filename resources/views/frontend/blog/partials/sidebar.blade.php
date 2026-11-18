<!-- ========================================================================= -->
<!-- Single Post Left Sidebar: Author Bio, Recent Posts, Categories & Comments -->
<!-- ========================================================================= -->
<div class="sticky-sidebar d-flex flex-column gap-4">

    <!-- 1. AUTHOR BIO WIDGET -->
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body sidebar-widget hover-lift">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-10">
            <span class="font-mono small text-success fw-bold">
                <i class="bi bi-person-badge-fill me-1"></i> // AUTHOR PROFILE
            </span>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.7rem;">
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
            <small class="text-body-secondary font-mono d-block" style="font-size: 0.75rem;">
                {{ $post->author->posts_count ?? $post->author->posts()->published()->count() }} Published Transmissions
            </small>
        </div>

        <!-- Brief Description / Bio -->
        <p class="text-body-secondary small text-center mb-3 line-clamp-3" style="line-height: 1.55;">
            {{ $post->author->bio ?: 'Fullstack software engineer and tech analyst authoring deep-dive telemetry on web architecture, Laravel, and AI systems at Shrawan Effects.' }}
        </p>

        <!-- Author Website Link -->
        <div class="text-center mb-3">
            <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 font-mono small d-inline-flex align-items-center gap-1.5 hover-lift" title="Visit Official Website">
                <i class="bi bi-globe2"></i>
                <span>shrawaneffects.com</span>
                <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.65rem;"></i>
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
                <a href="{{ $authorTwitter }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-circle hover-lift" title="X (Twitter)" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-twitter-x"></i></a>
            @endif
            @if(!empty($authorLinkedin))
                <a href="{{ $authorLinkedin }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-circle hover-lift" title="LinkedIn" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-linkedin"></i></a>
            @endif
            @if(!empty($authorGithub))
                <a href="{{ $authorGithub }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-circle hover-lift" title="GitHub" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-github"></i></a>
            @endif
            @if(!empty($authorInstagram))
                <a href="{{ $authorInstagram }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-circle hover-lift" title="Instagram" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-instagram"></i></a>
            @endif
            @if(!empty($authorYoutube))
                <a href="{{ $authorYoutube }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-circle hover-lift" title="YouTube" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-youtube"></i></a>
            @endif
            @if(!empty($authorFacebook))
                <a href="{{ $authorFacebook }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary rounded-circle hover-lift" title="Facebook" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-facebook"></i></a>
            @endif
            @if(!empty($authorWhatsapp))
                @php
                    $waClean = preg_replace('/[^0-9]/', '', $authorWhatsapp);
                    $waSidebarLink = str_starts_with($authorWhatsapp, 'http') ? $authorWhatsapp : 'https://wa.me/' . $waClean;
                @endphp
                <a href="{{ $waSidebarLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success rounded-circle hover-lift" title="WhatsApp" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-whatsapp"></i></a>
            @endif
        </div>
    </div>

    <!-- 2. RECENT POSTS WIDGET -->
    @if(isset($recentPosts) && $recentPosts->count() > 0)
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-body sidebar-widget hover-lift">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                <span class="font-mono small text-gradient fw-bold font-cyber">
                    <i class="bi bi-clock-history text-primary me-1"></i> RECENT POSTS
                </span>
                <span class="badge bg-body-tertiary text-body-secondary rounded-pill font-mono" style="font-size: 0.7rem;">
                    Fresh Intel
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
                                <a href="{{ route('blog.category', $rPost->category->slug) }}" class="badge rounded-pill text-decoration-none mb-1 text-truncate d-inline-block" style="font-size: 0.65rem; background-color: {{ $rPost->category->color }}; color: #fff; max-width: 140px;">
                                    {{ $rPost->category->name }}
                                </a>
                            @endif
                            <h6 class="mb-1 fw-semibold line-clamp-2" style="font-size: 0.83rem; line-height: 1.35;">
                                <a href="{{ route('blog.show', $rPost->slug) }}" class="text-decoration-none text-body hover-gradient">
                                    {{ $rPost->title }}
                                </a>
                            </h6>
                            <div class="d-flex align-items-center gap-2 text-body-secondary font-mono" style="font-size: 0.7rem;">
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
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-body sidebar-widget hover-lift">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                <span class="font-mono small text-gradient fw-bold font-cyber">
                    <i class="bi bi-folder-fill text-success me-1"></i> TOP CATEGORIES
                </span>
                <span class="badge bg-body-tertiary text-body-secondary rounded-pill font-mono" style="font-size: 0.7rem;">
                    Sectors
                </span>
            </div>

            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                @foreach($topCategories as $cat)
                    <li>
                        <a href="{{ route('blog.category', $cat->slug) }}" class="d-flex justify-content-between align-items-center p-2 rounded-3 text-decoration-none text-body bg-body-tertiary hover-lift border border-transparent">
                            <div class="d-flex align-items-center gap-2 text-truncate">
                                <span class="rounded-circle d-inline-block flex-shrink-0" style="width: 8px; height: 8px; background-color: {{ $cat->color ?? '#00ff66' }};"></span>
                                <span class="fw-medium small text-truncate">{{ $cat->name }}</span>
                            </div>
                            <span class="badge rounded-pill bg-success-subtle text-success font-mono" style="font-size: 0.72rem;">
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
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-body sidebar-widget hover-lift">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-10">
                <span class="font-mono small text-gradient fw-bold font-cyber">
                    <i class="bi bi-chat-left-quote-fill text-info me-1"></i> TOP COMMENTS
                </span>
                <span class="badge bg-body-tertiary text-body-secondary rounded-pill font-mono" style="font-size: 0.7rem;">
                    Feedback
                </span>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach($topComments as $topC)
                    <div class="{{ !$loop->last ? 'pb-3 border-bottom border-secondary border-opacity-10' : '' }}">
                        <div class="d-flex align-items-center gap-2 mb-1.5">
                            <img src="{{ $topC->author_avatar }}" alt="{{ $topC->author_name }}" class="rounded-circle flex-shrink-0" width="22" height="22" style="object-fit: cover;">
                            <span class="fw-bold text-body small text-truncate" style="font-size: 0.78rem;">
                                {{ $topC->author_name }}
                            </span>
                            <small class="text-body-secondary font-mono ms-auto" style="font-size: 0.68rem;">
                                {{ $topC->created_at->diffForHumans(null, true) }}
                            </small>
                        </div>
                        @if($topC->post)
                            <div class="small text-muted mb-1 text-truncate" style="font-size: 0.7rem;">
                                on <a href="{{ route('blog.show', $topC->post->slug) }}#comment-{{ $topC->id }}" class="text-decoration-none text-primary fw-medium">{{ Str::limit($topC->post->title, 28) }}</a>
                            </div>
                        @endif
                        <p class="text-body-secondary small mb-0 font-monospace fst-italic" style="font-size: 0.75rem; line-height: 1.45;">
                            &ldquo;{{ Str::limit(strip_tags($topC->content), 75) }}&rdquo;
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

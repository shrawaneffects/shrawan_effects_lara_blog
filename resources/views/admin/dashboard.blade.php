@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Analytics & Overview')

@section('content')
    <!-- Dashboard Welcome Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">Welcome back, <span class="text-gradient">{{ auth()->user()->name }}</span> 👋</h3>
            <p class="text-body-secondary small mb-0">Here is what is happening across your articles, SEO performance, and system status today.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-body text-body border p-2 px-3 rounded-pill shadow-sm small">
                <i class="bi bi-calendar3 me-1 text-primary"></i> {{ now()->format('l, d M Y') }}
            </span>
        </div>
    </div>

    <!-- Stat Metrics Row -->
    <div class="row g-4 mb-4">
        <!-- Total Posts -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card shadow-sm d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-body-secondary small fw-semibold text-uppercase">Total Articles</span>
                    <h2 class="fw-bold my-1">{{ number_format($stats['total_posts']) }}</h2>
                    <span class="badge bg-success-subtle text-success small">
                        <i class="bi bi-check-circle me-1"></i>{{ $stats['published_posts'] }} Published
                    </span>
                    @if($stats['draft_posts'] > 0)
                        <span class="badge bg-secondary-subtle text-secondary small ms-1">{{ $stats['draft_posts'] }} Drafts</span>
                    @endif
                </div>
                <div class="stat-icon-box bg-primary-subtle text-primary">
                    <i class="bi bi-journal-text"></i>
                </div>
            </div>
        </div>

        <!-- Total Views -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card shadow-sm d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-body-secondary small fw-semibold text-uppercase">Total Views</span>
                    <h2 class="fw-bold my-1">{{ number_format($stats['total_views']) }}</h2>
                    <span class="badge bg-info-subtle text-info small">
                        <i class="bi bi-eye me-1"></i> Lifetime views
                    </span>
                </div>
                <div class="stat-icon-box bg-info-subtle text-info">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>

        <!-- Comments Moderation -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card shadow-sm d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-body-secondary small fw-semibold text-uppercase">Comments</span>
                    <h2 class="fw-bold my-1">{{ number_format($stats['total_comments']) }}</h2>
                    @if($stats['pending_comments'] > 0)
                        <span class="badge bg-warning text-dark small">
                            <i class="bi bi-clock me-1"></i>{{ $stats['pending_comments'] }} Pending
                        </span>
                    @else
                        <span class="badge bg-success-subtle text-success small">All Moderated</span>
                    @endif
                </div>
                <div class="stat-icon-box bg-warning-subtle text-warning">
                    <i class="bi bi-chat-dots"></i>
                </div>
            </div>
        </div>

        <!-- Users & Subscribers -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card shadow-sm d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-body-secondary small fw-semibold text-uppercase">Subscribers & Users</span>
                    <h2 class="fw-bold my-1">{{ number_format($stats['total_subscribers'] + $stats['total_users']) }}</h2>
                    <span class="badge bg-purple-subtle text-primary small">
                        {{ $stats['total_users'] }} Users &bull; {{ $stats['total_subscribers'] }} Subs
                    </span>
                </div>
                <div class="stat-icon-box bg-success-subtle text-success">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO & Server Performance Health Banner -->
    <div class="card border-0 shadow-sm p-4 mb-4 rounded-4 bg-body glass-panel" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(6, 182, 212, 0.06) 100%);">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-graph-up-arrow me-1"></i> SEO & Server Optimization Hub</h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5">2026 Engine</span>
                </div>
                <p class="text-body-secondary small mb-0">Monitor site-wide search optimization, follow/nofollow link grading, server config files (<kbd>robots.txt</kbd>, <kbd>.htaccess</kbd>), and visual brand identity.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.seo.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-speedometer2 me-1"></i> SEO Suite
                </a>
                <a href="{{ route('admin.seo.grader') }}" class="btn btn-outline-warning btn-sm rounded-pill px-3">
                    <i class="bi bi-award-fill me-1"></i> SEO Grader
                </a>
                <a href="{{ route('admin.seo.server-config') }}" class="btn btn-outline-info btn-sm rounded-pill px-3">
                    <i class="bi bi-hdd-network me-1"></i> Robots & .htaccess
                </a>
                <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-palette me-1"></i> Upload Logo / Favicon
                </a>
            </div>
        </div>

        <hr class="my-3 opacity-25">

        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="p-2.5 rounded-3 bg-body border d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block" style="font-size:0.75rem;">Avg SEO Quality</small>
                        <strong class="text-{{ $stats['avg_seo_score'] >= 75 ? 'success' : ($stats['avg_seo_score'] >= 50 ? 'warning' : 'danger') }}">{{ $stats['avg_seo_score'] }}/100</strong>
                    </div>
                    <a href="{{ route('admin.seo.index') }}" class="btn btn-sm btn-light border rounded-circle"><i class="bi bi-arrow-right-short"></i></a>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-2.5 rounded-3 bg-body border d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block" style="font-size:0.75rem;">Robots & .htaccess</small>
                        <span class="badge bg-success-subtle text-success" style="font-size:0.7rem;">Active & Backed Up</span>
                    </div>
                    <a href="{{ route('admin.seo.server-config') }}" class="btn btn-sm btn-light border rounded-circle"><i class="bi bi-arrow-right-short"></i></a>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-2.5 rounded-3 bg-body border d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block" style="font-size:0.75rem;">Brand Header Logo</small>
                        <span class="badge {{ $stats['site_logo_set'] ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}" style="font-size:0.7rem;">
                            {{ $stats['site_logo_set'] ? 'Custom Logo' : 'Default Text' }}
                        </span>
                    </div>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-light border rounded-circle"><i class="bi bi-arrow-right-short"></i></a>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-2.5 rounded-3 bg-body border d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block" style="font-size:0.75rem;">Browser Favicon</small>
                        <span class="badge {{ $stats['site_favicon_set'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}" style="font-size:0.7rem;">
                            {{ $stats['site_favicon_set'] ? 'Custom Favicon' : 'Default' }}
                        </span>
                    </div>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-light border rounded-circle"><i class="bi bi-arrow-right-short"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Bar -->
    <div class="card border-0 shadow-sm p-3 mb-4 rounded-3 bg-body">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <span class="fw-bold"><i class="bi bi-lightning-charge text-warning me-1"></i> Quick Actions:</span>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Write New Post
                </a>
                <a href="{{ route('admin.seo.grader') }}" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-award-fill me-1"></i> SEO Grader
                </a>
                <a href="{{ route('admin.seo.server-config') }}" class="btn btn-outline-info btn-sm">
                    <i class="bi bi-hdd-network me-1"></i> Robots & .htaccess
                </a>
                <a href="{{ route('admin.seo.clear-cache') }}" onclick="return confirm('Clear all system and SEO caches?');" class="btn btn-outline-danger btn-sm" title="Clear all temporary caches">
                    <i class="bi bi-trash3-fill me-1"></i> Clear Cache
                </a>
                <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-gear-wide-connected me-1"></i> Logo & Settings
                </a>
                <a href="{{ route('admin.security.index') }}" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-shield-lock-fill me-1"></i> Cyber Security
                </a>
                <a href="{{ route('admin.security.backups') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-database-check me-1"></i> Backups & Recovery
                </a>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-menu-button-wide me-1"></i> Menu Builder
                </a>
                <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye me-1"></i> View Live Site
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Articles Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-body h-100">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i> Recent Articles</h6>
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-link btn-sm text-decoration-none">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPosts as $post)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="rounded-2 me-2 object-fit-cover" style="width: 40px; height: 35px;">
                                            <div>
                                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="fw-semibold text-decoration-none text-body small">
                                                    {{ Str::limit($post->title, 40) }}
                                                </a>
                                                <div class="text-muted" style="font-size: 0.75rem;">By {{ $post->author->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($post->category)
                                            <span class="badge" style="background-color: {{ $post->category->color }};">{{ $post->category->name }}</span>
                                        @else
                                            <span class="text-muted small">None</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($post->status === 'published')
                                            <span class="badge bg-success-subtle text-success">Published</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ number_format($post->views_count) }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn btn-outline-secondary" title="View Article"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-outline-primary" title="Edit Article"><i class="bi bi-pencil"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No posts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Comments & Category Stats -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <!-- Recent Comments Box -->
                <div class="card border-0 shadow-sm rounded-4 bg-body">
                    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0"><i class="bi bi-chat-dots me-2 text-warning"></i> Recent Comments</h6>
                        <a href="{{ route('admin.comments.index') }}" class="btn btn-link btn-sm text-decoration-none">Manage</a>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex flex-column gap-3">
                            @forelse($recentComments as $comment)
                                <div class="p-2 rounded bg-body-tertiary">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold small">{{ $comment->author_name }}</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="small text-body mb-2 text-truncate">{{ $comment->content }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge {{ $comment->status === 'approved' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} small" style="font-size: 0.7rem;">
                                            {{ ucfirst($comment->status) }}
                                        </span>
                                        <a href="{{ route('blog.show', $comment->post->slug ?? '#') }}" target="_blank" class="small text-decoration-none">
                                            on {{ Str::limit($comment->post->title ?? 'Post', 20) }} <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small text-center my-3">No recent comments.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Top Categories Box -->
                <div class="card border-0 shadow-sm rounded-4 bg-body">
                    <div class="card-header bg-transparent py-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2 text-success"></i> Categories Breakdown</h6>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush">
                            @foreach($categories as $cat)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                    <span class="d-flex align-items-center small fw-semibold">
                                        <span class="d-inline-block rounded-circle me-2" style="width: 10px; height: 10px; background-color: {{ $cat->color }};"></span>
                                        {{ $cat->name }}
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $cat->posts_count }} posts</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

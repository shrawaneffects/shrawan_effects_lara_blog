@php
    $siteName = \App\Models\Setting::get('site_name', 'LaravelBlog');
    $siteLogo = \App\Models\Setting::getLogoUrl();
    $siteFavicon = \App\Models\Setting::getFaviconUrl();
@endphp
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Admin Panel</title>
    @if($siteFavicon)
        <link rel="icon" href="{{ $siteFavicon }}">
        <link rel="shortcut icon" href="{{ $siteFavicon }}">
        <link rel="apple-touch-icon" href="{{ $siteFavicon }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/trending-seo.css') }}">

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --sidebar-width: 260px;
            --gradient-primary: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            --gradient-sunset: linear-gradient(135deg, #f43f5e 0%, #fb7185 45%, #fb923c 100%);
            --gradient-emerald: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #06b6d4 100%);
        }
        body {
            font-family: var(--font-main);
            background-color: var(--bs-tertiary-bg);
            min-height: 100vh;
        }
        .top-gradient-bar {
            height: 3px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6, #ec4899, #f43f5e, #fb923c, #10b981, #06b6d4, #6366f1);
            background-size: 200% 100%;
            animation: gradientMove 6s linear infinite;
        }
        @keyframes gradientMove {
            0% { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }
        .text-gradient {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }
        .btn-gradient {
            background: var(--gradient-primary);
            color: #fff !important;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.4);
        }
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            max-height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            background-color: var(--bs-body-bg);
            border-right: 1px solid var(--bs-border-color);
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            scrollbar-width: thin;
            scrollbar-color: rgba(99, 102, 241, 0.3) transparent;
        }
        #sidebar::-webkit-scrollbar {
            width: 4px;
        }
        #sidebar::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.25);
            border-radius: 4px;
        }
        #sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.5);
        }
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            font-size: 1.25rem;
            font-weight: 800;
            border-bottom: 1px solid var(--bs-border-color);
            display: flex;
            align-items: center;
            text-decoration: none;
            position: sticky;
            top: 0;
            background-color: var(--bs-body-bg);
            z-index: 10;
        }
        .sidebar-nav {
            padding: 0.5rem 0 3.5rem 0;
            list-style: none;
            margin: 0;
        }
        .nav-item-header {
            padding: 0.75rem 1.5rem 0.25rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--bs-secondary-color);
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.7rem 1.5rem;
            color: var(--bs-body-color);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.25s ease;
            position: relative;
        }
        .sidebar-link i {
            font-size: 1.15rem;
            margin-right: 0.85rem;
            width: 20px;
            text-align: center;
            transition: transform 0.25s ease;
        }
        .sidebar-link:hover {
            color: #6366f1;
            background-color: rgba(99, 102, 241, 0.08);
            transform: translateX(4px);
        }
        .sidebar-link:hover i {
            transform: scale(1.15);
        }
        .sidebar-link.active {
            color: #6366f1;
            font-weight: 700;
            background-color: rgba(99, 102, 241, 0.12);
            border-right: 4px solid #6366f1;
        }
        .admin-topbar {
            background-color: var(--bs-body-bg);
            border-bottom: 1px solid var(--bs-border-color);
            padding: 0.85rem 1.75rem;
        }
        .stat-card {
            border: 1px solid var(--bs-border-color-translucent);
            border-radius: 18px;
            background-color: var(--bs-body-bg);
            padding: 1.5rem;
            transition: transform 0.35s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.35s ease;
        }
        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px rgba(99, 102, 241, 0.12);
        }
        .stat-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }
        .stat-card:hover .stat-icon-box {
            transform: scale(1.1) rotate(5deg);
        }
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.show {
                margin-left: 0;
            }
            #main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Top Animated Rainbow Line -->
    <div class="top-gradient-bar"></div>

    <!-- Admin Sidebar -->
    <nav id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand d-flex align-items-center">
            @if($siteLogo)
                <img src="{{ $siteLogo }}" alt="{{ $siteName }}" style="max-height: 34px; width: auto; object-fit: contain;" class="me-2">
                <span class="text-gradient fw-bold">{{ $siteName }}</span>
            @else
                <i class="bi bi-shield-lock-fill fs-4 me-2 text-gradient"></i>
                <span class="text-gradient fw-bold">Admin<span class="text-body">Panel</span></span>
            @endif
        </a>

        <!-- Admin User Profile Box in Sidebar -->
        <div class="px-3 py-2.5 mx-2.5 my-2.5 rounded-4 border bg-body-tertiary d-flex align-items-center gap-2.5 shadow-sm">
            <div class="position-relative flex-shrink-0">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle border" width="40" height="40" style="object-fit: cover; border-color: #6366f1 !important;">
                <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" style="width: 10px; height: 10px;" title="Online"></span>
            </div>
            <div class="overflow-hidden flex-grow-1">
                <div class="fw-bold text-truncate text-body" style="font-size: 0.9rem;" title="{{ auth()->user()->name }}">
                    {{ auth()->user()->name }}
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                    <i class="bi bi-shield-check me-0.5"></i> {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
        </div>

        <ul class="sidebar-nav">
            <li class="nav-item-header">Overview</li>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            @if(auth()->user()->isAdmin())
                <li>
                    <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear-wide-connected text-primary"></i>
                        <span class="d-flex align-items-center justify-content-between w-100">
                            <span>Website Settings</span>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">Homepage & Logo</span>
                        </span>
                    </a>
                </li>
            @endif

            <li class="nav-item-header">Content Management</li>
            <li>
                <a href="{{ route('admin.media.index') }}" class="sidebar-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                    <i class="bi bi-collection-play-fill text-gradient"></i> Media Library
                </a>
            </li>
            <li>
                <a href="{{ route('admin.posts.index') }}" class="sidebar-link {{ request()->routeIs('admin.posts.index') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Articles / Posts
                </a>
            </li>
            <li>
                <a href="{{ route('admin.posts.create') }}" class="sidebar-link {{ request()->routeIs('admin.posts.create') ? 'active' : '' }}">
                    <i class="bi bi-pencil-square"></i> New Article
                </a>
            </li>
            <li>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-folder2"></i> Categories
                </a>
            </li>
            <li>
                <a href="{{ route('admin.tags.index') }}" class="sidebar-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Tags
                </a>
            </li>
            <li>
                <a href="{{ route('admin.pages.index') }}" class="sidebar-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-richtext"></i> Custom Pages
                </a>
            </li>
            <li>
                <a href="{{ route('admin.services.index') }}" class="sidebar-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <i class="bi bi-briefcase-fill text-warning"></i> Services & Offerings
                </a>
            </li>
            <li>
                <a href="{{ route('admin.comments.index') }}" class="sidebar-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-left-dots"></i> Comments Moderation
                </a>
            </li>

            <li class="nav-item-header">SEO & Performance</li>
            <li>
                <a href="{{ route('admin.seo.index') }}" class="sidebar-link {{ request()->routeIs('admin.seo.index') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow text-primary"></i> SEO Health Suite
                </a>
            </li>
            <li>
                <a href="{{ route('admin.seo.grader') }}" class="sidebar-link {{ request()->routeIs('admin.seo.grader') ? 'active' : '' }}">
                    <i class="bi bi-award-fill text-warning"></i> On-Page SEO Grader
                </a>
            </li>
            <li>
                <a href="{{ route('admin.seo.redirects') }}" class="sidebar-link {{ request()->routeIs('admin.seo.redirects') ? 'active' : '' }}">
                    <i class="bi bi-signpost-split"></i> 301 Redirects
                </a>
            </li>
            <li>
                <a href="{{ route('admin.seo.server-config') }}" class="sidebar-link {{ request()->routeIs('admin.seo.server-config*') ? 'active' : '' }}">
                    <i class="bi bi-hdd-network text-info"></i> Robots.txt & .htaccess
                </a>
            </li>
            <li>
                <a href="{{ route('admin.seo.clear-cache') }}" onclick="return confirm('Clear all system, route, views, and SEO caches?');" class="sidebar-link text-danger-emphasis">
                    <i class="bi bi-trash3-fill text-danger"></i> Clear Cache
                </a>
            </li>

            @if(auth()->user()->isAdmin())
                <li class="nav-item-header">Administration</li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Users & Roles
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.contacts.index') }}" class="sidebar-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                        <i class="bi bi-inbox"></i> Contact Messages
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear-wide-connected"></i> Website Settings & Logo
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.menus.index') }}" class="sidebar-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                        <i class="bi bi-menu-button-wide"></i> Navigation Menus
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.security.index') }}" class="sidebar-link {{ request()->routeIs('admin.security.index') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock-fill text-warning"></i> Cyber Security
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.security.audit-logs') }}" class="sidebar-link {{ request()->routeIs('admin.security.audit-logs') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Audit Logs
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.security.backups') }}" class="sidebar-link {{ request()->routeIs('admin.security.backups') ? 'active' : '' }}">
                        <i class="bi bi-database-check"></i> Disaster Recovery
                    </a>
                </li>
            @endif

            <li class="nav-item-header">External</li>
            <li>
                <a href="{{ route('home') }}" target="_blank" class="sidebar-link text-primary">
                    <i class="bi bi-box-arrow-up-right"></i> View Public Blog
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content Area -->
    <div id="main-content">
        <!-- Top Navbar -->
        <header class="admin-topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary btn-sm d-lg-none me-2" id="sidebarToggleBtn" type="button">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h5 class="mb-0 fw-bold">@yield('page_title', 'Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm px-2.5 rounded-pill d-inline-flex align-items-center shadow-xs" title="Website Settings & Homepage Router">
                        <i class="bi bi-gear-wide-connected text-primary me-1"></i> <span class="d-none d-md-inline small fw-semibold">Settings</span>
                    </a>
                @endif

                <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm px-3 d-none d-sm-inline-flex align-items-center">
                    <i class="bi bi-plus-lg me-1"></i> New Article
                </a>

                <!-- Theme Toggle -->
                <button class="btn btn-outline-secondary btn-sm" id="themeToggleBtn" type="button" title="Toggle theme">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </button>

                <!-- Admin User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light border btn-sm dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-2.5 py-1 shadow-sm" type="button" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle border" width="28" height="28" style="object-fit: cover;">
                        <span class="fw-semibold text-body" style="font-size: 0.88rem;">{{ auth()->user()->name }}</span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill d-none d-lg-inline-block" style="font-size: 0.68rem;">{{ ucfirst(auth()->user()->role) }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3">
                        <li class="px-3 py-2 border-bottom mb-1 bg-body-tertiary">
                            <div class="fw-bold small">{{ auth()->user()->name }}</div>
                            <small class="text-muted text-truncate d-block" style="font-size: 0.78rem;">{{ auth()->user()->email }}</small>
                        </li>
                        <li>
                            <a class="dropdown-item py-1.5" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-gear me-2 text-primary"></i> Profile Settings
                            </a>
                        </li>
                        @if(auth()->user()->isAdmin())
                            <li>
                                <a class="dropdown-item py-1.5" href="{{ route('admin.settings.index') }}">
                                    <i class="bi bi-gear-wide-connected me-2 text-primary"></i> Website & Homepage Settings
                                </a>
                            </li>
                        @endif
                        <li>
                            <a class="dropdown-item" href="{{ route('home') }}" target="_blank">
                                <i class="bi bi-globe me-2"></i> Visit Website
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="container-fluid px-4 mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div>{{ session('warning') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                    <div>{{ session('info') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-x-circle me-1"></i> Please check errors:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <!-- Page Body Content -->
        <main class="flex-grow-1 p-4">
            @yield('content')
        </main>

        <!-- Admin Footer -->
        <footer class="bg-body py-3 px-4 border-top text-center text-muted small">
            &copy; {{ date('Y') }} Copyright by Shrawan Effects
        </footer>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme & Sidebar Toggle Script -->
    <script>
        (function() {
            const htmlElement = document.documentElement;
            const themeBtn = document.getElementById('themeToggleBtn');
            const themeIcon = document.getElementById('themeIcon');
            const sidebar = document.getElementById('sidebar');
            const sidebarBtn = document.getElementById('sidebarToggleBtn');

            function getPreferredTheme() {
                const stored = localStorage.getItem('theme');
                if (stored) return stored;
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            function setTheme(theme) {
                htmlElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('theme', theme);
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun-fill text-warning';
                } else {
                    themeIcon.className = 'bi bi-moon-stars-fill text-dark';
                }
            }

            setTheme(getPreferredTheme());

            if (themeBtn) {
                themeBtn.addEventListener('click', () => {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    setTheme(newTheme);
                });
            }

            if (sidebarBtn && sidebar) {
                sidebarBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('show');
                });
            }
        })();
    </script>
    <script src="{{ asset('js/trending-background.js') }}"></script>
    @stack('scripts')
</body>
</html>

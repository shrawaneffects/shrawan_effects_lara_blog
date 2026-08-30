<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name' => 'Alex Morgan',
            'email' => 'admin@blog.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'bio' => 'Senior Full-Stack Architect & Tech Evangelist with over 10 years of experience building high-scale web platforms.',
            'is_active' => true,
        ]);

        $author = User::create([
            'name' => 'Sarah Jenkins',
            'email' => 'author@blog.com',
            'password' => Hash::make('password'),
            'role' => 'author',
            'bio' => 'UI/UX specialist, design systems advocate, and frontend developer passionate about clean interfaces and web accessibility.',
            'is_active' => true,
        ]);

        $reader = User::create([
            'name' => 'David Chen',
            'email' => 'user@blog.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'bio' => 'Curious software engineer and tech reader exploring new web ecosystems.',
            'is_active' => true,
        ]);

        // 2. Categories
        $categoriesData = [
            [
                'name' => 'Laravel & PHP',
                'slug' => 'laravel-php',
                'description' => 'In-depth tutorials, tips, and best practices for modern PHP and Laravel ecosystem.',
                'color' => '#ff2d20',
                'image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Explore frontend, backend, APIs, and modern full-stack web engineering.',
                'color' => '#0d6efd',
                'image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'UI/UX & Design',
                'slug' => 'ui-ux-design',
                'description' => 'Design systems, Bootstrap styling, user experience paradigms, and modern interfaces.',
                'color' => '#6f42c1',
                'image' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'AI & Machine Learning',
                'slug' => 'ai-machine-learning',
                'description' => 'Insights into LLMs, neural networks, automation, and AI-driven development.',
                'color' => '#0dcaf0',
                'image' => 'https://images.unsplash.com/photo-1677442136019-21780efad99a?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Cloud & DevOps',
                'slug' => 'cloud-devops',
                'description' => 'Docker, CI/CD pipelines, Kubernetes, serverless, and deployment strategies.',
                'color' => '#198754',
                'image' => 'https://images.unsplash.com/photo-1667372393119-3d4c48d07fc9?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Career & Insights',
                'slug' => 'career-insights',
                'description' => 'Productivity hacks, tech career growth, interviewing, and engineering leadership.',
                'color' => '#fd7e14',
                'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::create($cat);
        }

        // 3. Tags
        $tagNames = [
            'Laravel', 'PHP', 'Bootstrap 5', 'JavaScript', 'Architecture',
            'AI Agents', 'DevOps', 'Docker', 'Database', 'Security',
            'Productivity', 'CSS', 'Clean Code'
        ];

        $tags = [];
        foreach ($tagNames as $name) {
            $tags[$name] = Tag::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }

        // 4. Posts
        $postsData = [
            [
                'user_id' => $admin->id,
                'category_id' => $categories['laravel-php']->id,
                'title' => 'Getting Started with Laravel 12: Modern Architecture and Key Innovations',
                'slug' => 'getting-started-with-laravel-12-modern-architecture',
                'excerpt' => 'Discover the standout features and structural improvements in Laravel 12 that supercharge full-stack PHP development.',
                'content' => '<p class="lead">Laravel 12 brings a streamlined development workflow, refined application skeleton, advanced concurrency utilities, and unprecedented performance tuning out of the box.</p>
                <h2>Why Laravel 12 is a Game Changer</h2>
                <p>The latest iteration of the framework reinforces simplicity without sacrificing enterprise-grade capabilities. With lean configuration defaults and improved routing engines, bootstrapping new web applications is faster than ever.</p>
                <div class="p-4 my-4 bg-light rounded-3 border-start border-primary border-4">
                    <h5 class="fw-bold mb-2 text-primary"><i class="bi bi-lightbulb me-2"></i>Key Takeaway</h5>
                    <p class="mb-0">Laravel 12 continues the elegant evolution of modern PHP, pairing minimal boilerplate with robust Eloquent features and zero-friction deployments.</p>
                </div>
                <h3>1. Streamlined Project Core</h3>
                <p>Laravel consolidates configuration and service providers into intuitive registration hooks, cutting down directory clutter while keeping complete customization power at your fingertips.</p>
                <pre class="bg-dark text-white p-3 rounded"><code>// Bootstrap application routes and middleware seamlessly
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.\'/../routes/web.php\',
        commands: __DIR__.\'/../routes/console.php\',
        health: \'/up\',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Custom global or group middleware
    })
    ->create();</code></pre>
                <h3>2. High-Performance Eloquent Queries</h3>
                <p>Eloquent in Laravel 12 features smarter query compiling, automatic eager load safeguards, and optimized JSON column query transformations for MySQL and PostgreSQL.</p>
                <h3>Conclusion</h3>
                <p>Whether you are building a personal publication or an enterprise SaaS, Laravel 12 is equipped to deliver speed, reliability, and delight to developers worldwide.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80',
                'status' => 'published',
                'is_featured' => true,
                'is_trending' => true,
                'views_count' => 1420,
                'reading_time' => 4,
                'meta_title' => 'Getting Started with Laravel 12 - Complete Guide',
                'meta_description' => 'A comprehensive deep dive into Laravel 12 features, performance updates, and best practices.',
                'faqs' => [
                    [
                        'question' => 'What are the minimum PHP requirements for Laravel 12?',
                        'answer' => 'Laravel 12 requires PHP 8.2 or higher, providing access to typed properties, readonly classes, and improved performance.'
                    ],
                    [
                        'question' => 'How does Laravel 12 handle application configuration?',
                        'answer' => 'Laravel 12 streamlines setup via bootstrap/app.php using a fluent configuration builder for routes, middleware, and exception handling.'
                    ],
                    [
                        'question' => 'Can I upgrade existing Laravel 11 applications to Laravel 12?',
                        'answer' => 'Yes! The upgrade process is straightforward following Laravel Shift or the official upgrade documentation.'
                    ]
                ],
                'published_at' => now()->subDays(3),
                'tags' => ['Laravel', 'PHP', 'Architecture', 'Clean Code'],
            ],
            [
                'user_id' => $author->id,
                'category_id' => $categories['ui-ux-design']->id,
                'title' => 'Mastering Modern Web UI with Bootstrap 5.3 and Theme Tokens',
                'slug' => 'mastering-modern-web-ui-with-bootstrap-5',
                'excerpt' => 'How to leverage Bootstrap 5.3 CSS variables, dark mode color modes, and utilities to craft stunning interfaces.',
                'content' => '<p class="lead">Bootstrap 5.3 makes building responsive, dark-mode ready web interfaces simpler than ever with native color modes and semantic utility tokens.</p>
                <h2>Native Dark Mode Support</h2>
                <p>By simply using the <code>data-bs-theme="dark"</code> attribute, you can toggle themes dynamically across your entire application or isolate it to specific cards or navigation bars.</p>
                <pre class="bg-dark text-white p-3 rounded"><code>&lt;html lang="en" data-bs-theme="auto"&gt;
  &lt;!-- Navbar with native dark mode --&gt;
  &lt;nav class="navbar navbar-expand-lg bg-body-tertiary"&gt;
    &lt;div class="container"&gt;
      &lt;a class="navbar-brand" href="#"&gt;Brand&lt;/a&gt;
    &lt;/div&gt;
  &lt;/nav&gt;
&lt;/html&gt;</code></pre>
                <h3>Clean Typography and Card Systems</h3>
                <p>Pairing Bootstrap with clean Google Fonts like <em>Outfit</em> or <em>Inter</em> gives your blog a polished, editorial look that engages readers.</p>
                <div class="row g-3 my-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm p-3">
                            <h5 class="text-primary"><i class="bi bi-palette me-2"></i>Semantic Palette</h5>
                            <p class="text-muted small">Standardized color variables adapt automatically between light and dark themes.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm p-3">
                            <h5 class="text-success"><i class="bi bi-phone me-2"></i>Fully Responsive</h5>
                            <p class="text-muted small">Mobile first grid ensures perfect display across phones, tablets, and 4K displays.</p>
                        </div>
                    </div>
                </div>',
                'featured_image' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=1200&q=80',
                'status' => 'published',
                'is_featured' => true,
                'is_trending' => false,
                'views_count' => 980,
                'reading_time' => 3,
                'meta_title' => 'Mastering Modern Web UI with Bootstrap 5.3',
                'meta_description' => 'Learn how to utilize Bootstrap 5 color modes and component utilities for beautiful blog layouts.',
                'published_at' => now()->subDays(5),
                'tags' => ['Bootstrap 5', 'CSS', 'UI/UX & Design'],
            ],
            [
                'user_id' => $admin->id,
                'category_id' => $categories['ai-machine-learning']->id,
                'title' => 'The Rise of Autonomous AI Agents in Full-Stack Engineering',
                'slug' => 'the-rise-of-autonomous-ai-agents',
                'excerpt' => 'How agentic workflows and tool-calling models are reshaping software development, test automation, and deployment.',
                'content' => '<p class="lead">AI is shifting from passive code completions to proactive agentic assistants capable of executing terminal tasks, verifying code, and managing end-to-end architectures.</p>
                <h2>From Autocomplete to Autonomous Pair Programmers</h2>
                <p>Engineers today collaborate with agentic systems that understand project context, run test suites, inspect database queries, and fix bugs iteratively.</p>
                <blockquote>
                    <p class="fst-italic text-secondary">"The greatest productivity multiplier in modern software is an agent that can plan, execute, and verify its own output safely."</p>
                </blockquote>
                <h3>Core Pillars of Effective Agents</h3>
                <ul>
                    <li><strong>Tool Integration:</strong> Direct access to filesystem, compilers, linters, and APIs.</li>
                    <li><strong>Self-Correction:</strong> Ability to read compiler errors and re-evaluate solutions in real-time.</li>
                    <li><strong>Context Awareness:</strong> Analyzing full repository graphs and dependencies.</li>
                </ul>',
                'featured_image' => 'https://images.unsplash.com/photo-1677442136019-21780efad99a?auto=format&fit=crop&w=1200&q=80',
                'status' => 'published',
                'is_featured' => false,
                'is_trending' => true,
                'views_count' => 2150,
                'reading_time' => 5,
                'meta_title' => 'The Rise of Autonomous AI Agents',
                'meta_description' => 'Explore how AI agents are transforming modern development teams and workflows.',
                'published_at' => now()->subDays(2),
                'tags' => ['AI Agents', 'Architecture', 'Productivity'],
            ],
            [
                'user_id' => $author->id,
                'category_id' => $categories['web-development']->id,
                'title' => '10 Essential Security Practices Every Backend Developer Must Know',
                'slug' => '10-essential-security-practices-backend-development',
                'excerpt' => 'Protect your applications against SQL injection, CSRF, XSS, rate-limiting abuse, and authentication exploits.',
                'content' => '<p class="lead">Security should never be an afterthought. Here are fundamental practices to harden your backend APIs and web applications against common vectors.</p>
                <h2>1. Parameterized Queries & ORM Protection</h2>
                <p>Never concatenate raw SQL strings. Using Eloquent or PDO prepared statements prevents SQL injection vulnerabilities completely.</p>
                <h2>2. CSRF Token Verification</h2>
                <p>Always ensure state-changing POST, PUT, and DELETE routes are protected by CSRF tokens or signed tokens.</p>
                <h2>3. Secure Password Hashing</h2>
                <p>Employ Bcrypt or Argon2id with adequate cost factors to keep user passwords safely hashed.</p>
                <h2>4. Rate Limiting</h2>
                <p>Implement aggressive rate limiting on authentication and API endpoints to thwart brute-force and credential stuffing attacks.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1200&q=80',
                'status' => 'published',
                'is_featured' => false,
                'is_trending' => false,
                'views_count' => 640,
                'reading_time' => 4,
                'meta_title' => '10 Essential Backend Security Practices',
                'meta_description' => 'Learn top security practices to safeguard web applications and APIs.',
                'published_at' => now()->subDays(7),
                'tags' => ['Security', 'PHP', 'Laravel', 'Clean Code'],
            ],
            [
                'user_id' => $admin->id,
                'category_id' => $categories['cloud-devops']->id,
                'title' => 'Containerizing Laravel Applications with Docker and Nginx in Production',
                'slug' => 'containerizing-laravel-with-docker-and-nginx',
                'excerpt' => 'A step-by-step walkthrough of creating lightweight, production-ready multi-stage Docker builds for Laravel.',
                'content' => '<p class="lead">Containerization ensures consistency across local development, staging environments, and production Kubernetes clusters.</p>
                <h2>Optimizing Multi-Stage Dockerfile</h2>
                <p>By separating composer build steps from the final PHP-FPM runtime image, you can reduce image sizes from 1GB+ to under 120MB.</p>
                <pre class="bg-dark text-white p-3 rounded"><code># Build stage
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Production runtime
FROM php:8.2-fpm-alpine
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN chown -R www-data:www-data storage bootstrap/cache</code></pre>',
                'featured_image' => 'https://images.unsplash.com/photo-1667372393119-3d4c48d07fc9?auto=format&fit=crop&w=1200&q=80',
                'status' => 'published',
                'is_featured' => false,
                'is_trending' => true,
                'views_count' => 870,
                'reading_time' => 4,
                'meta_title' => 'Dockerizing Laravel Applications',
                'meta_description' => 'Master multi-stage Docker builds for high performance Laravel deployments.',
                'published_at' => now()->subDays(4),
                'tags' => ['Docker', 'DevOps', 'Laravel'],
            ],
            [
                'user_id' => $author->id,
                'category_id' => $categories['career-insights']->id,
                'title' => 'How to Build an Impactful Tech Portfolio and Land Senior Roles',
                'slug' => 'how-to-build-an-impactful-tech-portfolio',
                'excerpt' => 'Stand out in engineering hiring cycles by showcasing problem-solving ability, code quality, and system architecture.',
                'content' => '<p class="lead">Senior engineering recruiters look for how you solve real business problems, make trade-offs, and mentor others.</p>
                <h2>Focus on Case Studies Over Generic Clones</h2>
                <p>Instead of generic to-do apps, showcase real projects with architecture diagrams, performance benchmarks, and lessons learned.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80',
                'status' => 'published',
                'is_featured' => false,
                'is_trending' => false,
                'views_count' => 510,
                'reading_time' => 3,
                'meta_title' => 'Building an Impactful Tech Portfolio',
                'meta_description' => 'Key strategies to build an impressive developer portfolio.',
                'published_at' => now()->subDays(6),
                'tags' => ['Productivity', 'Career & Insights'],
            ],
            [
                'user_id' => $admin->id,
                'category_id' => $categories['laravel-php']->id,
                'title' => 'Upcoming Trends in PHP 8.4 and Beyond',
                'slug' => 'upcoming-trends-in-php-8-4',
                'excerpt' => 'Draft exploration of property hooks, asymmetric visibility, and newly planned core features.',
                'content' => '<p>Draft preview of PHP language enhancements including property hooks and standard library upgrades.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=1200&q=80',
                'status' => 'draft',
                'is_featured' => false,
                'is_trending' => false,
                'views_count' => 0,
                'reading_time' => 2,
                'meta_title' => 'PHP 8.4 Preview',
                'meta_description' => 'Draft article on PHP 8.4 features.',
                'published_at' => null,
                'tags' => ['PHP', 'Laravel'],
            ],
        ];

        foreach ($postsData as $pData) {
            $tagNamesList = $pData['tags'];
            unset($pData['tags']);

            $post = Post::create($pData);

            // Sync tags
            $tagIds = [];
            foreach ($tagNamesList as $tName) {
                if (isset($tags[$tName])) {
                    $tagIds[] = $tags[$tName]->id;
                }
            }
            $post->tags()->sync($tagIds);

            // Add sample comments to published posts
            if ($post->status === 'published') {
                $c1 = Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $reader->id,
                    'content' => 'Fantastic breakdown! The code examples and visual explanations made this very clear to follow.',
                    'status' => 'approved',
                ]);

                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $admin->id,
                    'parent_id' => $c1->id,
                    'content' => 'Thank you David! Glad you found the examples practical. More deep-dives coming soon!',
                    'status' => 'approved',
                ]);

                Comment::create([
                    'post_id' => $post->id,
                    'guest_name' => 'Michael Scott',
                    'guest_email' => 'michael@dunder.com',
                    'content' => 'Really enjoyed this article. Bookmarked for my team!',
                    'status' => 'approved',
                ]);
            }
        }

        // 5. Custom Dynamic Pages
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h2>Our Mission & Vision</h2><p class="lead">We are a passionate community of software engineers, architects, and open-source contributors dedicated to crafting high-quality, practical web development insights.</p><h3>What We Cover</h3><p>From full-stack Laravel 12 architecture and reactive UI patterns to cloud scalability and AI developer tooling, our editorial team produces deeply researched tutorials and engineering breakdowns.</p><h3>Join Our Community</h3><p>Interested in contributing or joining our writing team? Reach out through our contact page or connect with our editorial staff.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&q=80',
                'meta_title' => 'About Our Platform - Mission and Community',
                'meta_description' => 'Learn about our technical publication, mission, editorial values, and contributor community.',
                'status' => 'published',
                'show_in_navbar' => true,
                'show_in_footer' => true,
                'order' => 1,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h2>Privacy Policy</h2><p class="lead">Your privacy is important to us. This policy outlines how we collect, protect, and respect your personal information.</p><h3>1. Information Collection</h3><p>We only collect information necessary to provide our services, such as email addresses for newsletter subscribers and account registration credentials for authors and commenters.</p><h3>2. Data Security</h3><p>We employ modern encryption standards, secure session management, and automated database backups to safeguard your information at all times.</p><h3>3. Cookies and Analytics</h3><p>We use minimal session cookies strictly necessary for site functionality, dark/light theme persistence, and authentication.</p>',
                'meta_title' => 'Privacy Policy - Data Security and Protection',
                'meta_description' => 'Our commitment to data privacy, secure communications, and transparent user controls.',
                'status' => 'published',
                'show_in_navbar' => false,
                'show_in_footer' => true,
                'order' => 2,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => '<h2>Terms of Service</h2><p class="lead">By accessing and using our publication platform, you agree to comply with and be bound by the following terms and conditions.</p><h3>1. Content License</h3><p>All tutorials, open-source code snippets, and architectural guides are provided for educational and development purposes. Code snippets are available under MIT terms unless specified otherwise.</p><h3>2. Community Guidelines</h3><p>We maintain an inclusive, respectful commenting and discussion space. Spam, harassment, or unlawful content will be moderated and removed immediately.</p>',
                'meta_title' => 'Terms of Service - Platform Usage Guidelines',
                'meta_description' => 'Review the terms and conditions governing the use of our publication and community platform.',
                'status' => 'published',
                'show_in_navbar' => false,
                'show_in_footer' => true,
                'order' => 3,
            ],
        ];

        foreach ($pages as $p) {
            \App\Models\Page::firstOrCreate(
                ['slug' => $p['slug']],
                $p
            );
        }
    }
}


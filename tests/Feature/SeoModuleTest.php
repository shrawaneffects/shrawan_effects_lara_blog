<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostSeo;
use App\Models\SeoRedirect;
use App\Models\User;
use App\Services\Seo\SeoAuditService;
use App\Services\Seo\SeoScoreService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SeoModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_post_seo_relationship_and_creation(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $category = Category::create(['name' => 'Technology', 'slug' => 'technology-' . uniqid(), 'is_active' => true]);

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Mastering Laravel 12 Architecture and SEO',
            'slug' => 'mastering-laravel-12-architecture-' . uniqid(),
            'excerpt' => 'A comprehensive guide to high-performance Laravel SEO architecture.',
            'content' => '<h2>Introduction</h2><p>Laravel 12 brings groundbreaking improvements to PHP development.</p><h3>Heading 3</h3><p>More detailed analysis.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $postSeo = PostSeo::create([
            'post_id' => $post->id,
            'seo_title' => 'Mastering Laravel 12 Architecture & Performance SEO',
            'meta_description' => 'A comprehensive guide to high-performance Laravel SEO architecture and optimizations.',
            'primary_keyword' => 'laravel 12 architecture',
            'search_intent' => 'informational',
            'robots_index' => true,
            'robots_follow' => true,
            'include_in_sitemap' => true,
            'seo_score' => 88,
            'seo_status' => 'good',
        ]);

        $this->assertNotNull($post->seo);
        $this->assertEquals('laravel 12 architecture', $post->seo->primary_keyword);
        $this->assertEquals('index, follow', $post->seo->robots_directive);
    }

    public function test_seo_score_calculation_and_categories(): void
    {
        $auditData = [
            'title' => 'Best Laptops for Developers 2026',
            'slug' => 'best-laptops-developers-2026',
            'content' => '<h2>Top Developer Laptops</h2><p>Here is our comparison and pricing breakdown of the best laptops.</p><h3>MacBook Pro M3 vs Dell XPS</h3><p>Pros and cons comparison.</p>',
            'excerpt' => 'A detailed review and comparison of the top laptops for software engineers.',
            'seo_title' => 'Best Laptops for Developers 2026: Comparison & Pricing Guide',
            'meta_description' => 'A detailed review, comparison, and buyer pricing guide of the top laptops for software engineers.',
            'primary_keyword' => 'best laptops',
            'search_intent' => 'commercial',
            'robots_index' => true,
            'robots_follow' => true,
            'include_in_sitemap' => true,
            'has_featured_image' => true,
        ];

        $audit = SeoAuditService::audit($auditData);

        $this->assertArrayHasKey('total_score', $audit);
        $this->assertArrayHasKey('breakdown', $audit);
        $this->assertGreaterThanOrEqual(70, $audit['total_score']);
        $this->assertEquals(25, $audit['breakdown']['technical']['max_score']);
        $this->assertEquals(25, $audit['breakdown']['content']['max_score']);
        $this->assertEquals(20, $audit['breakdown']['on_page']['max_score']);
    }

    public function test_search_intent_commercial_evaluation_warning(): void
    {
        $auditData = [
            'title' => 'What is a Computer?',
            'slug' => 'what-is-a-computer',
            'content' => '<p>A computer is an electronic device that manipulates information or data.</p>',
            'excerpt' => 'History of computers',
            'seo_title' => 'What is a Computer? History & Definition',
            'meta_description' => 'Basic introduction to how computers work.',
            'primary_keyword' => 'best computers',
            'search_intent' => 'commercial', // commercial intent but content is purely definitional
            'robots_index' => true,
            'robots_follow' => true,
        ];

        $audit = SeoAuditService::audit($auditData);

        $hasIntentWarning = false;
        foreach ($audit['breakdown']['content']['rules'] as $r) {
            if ($r['rule'] === 'search_intent_mismatch') {
                $hasIntentWarning = true;
                break;
            }
        }

        $this->assertTrue($hasIntentWarning, 'Should warn when commercial intent lacks comparison/evaluation terminology.');
    }

    public function test_heading_hierarchy_skipped_levels_detection(): void
    {
        $auditData = [
            'title' => 'Advanced PHP Guide',
            'slug' => 'advanced-php-guide',
            'content' => '<h2>Main Section</h2><p>Some text</p><h4>Skipped Heading</h4><p>Text skipping H3</p>',
            'excerpt' => 'Advanced PHP Guide overview',
            'seo_title' => 'Advanced PHP Guide',
            'meta_description' => 'Advanced PHP Guide overview description.',
            'primary_keyword' => 'advanced php',
            'search_intent' => 'informational',
            'robots_index' => true,
        ];

        $audit = SeoAuditService::audit($auditData);

        $hasSkippedHeadingWarning = false;
        foreach ($audit['breakdown']['content']['rules'] as $r) {
            if ($r['rule'] === 'heading_hierarchy_skipped') {
                $hasSkippedHeadingWarning = true;
                break;
            }
        }

        $this->assertTrue($hasSkippedHeadingWarning, 'Should detect skipped heading level from H2 to H4.');
    }

    public function test_automatic_301_redirect_on_slug_change(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $oldSlug = 'original-legacy-slug-' . uniqid();
        $newSlug = 'updated-seo-friendly-slug-' . uniqid();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Original Article Title',
            'slug' => $oldSlug,
            'excerpt' => 'Excerpt test',
            'content' => '<p>Article body content for testing 301 redirects.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->actingAs($user)->put(route('admin.posts.update', $post->id), [
            'title' => 'Updated Article Title',
            'slug' => $newSlug,
            'content' => '<p>Article body content for testing 301 redirects.</p>',
            'status' => 'published',
        ]);

        $redirect = SeoRedirect::where('old_url', '/blog/' . $oldSlug)->first();
        $this->assertNotNull($redirect, '301 redirect must be created when published slug changes');
        $this->assertEquals('/blog/' . $newSlug, $redirect->new_url);

        // Test middleware redirect response
        $response = $this->get('/blog/' . $oldSlug);
        $response->assertStatus(301);
        $response->assertRedirect('/blog/' . $newSlug);
    }

    public function test_xml_sitemap_endpoint_and_generation(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $publishedPost = Post::create([
            'user_id' => $user->id,
            'title' => 'Sitemap Test Published Post',
            'slug' => 'sitemap-test-published-' . uniqid(),
            'content' => '<p>Published post content for sitemap</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $draftPost = Post::create([
            'user_id' => $user->id,
            'title' => 'Sitemap Test Draft Post',
            'slug' => 'sitemap-test-draft-' . uniqid(),
            'content' => '<p>Draft post content should not appear in sitemap</p>',
            'status' => 'draft',
        ]);

        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('/blog/' . $publishedPost->slug);
        $response->assertDontSee('/blog/' . $draftPost->slug);
    }

    public function test_live_seo_analysis_endpoint(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($user)->postJson(route('admin.seo.analyze'), [
            'title' => 'Complete Docker Tutorial for Developers',
            'slug' => 'complete-docker-tutorial',
            'content' => '<h2>Docker Basics</h2><p>Docker enables containerized deployments.</p>',
            'seo_title' => 'Complete Docker Tutorial for Developers in 2026',
            'meta_description' => 'Learn how to build, ship, and run distributed containers with Docker.',
            'primary_keyword' => 'docker tutorial',
            'search_intent' => 'informational',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'audit' => [
                'total_score',
                'status',
                'breakdown',
                'serp_preview',
            ],
            'link_suggestions',
        ]);
    }

    public function test_frontend_seo_head_tags_and_json_ld_schema(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Full-Stack Performance Strategies',
            'slug' => 'full-stack-performance-strategies-' . uniqid(),
            'excerpt' => 'High performance full stack web techniques.',
            'content' => '<h2>Caching and Indexing</h2><p>Optimizing web workloads.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        PostSeo::create([
            'post_id' => $post->id,
            'seo_title' => 'Full-Stack Performance Strategies: 2026 Edition',
            'meta_description' => 'Master high-performance full stack web development techniques and optimizations.',
            'primary_keyword' => 'performance strategies',
            'search_intent' => 'informational',
            'robots_index' => true,
            'robots_follow' => true,
            'og_title' => 'Full-Stack Performance Strategies: 2026 Edition',
        ]);

        $response = $this->get(route('blog.show', $post->slug));
        $response->assertStatus(200);
        $response->assertSee('Full-Stack Performance Strategies: 2026 Edition', false);
        $response->assertSee('Master high-performance full stack web development techniques', false);
        $response->assertSee('rel="canonical"', false);
        $response->assertSee('content="index, follow"', false);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('"@type": "BlogPosting"', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
    }

    public function test_seo_link_grader_internal_and_external_follow_nofollow_parsing(): void
    {
        $htmlContent = '
            <p>Check out our <a href="/blog/laravel-guide">Laravel Guide</a> for more details.</p>
            <p>Also see our <a href="/about-us" rel="nofollow">About Us Page</a>.</p>
            <p>Visit external partner <a href="https://example.com/affiliate" rel="sponsored" target="_blank">Partner Site</a>.</p>
            <p>Reference <a href="https://php.net/manual" rel="nofollow noopener" target="_blank">PHP Docs</a>.</p>
            <p>See <a href="https://google.com">Google</a> and <a href="/contact">click here</a>.</p>
        ';

        $result = \App\Services\Seo\SeoLinkGraderService::grade($htmlContent, 100);

        $this->assertEquals(6, $result['total_links']);
        $this->assertEquals(3, $result['internal']['total']);
        $this->assertEquals(2, $result['internal']['dofollow']);
        $this->assertEquals(1, $result['internal']['nofollow']); // /about-us has rel="nofollow"
        $this->assertEquals(3, $result['external']['total']);
        $this->assertEquals(1, $result['external']['dofollow']); // google.com
        $this->assertEquals(1, $result['external']['nofollow']); // php.net
        $this->assertEquals(1, $result['external']['sponsored']); // example.com
        $this->assertEquals(1, $result['generic_anchors_count']); // "click here"
        $this->assertEquals(1, $result['internal_nofollow_warnings']);
    }

    public function test_seo_grader_service_letter_grades_and_summary(): void
    {
        $data = [
            'title' => 'Mastering Modern Full Stack Web Architecture in 2026',
            'slug' => 'mastering-modern-full-stack-web-architecture-2026',
            'content' => '<h2>Introduction</h2><p>Overview of system design.</p><h3>Microservices</h3><p>Detailed architecture principles and <a href="/blog/laravel-guide">Laravel link</a>.</p>',
            'excerpt' => 'A comprehensive guide on scalable architecture.',
            'seo_title' => 'Mastering Modern Full Stack Web Architecture in 2026',
            'meta_description' => 'A comprehensive guide on scalable web architecture and modern full-stack performance.',
            'primary_keyword' => 'web architecture',
            'search_intent' => 'informational',
            'robots_index' => true,
            'robots_follow' => true,
            'include_in_sitemap' => true,
            'has_featured_image' => true,
        ];

        $grader = \App\Services\Seo\SeoGraderService::grade($data);

        $this->assertArrayHasKey('letter_grade', $grader);
        $this->assertArrayHasKey('categories', $grader);
        $this->assertArrayHasKey('link_grader', $grader);
        $this->assertContains($grader['letter_grade'], ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'F']);
    }

    public function test_admin_can_access_onpage_seo_grader_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($user)->get(route('admin.seo.grader'));
        $response->assertStatus(200);
        $response->assertSee('On-Page SEO Grader');
        $response->assertSee('Content Links Inventory');
    }

    public function test_admin_can_run_ondemand_seo_grader_api(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($user)->postJson(route('admin.seo.grader.run'), [
            'title' => 'Interactive On-Demand SEO Grader Test',
            'content' => '<h2>Testing Live Grader</h2><p>Here is an internal link <a href="/blog/test">Internal Guide</a> and an external <a href="https://wikipedia.org" rel="nofollow">Wiki</a>.</p>',
            'primary_keyword' => 'grader test',
            'search_intent' => 'informational',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'result' => [
                'total_score',
                'letter_grade',
                'grade_label',
                'categories',
                'link_grader',
            ]
        ]);
    }

    public function test_admin_can_access_server_config_and_update_robots_and_htaccess(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // 1. Access Server Config View
        $response = $this->actingAs($user)->get(route('admin.seo.server-config'));
        $response->assertStatus(200);
        $response->assertSee('Server Configuration &amp; SEO Files', false);
        $response->assertSee('robots.txt');
        $response->assertSee('.htaccess');

        // Backup existing contents before test writes
        $robotsOrig = file_exists(public_path('robots.txt')) ? file_get_contents(public_path('robots.txt')) : null;
        $htaccessOrig = file_exists(public_path('.htaccess')) ? file_get_contents(public_path('.htaccess')) : null;

        try {
            // 2. Update robots.txt
            $testRobots = "User-agent: *\nDisallow: /admin/\n\nSitemap: " . url('/sitemap.xml');
            $robotsUpdate = $this->actingAs($user)->post(route('admin.seo.server-config.robots'), [
                'content' => $testRobots,
            ]);
            $robotsUpdate->assertRedirect();
            $this->assertFileExists(public_path('robots.txt'));
            $this->assertEquals($testRobots, file_get_contents(public_path('robots.txt')));

            // 3. Update .htaccess
            $testHtaccess = "<IfModule mod_rewrite.c>\nRewriteEngine On\n</IfModule>";
            $htaccessUpdate = $this->actingAs($user)->post(route('admin.seo.server-config.htaccess'), [
                'content' => $testHtaccess,
            ]);
            $htaccessUpdate->assertRedirect();
            $this->assertFileExists(public_path('.htaccess'));
            $this->assertEquals($testHtaccess, file_get_contents(public_path('.htaccess')));

            // 4. Restore from backup
            $restoreRobots = $this->actingAs($user)->post(route('admin.seo.server-config.restore-robots'));
            $restoreRobots->assertRedirect();

            $restoreHtaccess = $this->actingAs($user)->post(route('admin.seo.server-config.restore-htaccess'));
            $restoreHtaccess->assertRedirect();
        } finally {
            // Restore original files
            if ($robotsOrig !== null) {
                file_put_contents(public_path('robots.txt'), $robotsOrig);
            }
            if ($htaccessOrig !== null) {
                file_put_contents(public_path('.htaccess'), $htaccessOrig);
            }
            // Clean up temporary test backups
            if (file_exists(public_path('robots.txt.backup'))) @unlink(public_path('robots.txt.backup'));
            if (file_exists(public_path('.htaccess.backup'))) @unlink(public_path('.htaccess.backup'));
        }
    }

    public function test_admin_can_clear_seo_and_system_caches_and_optimize(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // 1. Test Clear All Caches
        $clearAllResponse = $this->actingAs($user)->post(route('admin.seo.clear-cache'), [
            'type' => 'all',
        ]);
        $clearAllResponse->assertRedirect();
        $clearAllResponse->assertSessionHas('success');

        // 2. Test Clear Views Cache
        $clearViewsResponse = $this->actingAs($user)->post(route('admin.seo.clear-cache'), [
            'type' => 'views',
        ]);
        $clearViewsResponse->assertRedirect();
        $clearViewsResponse->assertSessionHas('success');

        // 3. Test Clear Routes Cache
        $clearRoutesResponse = $this->actingAs($user)->post(route('admin.seo.clear-cache'), [
            'type' => 'routes',
        ]);
        $clearRoutesResponse->assertRedirect();
        $clearRoutesResponse->assertSessionHas('success');

        // 4. Test Direct GET request to clear-cache (URL bar navigation)
        $getClearResponse = $this->actingAs($user)->get(route('admin.seo.clear-cache'));
        $getClearResponse->assertRedirect(route('admin.seo.index'));
        $getClearResponse->assertSessionHas('success');

        // 5. Test Direct GET request to optimize
        $getOptimizeResponse = $this->actingAs($user)->get(route('admin.seo.optimize'));
        $getOptimizeResponse->assertRedirect(route('admin.seo.index'));
        $getOptimizeResponse->assertSessionHas('success');

        // 6. Test Re-Optimize System via POST
        $optimizeResponse = $this->actingAs($user)->post(route('admin.seo.optimize'));
        $optimizeResponse->assertRedirect();
        $optimizeResponse->assertSessionHas('success');

        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    }

    public function test_media_attribute_enrichment_and_media_exclusion_from_links(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::firstOrCreate(['slug' => 'tech-guide'], ['name' => 'Tech Guide', 'color' => '#6366f1', 'is_active' => true]);

        // 1. Create Article with raw un-attributed media and document links
        $rawContent = '
            <h2>Introduction</h2>
            <p>Here is an illustration:</p>
            <img src="/storage/uploads/laravel-cloud-infra.png">
            <p>Watch this video:</p>
            <video src="/storage/uploads/demo.mp4"></video>
            <p>Download our whitepaper:</p>
            <a href="/storage/uploads/laravel-whitepaper.pdf">Download Architecture PDF</a>
            <p>Embedded video:</p>
            <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe>
        ';

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Cloud Native Architecture Guide',
            'slug' => 'cloud-native-architecture-guide',
            'excerpt' => 'Exploring modern cloud native architecture.',
            'content' => $rawContent,
            'status' => 'published',
        ]);

        // Verify Processed Content automatically enriches media attributes
        $processed = $post->processed_content;

        // Image assertions
        $this->assertStringContainsString('alt="Laravel Cloud Infra"', $processed);
        $this->assertStringContainsString('loading="lazy"', $processed);
        $this->assertStringContainsString('decoding="async"', $processed);
        $this->assertStringContainsString('img-fluid', $processed);

        // Video assertions
        $this->assertStringContainsString('controls', $processed);
        $this->assertStringContainsString('preload="metadata"', $processed);
        $this->assertStringContainsString('playsinline', $processed);

        // Document link assertions
        $this->assertStringContainsString('download', $processed);
        $this->assertStringContainsString('target="_blank"', $processed);
        $this->assertStringContainsString('rel="noopener noreferrer"', $processed);
        $this->assertStringContainsString('data-doc-type="pdf"', $processed);

        // Iframe video embed assertions
        $this->assertStringContainsString('allowfullscreen', $processed);
        $this->assertStringContainsString('title="Cloud Native Architecture Guide Video Presentation"', $processed);

        // Verify media files are excluded from outgoing internal content links
        $this->assertEquals(0, $post->outgoing_links_count);
    }

    public function test_incoming_outgoing_and_orphan_links_on_published_articles(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::firstOrCreate(['slug' => 'tech-guide'], ['name' => 'Tech Guide', 'color' => '#6366f1', 'is_active' => true]);

        // Create Article A (Orphan initially)
        $postA = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Core Microservices Design',
            'slug' => 'core-microservices-design',
            'content' => '<p>Basic intro to microservices.</p>',
            'status' => 'published',
        ]);

        $this->assertTrue($postA->is_orphan);
        $this->assertEquals(0, $postA->incoming_links_count);

        // Create Article B linking to Article A
        $postB = Post::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Building Scalable APIs in 2026',
            'slug' => 'building-scalable-apis-in-2026',
            'content' => '<p>See our <a href="/blog/core-microservices-design">Microservices Guide</a> for details.</p>',
            'status' => 'published',
        ]);

        // Post A is no longer orphan
        $this->assertFalse($postA->is_orphan);
        $this->assertEquals(1, $postA->incoming_links_count);
        $this->assertCount(1, $postA->incoming_posts);
        $this->assertEquals('Building Scalable APIs in 2026', $postA->incoming_posts->first()->title);

        // Post B has outgoing link to Post A
        $this->assertCount(1, $postB->outgoing_posts);
        $this->assertEquals('Core Microservices Design', $postB->outgoing_posts->first()->title);

        // Verify Frontend Renders Knowledge Graph / Connected Guides section
        $response = $this->get('/blog/' . $postA->slug);
        $response->assertStatus(200);
        $response->assertSee('Connected Guides');
        $response->assertSee('Articles Linking Here (Inbound)');
        $response->assertSee('Building Scalable APIs in 2026');
    }
}
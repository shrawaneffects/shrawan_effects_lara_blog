<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BlogWebsiteTest extends TestCase
{
    use DatabaseTransactions;

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Laravel');
    }

    public function test_blog_index_page_loads(): void
    {
        $response = $this->get('/blog');
        $response->assertStatus(200);
        $response->assertSee('Explore Articles');
    }

    public function test_post_detail_page_loads(): void
    {
        $post = Post::published()->first();
        if ($post) {
            $response = $this->get('/blog/' . $post->slug);
            $response->assertStatus(200);
            $response->assertSee($post->title);
        }
    }

    public function test_category_page_loads(): void
    {
        $category = Category::where('is_active', true)->first();
        if ($category) {
            $response = $this->get('/category/' . $category->slug);
            $response->assertStatus(200);
            $response->assertSee($category->name);
        }
    }

    public function test_contact_page_and_submission(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $response = $this->get('/contact');
        $response->assertStatus(200);

        $postResponse = $this->post('/contact', [
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message to verify the contact form.',
        ]);

        $postResponse->assertRedirect();
        $this->assertDatabaseHas('contacts', [
            'email' => 'tester@example.com',
        ]);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\ContactFormMail::class, function ($mail) {
            return $mail->hasTo('shrawaneffects@gmail.com') && $mail->contact->email === 'tester@example.com';
        });
    }

    public function test_newsletter_subscription(): void
    {
        $response = $this->post('/newsletter', [
            'email' => 'newsletter_subscriber@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'newsletter_subscriber@example.com',
        ]);
    }

    public function test_admin_dashboard_requires_authentication(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $response = $this->actingAs($admin)->get('/admin/dashboard');
            $response->assertStatus(200);
            $response->assertSee('Analytics & Overview');
        }
    }

    public function test_admin_can_access_posts_list(): void
    {
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $response = $this->actingAs($admin)->get('/admin/posts');
            $response->assertStatus(200);
            $response->assertSeeText('All Articles');
        }
    }

    public function test_admin_can_access_post_create_page(): void
    {
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $response = $this->actingAs($admin)->get('/admin/posts/create');
            $response->assertStatus(200);
            $response->assertSee('Create New Article');
            $response->assertSee('Table of Contents');
            $response->assertSee('Schema.org Structured Data');
        }
    }

    public function test_admin_can_access_post_edit_page(): void
    {
        $admin = User::where('role', 'admin')->first();
        $post = Post::first();
        if ($admin && $post) {
            $response = $this->actingAs($admin)->get('/admin/posts/' . $post->id . '/edit');
            $response->assertStatus(200);
            $response->assertSee('Edit Article');
            $response->assertSee('Table of Contents');
            $response->assertSee('Schema.org Structured Data');
        }
    }

    public function test_admin_can_create_article(): void
    {
        $admin = User::where('role', 'admin')->first();
        $category = Category::first();
        $testSlug = 'test-article-' . uniqid();

        $response = $this->actingAs($admin)->post('/admin/posts', [
            'title' => 'New Automated Test Article',
            'slug' => $testSlug,
            'category_id' => $category ? $category->id : null,
            'excerpt' => 'This is a test article excerpt.',
            'content' => '<p>This is test content written during automated test suite execution.</p>',
            'status' => 'published',
            'is_featured' => 1,
            'is_trending' => 1,
            'new_tags' => 'Automated, Testing',
        ]);

        $response->assertRedirect('/admin/posts');
        $this->assertDatabaseHas('posts', [
            'slug' => $testSlug,
        ]);
    }

    public function test_guest_can_comment_on_post(): void
    {
        $post = Post::published()->first();

        $response = $this->post('/blog/' . $post->id . '/comments', [
            'guest_name' => 'Guest Tester',
            'guest_email' => 'guest@example.com',
            'content' => 'This is a test comment by a guest user.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'guest_name' => 'Guest Tester',
            'status' => 'approved',
        ]);
    }

    public function test_user_can_login_and_logout(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $admin = User::firstOrCreate(
            ['email' => 'admin@blog.com'],
            [
                'name' => 'Admin User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Step 1: Submit credentials -> Redirects to OTP verification screen
        $response = $this->post('/login', [
            'email' => 'admin@blog.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login/verify');
        $this->assertGuest(); // User is NOT authenticated until OTP is verified!

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\LoginVerificationMail::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });

        $verification = \App\Models\LoginVerification::where('user_id', $admin->id)->first();
        $this->assertNotNull($verification);

        // Step 2: Submit 6-digit OTP code -> User is now logged in!
        $verifyResponse = $this->post('/login/verify', [
            'code' => $verification->code,
        ]);

        $verifyResponse->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);

        // Step 3: Logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_admin_can_access_and_update_settings_and_logo(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/settings');
        $response->assertStatus(200);
        $response->assertSeeText('Website Settings');

        \Illuminate\Support\Facades\Storage::fake('public');
        $logoFile = \Illuminate\Http\UploadedFile::fake()->image('custom_logo.png', 200, 60);
        $faviconFile = \Illuminate\Http\UploadedFile::fake()->image('custom_favicon.png', 32, 32);

        $updateResponse = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'Custom Brand Blog',
            'site_tagline' => 'Next-gen Developer Insights',
            'site_logo' => $logoFile,
            'site_favicon' => $faviconFile,
            'contact_email' => 'admin@custombrand.com',
        ]);

        $updateResponse->assertRedirect('/admin/settings');
        $this->assertDatabaseHas('settings', [
            'key' => 'site_name',
            'value' => 'Custom Brand Blog',
        ]);

        // Test delete logo & favicon and update anti-screenshot and AdSense settings
        $deleteResponse = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'Custom Brand Blog',
            'delete_logo' => 1,
            'delete_favicon' => 1,
            'disable_screenshot' => 1,
            'ads_enabled' => 1,
            'ads_placeholders' => 1,
            'adsense_publisher_id' => 'ca-pub-1234567890123456',
            'ad_slot_header' => '<div id="test-header-ad">AdSense Unit</div>',
        ]);
        $deleteResponse->assertRedirect('/admin/settings');
        $this->assertDatabaseHas('settings', [
            'key' => 'site_logo',
            'value' => null,
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'site_favicon',
            'value' => null,
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'disable_screenshot',
            'value' => '1',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'adsense_publisher_id',
            'value' => 'ca-pub-1234567890123456',
        ]);

        // Verify AdSense script and Ad unit render on public pages
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('pagead2.googlesyndication.com', false);
        $homeResponse->assertSee('test-header-ad', false);
    }

    public function test_admin_can_create_article_with_toc_and_custom_schema(): void
    {
        $admin = User::where('role', 'admin')->first();
        $category = Category::first();
        $testSlug = 'toc-schema-test-' . uniqid();

        $content = '<h2>Introduction to Scalability</h2><p>Overview of system design.</p><h3>Microservices Architecture</h3><p>Detailed architecture principles.</p><h2>Conclusion</h2><p>Summary of takeaways.</p>';

        $response = $this->actingAs($admin)->post('/admin/posts', [
            'title' => 'Article with Table of Contents and Schema',
            'slug' => $testSlug,
            'category_id' => $category ? $category->id : null,
            'excerpt' => 'An article testing TOC and Schema rich snippets.',
            'content' => $content,
            'status' => 'published',
            'enable_toc' => 1,
            'schema_type' => 'TechArticle',
            'custom_schema' => '{"@context":"https://schema.org","@type":"TechArticle","headline":"Scalability Guide"}',
        ]);

        $response->assertRedirect('/admin/posts');
        $this->assertDatabaseHas('posts', [
            'slug' => $testSlug,
            'enable_toc' => true,
            'schema_type' => 'TechArticle',
        ]);

        $post = Post::where('slug', $testSlug)->first();
        $this->assertNotNull($post);
        $this->assertCount(3, $post->table_of_contents);
        $this->assertEquals('Introduction to Scalability', $post->table_of_contents[0]['title']);
        $this->assertEquals('introduction-to-scalability', $post->table_of_contents[0]['id']);

        // Test frontend display
        $detailResponse = $this->get('/blog/' . $post->slug);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Table of Contents', false);
        $detailResponse->assertSee('introduction-to-scalability');
        $detailResponse->assertSee('Scalability Guide');
    }

    public function test_faq_page_loads_successfully(): void
    {
        $response = $this->get('/faq');
        $response->assertStatus(200);
        $response->assertSee('Frequently Asked');
        $response->assertSee('General & Reading', false);
    }

    public function test_admin_can_create_article_with_custom_faqs(): void
    {
        $admin = User::where('role', 'admin')->first();
        $category = Category::first();
        $testSlug = 'article-with-faqs-' . uniqid();

        $response = $this->actingAs($admin)->post('/admin/posts', [
            'title' => 'Article with Custom FAQ Accordion',
            'slug' => $testSlug,
            'category_id' => $category ? $category->id : null,
            'excerpt' => 'An article containing custom FAQ Q&A pairs.',
            'content' => '<p>Article body content with FAQs.</p>',
            'status' => 'published',
            'enable_toc' => 1,
            'faqs' => [
                [
                    'question' => 'How does Laravel handle routing?',
                    'answer' => 'Laravel maps HTTP requests to controller actions or closures in routes/web.php.'
                ],
                [
                    'question' => 'What database engines are supported?',
                    'answer' => 'MySQL, PostgreSQL, SQLite, and SQL Server are fully supported.'
                ]
            ],
        ]);

        $response->assertRedirect('/admin/posts');

        $post = Post::where('slug', $testSlug)->first();
        $this->assertNotNull($post);
        $this->assertIsArray($post->faqs);
        $this->assertCount(2, $post->faqs);
        $this->assertEquals('How does Laravel handle routing?', $post->faqs[0]['question']);

        // Check frontend article rendering
        $showResponse = $this->get('/blog/' . $post->slug);
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Frequently Asked');
        $showResponse->assertSee('How does Laravel handle routing?');
        $showResponse->assertSee('FAQPage');
    }

    public function test_admin_posts_list_displays_incoming_outgoing_and_orphan_links(): void
    {
        $admin = User::where('role', 'admin')->first();
        $category = Category::first();

        $targetSlug = 'target-post-' . uniqid();
        $sourceSlug = 'source-post-' . uniqid();

        // 1. Target post (has 0 incoming links initially -> Orphan)
        $targetPost = Post::create([
            'user_id' => $admin->id,
            'category_id' => $category ? $category->id : null,
            'title' => 'Target Article for Inlinks',
            'slug' => $targetSlug,
            'content' => '<p>This is a destination article with no outgoing links.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // 2. Source post (links to target post -> 1 Outlink)
        $sourcePost = Post::create([
            'user_id' => $admin->id,
            'category_id' => $category ? $category->id : null,
            'title' => 'Source Article Linking to Target',
            'slug' => $sourceSlug,
            'content' => '<p>Read our in-depth guide: <a href="/blog/' . $targetSlug . '">Target Guide</a> and check <a href="https://laravel.com">Laravel Docs</a>.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Test admin posts index view
        $response = $this->actingAs($admin)->get('/admin/posts');
        $response->assertStatus(200);
        $response->assertSee('Links (SEO)');
        $response->assertSee('Orphan');
        $response->assertSee('1 In'); // Target post received 1 inlink from source post
        $response->assertSee('2 Out'); // Source post has 2 outgoing links

        // Test filtering by orphan posts
        $orphanFilterResponse = $this->actingAs($admin)->get('/admin/posts?link_status=orphan');
        $orphanFilterResponse->assertStatus(200);
        $orphanFilterResponse->assertSee($sourcePost->title); // sourcePost has 0 inlinks -> orphan

        // Test filtering by inlinked posts
        $inlinkedFilterResponse = $this->actingAs($admin)->get('/admin/posts?link_status=inlinked');
        $inlinkedFilterResponse->assertStatus(200);
        $inlinkedFilterResponse->assertSee($targetPost->title); // targetPost has 1 inlink
    }

    public function test_admin_can_access_pages_list(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/pages');
        $response->assertStatus(200);
        $response->assertSee('Custom Dynamic Pages');
    }

    public function test_admin_can_create_and_view_custom_page(): void
    {
        $admin = User::where('role', 'admin')->first();
        $slug = 'custom-guide-' . uniqid();

        $response = $this->actingAs($admin)->post('/admin/pages', [
            'title' => 'Custom Community Guide',
            'slug' => $slug,
            'content' => '<h2>Welcome to our Community</h2><p>This is a custom dynamic page created by admin.</p>',
            'status' => 'published',
            'show_in_navbar' => 1,
            'show_in_footer' => 1,
            'meta_title' => 'Community Guide SEO Title',
            'meta_description' => 'Guide description for search engines.',
        ]);

        $response->assertRedirect('/admin/pages');

        $this->assertDatabaseHas('pages', [
            'slug' => $slug,
            'title' => 'Custom Community Guide',
            'status' => 'published',
            'show_in_navbar' => true,
        ]);

        // Public page view
        $publicResponse = $this->get('/page/' . $slug);
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('Custom Community Guide');
        $publicResponse->assertSee('Welcome to our Community');
    }

    public function test_admin_can_toggle_page_status_and_delete_page(): void
    {
        $admin = User::where('role', 'admin')->first();
        $slug = 'temp-page-' . uniqid();

        $page = \App\Models\Page::create([
            'title' => 'Temporary Page',
            'slug' => $slug,
            'content' => '<p>Temporary content</p>',
            'status' => 'published',
        ]);

        // Toggle to draft
        $toggleResponse = $this->actingAs($admin)->post('/admin/pages/' . $page->id . '/toggle-status');
        $toggleResponse->assertSessionHas('success');

        $page->refresh();
        $this->assertEquals('draft', $page->status);

        // Draft page should return 404 to public guests
        $draftResponse = $this->get('/page/' . $slug);
        $draftResponse->assertStatus(404);

        // Delete page
        $deleteResponse = $this->actingAs($admin)->delete('/admin/pages/' . $page->id);
        $deleteResponse->assertRedirect('/admin/pages');
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    public function test_admin_can_access_menus_builder(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/menus?location=header');
        $response->assertStatus(200);
        $response->assertSee('Navigation Menu Builder');
        $response->assertSee('Menu Arrangement');
    }

    public function test_admin_can_add_custom_link_and_reorder_drag_and_drop(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Add custom link to header menu with full domain or clean text
        $createResponse = $this->actingAs($admin)->post('/admin/menus', [
            'type' => 'custom',
            'location' => 'header',
            'title' => 'Special Showcase',
            'url' => 'http://localhost/showcase',
            'icon' => 'bi bi-stars',
            'target' => '_blank',
        ]);

        $createResponse->assertRedirect();
        // Verifies URL was normalized to clean text /showcase
        $this->assertDatabaseHas('menu_items', [
            'location' => 'header',
            'title' => 'Special Showcase',
            'url' => '/showcase',
            'target' => '_blank',
        ]);

        // Attempting to add duplicate link is prevented
        $duplicateResponse = $this->actingAs($admin)->post('/admin/menus', [
            'type' => 'custom',
            'location' => 'header',
            'title' => 'Duplicate Showcase',
            'url' => '/showcase',
        ]);
        $duplicateResponse->assertSessionHas('error');

        $item1 = \App\Models\MenuItem::where('title', 'Special Showcase')->first();
        $item2 = \App\Models\MenuItem::where('location', 'header')->where('id', '!=', $item1->id)->first();

        // 2. Drag & Drop Reorder AJAX POST
        $reorderResponse = $this->actingAs($admin)->postJson('/admin/menus/reorder', [
            'location' => 'header',
            'items' => [
                ['id' => $item1->id, 'order' => 1, 'parent_id' => null],
                ['id' => $item2->id, 'order' => 2, 'parent_id' => null],
            ]
        ]);

        $reorderResponse->assertStatus(200);
        $reorderResponse->assertJson(['success' => true]);

        $item1->refresh();
        $this->assertEquals(1, $item1->order);

        // 3. Verify public layout renders the newly arranged menu
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Special Showcase');
    }

    public function test_admin_can_update_and_delete_menu_item(): void
    {
        $admin = User::where('role', 'admin')->first();

        $item = \App\Models\MenuItem::create([
            'location' => 'header',
            'title' => 'Temporary Link',
            'url' => '/temp',
            'order' => 99,
            'is_active' => true,
        ]);

        // Update item
        $updateResponse = $this->actingAs($admin)->put('/admin/menus/' . $item->id, [
            'title' => 'Updated Link Title',
            'url' => '/updated-url',
            'target' => '_self',
            'is_active' => 1,
        ]);
        $updateResponse->assertRedirect();

        $item->refresh();
        $this->assertEquals('Updated Link Title', $item->title);
        $this->assertEquals('/updated-url', $item->url);

        // Delete item
        $deleteResponse = $this->actingAs($admin)->delete('/admin/menus/' . $item->id);
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('menu_items', ['id' => $item->id]);
    }

    public function test_admin_can_toggle_visibility_and_bulk_hide_menus(): void
    {
        $admin = User::where('role', 'admin')->first();

        $uniqueKeyA = uniqid();
        $uniqueKeyB = uniqid();

        $itemA = \App\Models\MenuItem::create([
            'location' => 'header',
            'title' => 'Secret Menu Link ' . $uniqueKeyA,
            'url' => '/secret-' . $uniqueKeyA,
            'order' => 10,
            'is_active' => true,
        ]);

        $itemB = \App\Models\MenuItem::create([
            'location' => 'header',
            'title' => 'Hidden Showcase ' . $uniqueKeyB,
            'url' => '/secret-' . $uniqueKeyB,
            'order' => 11,
            'is_active' => true,
        ]);

        // 1. One-click toggle visibility to hidden
        $toggleResponse = $this->actingAs($admin)->post('/admin/menus/' . $itemA->id . '/toggle-visibility');
        $toggleResponse->assertSessionHas('success');

        $itemA->refresh();
        $this->assertFalse($itemA->is_active);

        // Verify hidden itemA does NOT appear in frontend header
        $this->flushSession();
        $publicResponse = $this->get('/');
        $publicResponse->assertStatus(200);
        $publicResponse->assertDontSee($itemA->title);
        $publicResponse->assertDontSee($itemA->url);

        // 2. Bulk hide itemB
        $bulkHideResponse = $this->actingAs($admin)->post('/admin/menus/bulk-action', [
            'location' => 'header',
            'action' => 'hide',
            'ids' => [$itemB->id],
        ]);
        $bulkHideResponse->assertRedirect();

        $itemB->refresh();
        $this->assertFalse($itemB->is_active);

        // 3. Bulk show both items
        $bulkShowResponse = $this->actingAs($admin)->post('/admin/menus/bulk-action', [
            'location' => 'header',
            'action' => 'show',
            'ids' => [$itemA->id, $itemB->id],
        ]);
        $bulkShowResponse->assertRedirect();

        $itemA->refresh();
        $itemB->refresh();
        $this->assertTrue($itemA->is_active);
        $this->assertTrue($itemB->is_active);
    }

    public function test_security_headers_are_present_on_responses(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
    }

    public function test_login_rate_limiting_and_failed_attempt_audit_logging(): void
    {
        $uniqueEmail = 'hacker_' . uniqid() . '@test.com';

        // Attempt 5 failed logins
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $uniqueEmail,
                'password' => 'wrongpassword123',
            ]);
        }

        // Verify audit log has recorded the failed attempts
        $this->assertDatabaseHas('audit_logs', [
            'event' => 'login_failed',
        ]);

        // 6th attempt should be throttled
        $throttledResponse = $this->post('/login', [
            'email' => $uniqueEmail,
            'password' => 'wrongpassword123',
        ]);

        $throttledResponse->assertSessionHasErrors('email');
        $this->assertDatabaseHas('audit_logs', [
            'event' => 'login_throttled',
        ]);
    }

    public function test_user_can_export_personal_data_gdpr(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/profile/export-data');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        $data = json_decode($response->getContent(), true);
        $this->assertEquals($user->email, $data['account']['email']);
        $this->assertArrayHasKey('authored_articles', $data);
        $this->assertArrayHasKey('comments', $data);
        $this->assertArrayHasKey('security_audit_logs', $data);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'event' => 'gdpr_data_exported',
        ]);
    }

    public function test_admin_can_access_security_center_audit_logs_and_backups(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Security Overview Dashboard
        $secResponse = $this->actingAs($admin)->get('/admin/security');
        $secResponse->assertStatus(200);
        $secResponse->assertSee('Cyber Security', false);
        $secResponse->assertSee('Security Health Score', false);

        // 2. Audit Logs Explorer
        $logsResponse = $this->actingAs($admin)->get('/admin/security/audit-logs');
        $logsResponse->assertStatus(200);
        $logsResponse->assertSee('Security Audit Logs', false);

        // 3. Database Backups Center
        $backupResponse = $this->actingAs($admin)->get('/admin/security/backups');
        $backupResponse->assertStatus(200);
        $backupResponse->assertSee('Database Disaster Recovery', false);

        // 4. Create a database backup snapshot
        $createBackupResponse = $this->actingAs($admin)->post('/admin/security/backups');
        $createBackupResponse->assertSessionHas('success');

        // Verify backup file exists in list
        $backups = \App\Services\BackupService::getBackupsList();
        $this->assertNotEmpty($backups);

        $latestFilename = $backups[0]['filename'];

        // 5. Download backup
        $downloadResponse = $this->actingAs($admin)->get('/admin/security/backups/' . $latestFilename . '/download');
        $downloadResponse->assertStatus(200);

        // 6. Delete backup
        $deleteBackupResponse = $this->actingAs($admin)->delete('/admin/security/backups/' . $latestFilename);
        $deleteBackupResponse->assertSessionHas('success');
    }

    public function test_admin_can_configure_custom_homepage_types(): void
    {
        $admin = User::where('role', 'admin')->first();
        $category = Category::where('is_active', true)->first();
        $post = Post::published()->first();

        $customPage = \App\Models\Page::create([
            'title' => 'Custom Front Landing Page',
            'slug' => 'custom-front-landing-' . uniqid(),
            'content' => '<h1>Welcome to Our Product Landing</h1><p>High performance marketing front page.</p>',
            'status' => 'published',
        ]);

        // 1. Test configuring Homepage to All Blogs Feed
        $updateBlogsResponse = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'LaravelBlog Test',
            'homepage_type' => 'blogs',
        ]);
        $updateBlogsResponse->assertRedirect();
        $this->assertEquals('blogs', \App\Models\Setting::get('homepage_type'));

        $homeBlogsResponse = $this->get('/');
        $homeBlogsResponse->assertStatus(200);
        $homeBlogsResponse->assertSee('Explore Articles');

        // 2. Test configuring Homepage to Specific Category
        $updateCatResponse = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'LaravelBlog Test',
            'homepage_type' => 'category',
            'homepage_category_id' => $category->id,
        ]);
        $updateCatResponse->assertRedirect();
        $this->assertEquals('category', \App\Models\Setting::get('homepage_type'));

        $homeCatResponse = $this->get('/');
        $homeCatResponse->assertStatus(200);
        $homeCatResponse->assertSee($category->name);

        // 3. Test configuring Homepage to Single Blog Post
        $updatePostResponse = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'LaravelBlog Test',
            'homepage_type' => 'post',
            'homepage_post_id' => $post->id,
        ]);
        $updatePostResponse->assertRedirect();
        $this->assertEquals('post', \App\Models\Setting::get('homepage_type'));

        $homePostResponse = $this->get('/');
        $homePostResponse->assertStatus(200);
        $homePostResponse->assertSee($post->title);

        // 4. Test configuring Homepage to Static Custom Page
        $updatePageResponse = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'LaravelBlog Test',
            'homepage_type' => 'page',
            'homepage_page_id' => $customPage->id,
        ]);
        $updatePageResponse->assertRedirect();
        $this->assertEquals('page', \App\Models\Setting::get('homepage_type'));

        $homePageResponse = $this->get('/');
        $homePageResponse->assertStatus(200);
        $homePageResponse->assertSee('Custom Front Landing Page');
        $homePageResponse->assertSee('Welcome to Our Product Landing');

        // 5. Restore default dynamic homepage
        $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'LaravelBlog Test',
            'homepage_type' => 'default',
        ]);
        $this->assertEquals('default', \App\Models\Setting::get('homepage_type'));

        $homeDefaultResponse = $this->get('/');
        $homeDefaultResponse->assertStatus(200);
    }

    public function test_admin_can_update_homepage_meta_title_and_description(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => 'admin']);
        }

        // 1. Visit settings page as Admin
        $settingsPageResponse = $this->actingAs($admin)->get('/admin/settings');
        $settingsPageResponse->assertStatus(200);
        $settingsPageResponse->assertSee('Homepage Meta Title');
        $settingsPageResponse->assertSee('Homepage Meta Description');
        $settingsPageResponse->assertSee('Live Google Search Result (SERP) Preview');

        // 2. Submit new homepage meta title & description
        $customTitle = 'Custom Awesome Tech Blog - Ultimate 2026 Developer Guides';
        $customDesc = 'Join 50k+ developers discovering high performance Laravel architectures, PHP tips, and modern UI engineering guides.';
        $customKeywords = 'laravel, php, webdev, tutorials, engineering';

        $updateResponse = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'Custom Awesome Tech Blog',
            'site_tagline' => 'Ultimate 2026 Developer Guides',
            'homepage_meta_title' => $customTitle,
            'homepage_meta_description' => $customDesc,
            'homepage_meta_keywords' => $customKeywords,
            'homepage_type' => 'default',
        ]);

        $updateResponse->assertRedirect();
        $this->assertEquals($customTitle, \App\Models\Setting::get('homepage_meta_title'));
        $this->assertEquals($customDesc, \App\Models\Setting::get('homepage_meta_description'));
        $this->assertEquals($customKeywords, \App\Models\Setting::get('homepage_meta_keywords'));

        // 3. Visit homepage and verify meta tags in HTML
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('<title>' . $customTitle . '</title>', false);
        $homeResponse->assertSee('<meta name="description" content="' . $customDesc . '">', false);
        $homeResponse->assertSee('<meta name="keywords" content="' . $customKeywords . '">', false);
        $homeResponse->assertSee('<meta property="og:title" content="' . $customTitle . '">', false);
        $homeResponse->assertSee('<meta property="og:description" content="' . $customDesc . '">', false);
    }

    public function test_login_page_does_not_contain_demo_credentials_and_has_forgot_password_link(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertDontSee('Demo Credentials');
        $response->assertDontSee('fillCredentials');
        $response->assertSee('Forgot Password?');
        $response->assertSee(route('password.request'));
    }

    public function test_user_can_request_forgot_password_and_reset_it(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $user = User::firstOrCreate(
            ['email' => 'reset_tester@example.com'],
            [
                'name' => 'Reset Tester',
                'password' => \Illuminate\Support\Facades\Hash::make('old_password123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        // 1. Visit Forgot Password Page
        $forgotPageResponse = $this->get('/forgot-password');
        $forgotPageResponse->assertStatus(200);
        $forgotPageResponse->assertSee('Forgot Password?');
        $forgotPageResponse->assertSee('Send Password Reset Link');

        // 2. Submit Forgot Password Request
        $sendLinkResponse = $this->post('/forgot-password', [
            'email' => 'reset_tester@example.com',
        ]);
        $sendLinkResponse->assertRedirect();
        $sendLinkResponse->assertSessionHas('status');

        // Verify token saved in DB
        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', 'reset_tester@example.com')
            ->first();
        $this->assertNotNull($resetRecord);
        $token = $resetRecord->token;

        // Verify Mail sent
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\ResetPasswordMail::class, function ($mail) use ($user) {
            return $mail->hasTo('reset_tester@example.com');
        });

        // 3. Visit Reset Password Form
        $resetFormResponse = $this->get('/reset-password/' . $token . '?email=reset_tester@example.com');
        $resetFormResponse->assertStatus(200);
        $resetFormResponse->assertSee('Set New Password');
        $resetFormResponse->assertSee('reset_tester@example.com');

        // 4. Submit New Password
        $resetSubmitResponse = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'reset_tester@example.com',
            'password' => 'new_secure_password_123',
            'password_confirmation' => 'new_secure_password_123',
        ]);

        $resetSubmitResponse->assertRedirect('/login');
        $resetSubmitResponse->assertSessionHas('success');

        // Verify token removed
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'reset_tester@example.com',
        ]);

        // 5. Verify user can login with new password (and completes OTP verification)
        $loginResponse = $this->post('/login', [
            'email' => 'reset_tester@example.com',
            'password' => 'new_secure_password_123',
        ]);
        $loginResponse->assertRedirect('/login/verify');

        $verification = \App\Models\LoginVerification::where('user_id', $user->id)->first();
        $this->assertNotNull($verification);

        $otpResponse = $this->post('/login/verify', [
            'code' => $verification->code,
        ]);
        $otpResponse->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_page_renders_all_social_oauth_buttons(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Google');
        $response->assertSee('Facebook');
        $response->assertSee('Twitter (X)');
        $response->assertSee('LinkedIn');
        $response->assertSee(route('social.redirect', 'google'));
        $response->assertSee(route('social.redirect', 'facebook'));
        $response->assertSee(route('social.redirect', 'twitter'));
        $response->assertSee(route('social.redirect', 'linkedin'));
    }

    public function test_user_can_login_via_email_magic_link(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $user = User::firstOrCreate(
            ['email' => 'magic_link_user@example.com'],
            [
                'name' => 'Magic Link User',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->post('/login', [
            'email' => 'magic_link_user@example.com',
            'password' => 'password123',
        ]);

        $verification = \App\Models\LoginVerification::where('user_id', $user->id)->first();
        $this->assertNotNull($verification);

        // Click the 1-click magic link from email
        $magicResponse = $this->get('/login/verify/' . $verification->token);
        $magicResponse->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_otp_code(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $user = User::firstOrCreate(
            ['email' => 'otp_fail_user@example.com'],
            [
                'name' => 'OTP Fail User',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->post('/login', [
            'email' => 'otp_fail_user@example.com',
            'password' => 'password123',
        ]);

        // Submit wrong OTP code
        $failResponse = $this->post('/login/verify', [
            'code' => '000000',
        ]);

        $failResponse->assertSessionHasErrors('code');
        $this->assertGuest();
    }
}

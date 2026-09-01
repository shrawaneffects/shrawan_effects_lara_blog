<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_admin_can_access_media_library_page(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/media');
        $response->assertStatus(200);
        $response->assertSee('Centralized Media Library');
        $response->assertSee('Upload New Media');
    }

    public function test_guest_cannot_access_media_library(): void
    {
        $response = $this->get('/admin/media');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_upload_images_with_metadata_stored_in_database(): void
    {
        $admin = User::where('role', 'admin')->first();

        $fakeImage = UploadedFile::fake()->image('tech-diagram.png', 1200, 800)->size(1500);

        $response = $this->actingAs($admin)->post('/admin/media', [
            'files' => [$fakeImage],
        ]);

        $response->assertRedirect('/admin/media');

        // Verify database persistence
        $this->assertDatabaseHas('media', [
            'original_name' => 'tech-diagram.png',
            'media_type' => 'image',
            'disk' => 'public',
        ]);

        $media = Media::where('original_name', 'tech-diagram.png')->first();
        $this->assertNotNull($media);
        $this->assertEquals('image', $media->media_type);
        $this->assertNotNull($media->url);
        $this->assertStringContainsString('tech-diagram', $media->file_name);

        // Verify physical storage
        Storage::disk('public')->assertExists($media->file_path);
    }

    public function test_admin_can_upload_videos_and_documents(): void
    {
        $admin = User::where('role', 'admin')->first();

        $fakeVideo = UploadedFile::fake()->create('tutorial-video.mp4', 5000, 'video/mp4');
        $fakeDoc = UploadedFile::fake()->create('architecture-guide.pdf', 2500, 'application/pdf');

        $response = $this->actingAs($admin)->post('/admin/media', [
            'files' => [$fakeVideo, $fakeDoc],
        ]);

        $response->assertRedirect('/admin/media');

        $this->assertDatabaseHas('media', [
            'original_name' => 'tutorial-video.mp4',
            'media_type' => 'video',
        ]);

        $this->assertDatabaseHas('media', [
            'original_name' => 'architecture-guide.pdf',
            'media_type' => 'document',
        ]);
    }

    public function test_admin_can_filter_and_search_media(): void
    {
        $admin = User::where('role', 'admin')->first();

        Media::create([
            'user_id' => $admin->id,
            'file_name' => 'alpha-screenshot.png',
            'original_name' => 'alpha-screenshot.png',
            'file_path' => 'media/images/2026/08/alpha-screenshot.png',
            'disk' => 'public',
            'mime_type' => 'image/png',
            'media_type' => 'image',
            'file_size' => 102400,
            'title' => 'Alpha Screenshot',
        ]);

        Media::create([
            'user_id' => $admin->id,
            'file_name' => 'beta-document.pdf',
            'original_name' => 'beta-document.pdf',
            'file_path' => 'media/document/2026/08/beta-document.pdf',
            'disk' => 'public',
            'mime_type' => 'application/pdf',
            'media_type' => 'document',
            'file_size' => 204800,
            'title' => 'Beta PDF Document',
        ]);

        // Filter by Image
        $imageFilterResponse = $this->actingAs($admin)->get('/admin/media?type=image');
        $imageFilterResponse->assertStatus(200);
        $imageFilterResponse->assertSee('Alpha Screenshot');

        // Search by keyword
        $searchResponse = $this->actingAs($admin)->get('/admin/media?search=Beta');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Beta PDF Document');
    }

    public function test_admin_can_update_media_metadata(): void
    {
        $admin = User::where('role', 'admin')->first();

        $media = Media::create([
            'user_id' => $admin->id,
            'file_name' => 'seo-banner.jpg',
            'original_name' => 'seo-banner.jpg',
            'file_path' => 'media/images/2026/08/seo-banner.jpg',
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'media_type' => 'image',
            'file_size' => 50000,
            'title' => 'Old Title',
        ]);

        $response = $this->actingAs($admin)->put("/admin/media/{$media->id}", [
            'title' => 'Updated SEO Title',
            'alt_text' => 'High Ranking SEO Banner Alt',
            'caption' => 'Article Featured Banner',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'title' => 'Updated SEO Title',
            'alt_text' => 'High Ranking SEO Banner Alt',
            'caption' => 'Article Featured Banner',
        ]);
    }

    public function test_admin_can_delete_media(): void
    {
        $admin = User::where('role', 'admin')->first();

        $fakeFile = UploadedFile::fake()->image('temporary-card.webp');
        $uploadResponse = $this->actingAs($admin)->post('/admin/media', [
            'files' => [$fakeFile],
        ]);

        $media = Media::where('original_name', 'temporary-card.webp')->first();
        $this->assertNotNull($media);

        $deleteResponse = $this->actingAs($admin)->delete("/admin/media/{$media->id}");
        $deleteResponse->assertRedirect();

        $this->assertDatabaseMissing('media', [
            'id' => $media->id,
        ]);
    }
}

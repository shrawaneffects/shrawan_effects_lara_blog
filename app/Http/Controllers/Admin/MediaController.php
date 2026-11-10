<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\Media\MediaUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    protected MediaUploadService $uploader;

    public function __construct(MediaUploadService $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * Display the Media Library dashboard.
     */
    public function index(Request $request): View
    {
        // Auto-scan and index files if library is currently empty
        if (Media::count() === 0) {
            $this->scanAndImportStorageFiles();
        }

        $query = Media::with('user');

        // Filter by media type
        $type = $request->input('type');
        if ($type && in_array($type, ['image', 'video', 'audio', 'document'])) {
            $query->where('media_type', $type);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Sort
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'size_desc':
                $query->orderByDesc('file_size');
                break;
            case 'size_asc':
                $query->orderBy('file_size');
                break;
            case 'name_asc':
                $query->orderBy('original_name');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $mediaItems = $query->paginate(24)->withQueryString();

        // Statistics
        $stats = [
            'total_count' => Media::count(),
            'images_count' => Media::where('media_type', 'image')->count(),
            'videos_count' => Media::where('media_type', 'video')->count(),
            'audios_count' => Media::where('media_type', 'audio')->count(),
            'documents_count' => Media::where('media_type', 'document')->count(),
            'total_size' => $this->formatTotalSize(Media::sum('file_size')),
        ];

        return view('admin.media.index', compact('mediaItems', 'stats', 'type', 'sort'));
    }

    /**
     * Store newly uploaded media files (Supports multi-file upload & AJAX dropzone).
     */
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required_without:file|array',
            'files.*' => 'file|max:102400', // 100MB max per file
            'file' => 'nullable|file|max:102400',
        ]);

        $uploadedMedia = [];
        $files = $request->file('files');

        if ($request->hasFile('file')) {
            $files = [$request->file('file')];
        }

        if (is_array($files)) {
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $media = $this->uploader->upload($file, auth()->user());
                    $uploadedMedia[] = $media;
                }
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => count($uploadedMedia),
                'media' => $uploadedMedia,
                'message' => count($uploadedMedia) . ' file(s) uploaded successfully!',
            ]);
        }

        return redirect()->route('admin.media.index')->with('success', count($uploadedMedia) . ' file(s) uploaded to Media Library!');
    }

    /**
     * Get single media details for interactive modal editor.
     */
    public function show(int $id): JsonResponse
    {
        $media = Media::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'media' => $media,
            'formatted_created_at' => $media->created_at->format('M d, Y h:i A'),
        ]);
    }

    /**
     * Update media metadata (alt text, title, caption, description).
     */
    public function update(Request $request, int $id)
    {
        $media = Media::findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:3000',
        ]);

        $media->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'media' => $media,
                'message' => 'Media metadata updated successfully!',
            ]);
        }

        return back()->with('success', 'Media metadata updated successfully!');
    }

    /**
     * Delete media file from storage and database.
     */
    public function destroy(Request $request, int $id)
    {
        $media = Media::findOrFail($id);
        $this->uploader->delete($media);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully.',
            ]);
        }

        return back()->with('success', 'Media file permanently deleted.');
    }

    /**
     * JSON API Endpoint for media picker inside Post & Page editors.
     */
    public function apiPicker(Request $request): JsonResponse
    {
        $query = Media::latest();

        if ($request->filled('type')) {
            $query->where('media_type', $request->input('type'));
        }

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        $items = $query->paginate(30);

        return response()->json($items);
    }

    /**
     * Format bytes into human readable size.
     */
    protected function formatTotalSize(int|float $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Scan server storage directories and index existing files into Media Library.
     */
    public function syncStorage(Request $request): RedirectResponse
    {
        $importedCount = $this->scanAndImportStorageFiles();

        return redirect()->route('admin.media.index')->with(
            'success',
            $importedCount > 0
                ? "Storage scan complete! {$importedCount} media asset(s) successfully registered into Media Library."
                : "Storage scan complete! All storage files are already indexed in the Media Library."
        );
    }

    /**
     * Internal scanner for public disk and post featured images
     */
    protected function scanAndImportStorageFiles(): int
    {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        $importedCount = 0;

        // 1. Scan Post featured images
        try {
            $posts = \App\Models\Post::whereNotNull('featured_image')->get();
            foreach ($posts as $post) {
                $img = $post->featured_image;
                if (!empty($img) && !str_starts_with($img, 'http://') && !str_starts_with($img, 'https://')) {
                    $cleanPath = ltrim(str_replace('storage/', '', $img), '/');
                    if ($disk->exists($cleanPath) && !Media::where('file_path', $cleanPath)->exists()) {
                        $this->uploader->importFromDisk($cleanPath, $post->author);
                        $importedCount++;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore failure
        }

        // 2. Scan public storage directory recursively
        try {
            $allFiles = $disk->allFiles();
            foreach ($allFiles as $filePath) {
                $base = basename($filePath);
                if (str_starts_with($base, '.')) {
                    continue;
                }

                if (!Media::where('file_path', $filePath)->exists()) {
                    $imported = $this->uploader->importFromDisk($filePath, auth()->user());
                    if ($imported) {
                        $importedCount++;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore failure
        }

        return $importedCount;
    }
}

<?php

namespace App\Services\Media;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUploadService
{
    /**
     * Upload and store a single media file with full database metadata.
     */
    public function upload(UploadedFile $file, ?User $user = null, array $meta = []): Media
    {
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';
        $fileSize = $file->getSize();
        $mediaType = $this->determineMediaType($mimeType, $extension);

        // Generate safe unique filename
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $slugName = Str::slug($baseName) ?: 'file';
        $uniqueSuffix = Str::random(6);
        $fileName = "{$slugName}-{$uniqueSuffix}.{$extension}";

        // Organize directory: media/{type}/{year}/{month}
        $year = date('Y');
        $month = date('m');
        $directory = "media/{$mediaType}/{$year}/{$month}";
        $filePath = "{$directory}/{$fileName}";

        // Store file in public storage
        Storage::disk('public')->putFileAs($directory, $file, $fileName);

        // Extract image dimensions if applicable
        $width = null;
        $height = null;
        if ($mediaType === 'image' && $extension !== 'svg') {
            try {
                $imageInfo = @getimagesize($file->getRealPath());
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                }
            } catch (\Throwable $e) {
                // Ignore dimension extraction failure
            }
        }

        // Generate human-friendly title & alt text default
        $title = $meta['title'] ?? ucwords(str_replace(['-', '_'], ' ', $baseName));
        $altText = $meta['alt_text'] ?? ($mediaType === 'image' ? $title : null);

        return Media::create([
            'user_id' => $user ? $user->id : (auth()->check() ? auth()->id() : null),
            'file_name' => $fileName,
            'original_name' => $originalName,
            'file_path' => $filePath,
            'disk' => 'public',
            'mime_type' => $mimeType,
            'media_type' => $mediaType,
            'file_size' => $fileSize,
            'width' => $width,
            'height' => $height,
            'alt_text' => $altText,
            'title' => $title,
            'caption' => $meta['caption'] ?? null,
            'description' => $meta['description'] ?? null,
        ]);
    }

    /**
     * Delete media record and remove physical file from disk.
     */
    public function delete(Media $media): bool
    {
        if (Storage::disk($media->disk)->exists($media->file_path)) {
            Storage::disk($media->disk)->delete($media->file_path);
        }

        return (bool) $media->delete();
    }

    /**
     * Categorize media type based on MIME type and extension.
     */
    public function determineMediaType(string $mimeType, string $extension): string
    {
        $mime = strtolower($mimeType);
        $ext = strtolower($extension);

        // 1. Images
        if (str_starts_with($mime, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'avif', 'bmp', 'ico'])) {
            return 'image';
        }

        // 2. Videos
        if (str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'webm', 'mov', 'mkv', 'avi', 'wmv', 'flv', 'm4v', '3gp'])) {
            return 'video';
        }

        // 3. Audio
        if (str_starts_with($mime, 'audio/') || in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'wma'])) {
            return 'audio';
        }

        // 4. Documents
        $docMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar-compressed',
            'application/x-tar',
            'application/x-7z-compressed',
            'text/plain',
            'text/csv',
            'application/json',
            'application/xml',
        ];

        $docExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', '7z', 'tar', 'gz', 'txt', 'csv', 'json', 'xml', 'md'];

        if (in_array($mime, $docMimes) || in_array($ext, $docExts)) {
            return 'document';
        }

        return 'other';
    }
}

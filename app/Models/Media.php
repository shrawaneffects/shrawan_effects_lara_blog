<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'user_id',
        'file_name',
        'original_name',
        'file_path',
        'disk',
        'mime_type',
        'media_type',
        'file_size',
        'width',
        'height',
        'alt_text',
        'title',
        'caption',
        'description',
    ];

    protected $appends = [
        'url',
        'formatted_size',
        'icon',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
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

    public function getIconAttribute(): string
    {
        if ($this->media_type === 'image') {
            return 'bi-file-earmark-image-fill text-primary';
        }
        if ($this->media_type === 'video') {
            return 'bi-file-earmark-play-fill text-danger';
        }
        if ($this->media_type === 'audio') {
            return 'bi-file-earmark-music-fill text-success';
        }

        $mime = strtolower($this->mime_type);
        if (str_contains($mime, 'pdf')) {
            return 'bi-file-earmark-pdf-fill text-danger';
        }
        if (str_contains($mime, 'word') || str_contains($mime, 'doc')) {
            return 'bi-file-earmark-word-fill text-primary';
        }
        if (str_contains($mime, 'excel') || str_contains($mime, 'sheet') || str_contains($mime, 'csv')) {
            return 'bi-file-earmark-excel-fill text-success';
        }
        if (str_contains($mime, 'zip') || str_contains($mime, 'tar') || str_contains($mime, 'rar') || str_contains($mime, 'compressed')) {
            return 'bi-file-earmark-zip-fill text-warning';
        }
        if (str_contains($mime, 'presentation') || str_contains($mime, 'powerpoint')) {
            return 'bi-file-earmark-ppt-fill text-warning';
        }
        if (str_contains($mime, 'text') || str_contains($mime, 'plain')) {
            return 'bi-file-earmark-text-fill text-secondary';
        }

        return 'bi-file-earmark-fill text-secondary';
    }

    public function getIsImageAttribute(): bool
    {
        return $this->media_type === 'image';
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->media_type === 'video';
    }

    public function getIsAudioAttribute(): bool
    {
        return $this->media_type === 'audio';
    }

    public function getIsDocumentAttribute(): bool
    {
        return $this->media_type === 'document';
    }

    public function scopeImages($query)
    {
        return $query->where('media_type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('media_type', 'video');
    }

    public function scopeAudios($query)
    {
        return $query->where('media_type', 'audio');
    }

    public function scopeDocuments($query)
    {
        return $query->where('media_type', 'document');
    }

    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('original_name', 'like', "%{$search}%")
              ->orWhere('file_name', 'like', "%{$search}%")
              ->orWhere('title', 'like', "%{$search}%")
              ->orWhere('alt_text', 'like', "%{$search}%")
              ->orWhere('caption', 'like', "%{$search}%");
        });
    }
}

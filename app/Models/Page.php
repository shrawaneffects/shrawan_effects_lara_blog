<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'show_in_navbar',
        'show_in_footer',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'show_in_navbar' => 'boolean',
            'show_in_footer' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeNavbar(Builder $query): Builder
    {
        return $query->published()->where('show_in_navbar', true)->orderBy('order');
    }

    public function scopeFooter(Builder $query): Builder
    {
        return $query->published()->where('show_in_footer', true)->orderBy('order');
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'LIKE', "%{$term}%")
              ->orWhere('content', 'LIKE', "%{$term}%")
              ->orWhere('slug', 'LIKE', "%{$term}%");
        });
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->featured_image) {
            return null;
        }

        if (str_starts_with($this->featured_image, 'http://') || str_starts_with($this->featured_image, 'https://')) {
            return $this->featured_image;
        }

        return Storage::disk('public')->url($this->featured_image);
    }
}

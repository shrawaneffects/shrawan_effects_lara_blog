<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'url',
        'route_name',
        'category',
        'icon',
        'badge',
        'summary',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'is_active',
        'is_featured',
        'order',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Scope for active tools.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for frontend tools.
     */
    public function scopeFrontend(Builder $query): Builder
    {
        return $query->where('type', 'frontend');
    }

    /**
     * Scope for ordered tools.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order')->latest('id');
    }

    /**
     * Get live tool URL.
     */
    public function getLiveUrlAttribute(): string
    {
        if (!empty($this->url)) {
            return str_starts_with($this->url, 'http') ? $this->url : url($this->url);
        }
        if (!empty($this->route_name) && \Illuminate\Support\Facades\Route::has($this->route_name)) {
            return route($this->route_name);
        }
        return url('/services/' . $this->slug);
    }

    /**
     * Generate unique slug from title.
     */
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}

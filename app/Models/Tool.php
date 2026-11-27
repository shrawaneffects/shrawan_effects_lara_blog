<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Tool extends Model
{
    use HasFactory;

    protected $table = 'tools';

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
        'enable_toc',
        'faqs',
        'related_tools',
        'related_posts',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'is_active',
        'is_featured',
        'order',
        'type',
    ];

    protected $casts = [
        'enable_toc' => 'boolean',
        'faqs' => 'array',
        'related_tools' => 'array',
        'related_posts' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope for active tools
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured tools
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for public frontend tools
     */
    public function scopeFrontend($query)
    {
        return $query->where('type', 'frontend');
    }

    /**
     * Scope ordered by priority
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Generate unique slug for tool
     */
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        if (empty($base)) {
            $base = 'tool';
        }

        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Extract Table of Contents items (h2, h3) from content
     */
    public function getTableOfContentsAttribute(): array
    {
        if (!$this->enable_toc || empty($this->content)) {
            return [];
        }

        $toc = [];
        $pattern = '/<h([2-3])(?:\s+[^>]*)?>(.*?)<\/h\1>/is';
        if (preg_match_all($pattern, $this->content, $matches, PREG_SET_ORDER)) {
            $usedIds = [];
            foreach ($matches as $match) {
                $level = (int) $match[1];
                $title = trim(strip_tags($match[2]));
                if (empty($title)) {
                    continue;
                }

                $slug = Str::slug($title);
                if (empty($slug)) {
                    $slug = 'heading';
                }

                $id = $slug;
                $i = 1;
                while (in_array($id, $usedIds, true)) {
                    $id = $slug . '-' . $i++;
                }
                $usedIds[] = $id;

                $toc[] = [
                    'level' => $level,
                    'title' => $title,
                    'id' => $id,
                ];
            }
        }

        return $toc;
    }

    /**
     * Automatically inject anchor IDs to h2/h3 tags in content
     */
    public function getProcessedContentAttribute(): string
    {
        if (empty($this->content)) {
            return '';
        }

        $usedIds = [];
        return preg_replace_callback(
            '/<h([2-3])(\s+[^>]*)?>(.*?)<\/h\1>/is',
            function ($matches) use (&$usedIds) {
                $level = $matches[1];
                $attributes = $matches[2] ?? '';
                $innerHtml = $matches[3];
                $title = trim(strip_tags($innerHtml));
                $slug = Str::slug($title);
                if (empty($slug)) {
                    $slug = 'heading';
                }

                $id = $slug;
                $i = 1;
                while (in_array($id, $usedIds, true)) {
                    $id = $slug . '-' . $i++;
                }
                $usedIds[] = $id;

                // Remove existing id attribute if any
                $cleanAttributes = preg_replace('/\s+id="[^"]*"/i', '', $attributes);
                return "<h{$level}{$cleanAttributes} id=\"{$id}\">{$innerHtml}</h{$level}>";
            },
            $this->content
        );
    }

    /**
     * Resolve related tools models
     */
    public function getRelatedToolsListAttribute()
    {
        $relatedIdsOrSlugs = $this->related_tools ?? [];
        $tools = collect();

        if (is_array($relatedIdsOrSlugs) && count($relatedIdsOrSlugs) > 0) {
            $tools = static::active()
                ->where('id', '!=', $this->id)
                ->where(function ($q) use ($relatedIdsOrSlugs) {
                    $q->whereIn('id', $relatedIdsOrSlugs)
                      ->orWhereIn('slug', $relatedIdsOrSlugs);
                })
                ->get();
        }

        if ($tools->count() < 4) {
            $needed = 4 - $tools->count();
            $existingIds = $tools->pluck('id')->push($this->id)->all();
            $fallback = static::active()
                ->whereNotIn('id', $existingIds)
                ->ordered()
                ->limit($needed)
                ->get();
            $tools = $tools->concat($fallback);
        }

        return $tools;
    }

    /**
     * Resolve related blog posts models
     */
    public function getRelatedPostsListAttribute()
    {
        $relatedPostIds = $this->related_posts ?? [];
        $posts = collect();

        if (is_array($relatedPostIds) && count($relatedPostIds) > 0 && class_exists(Post::class)) {
            $posts = Post::published()
                ->whereIn('id', $relatedPostIds)
                ->with(['category', 'author'])
                ->get();
        }

        if ($posts->count() < 4 && class_exists(Post::class)) {
            $needed = 4 - $posts->count();
            $existingIds = $posts->pluck('id')->all();
            $fallback = Post::published()
                ->whereNotIn('id', $existingIds)
                ->latest('published_at')
                ->with(['category', 'author'])
                ->limit($needed)
                ->get();
            $posts = $posts->concat($fallback);
        }

        return $posts;
    }

    /**
     * Get live target URL
     */
    public function getLiveUrlAttribute(): string
    {
        if (!empty($this->route_name) && Route::has($this->route_name)) {
            return route($this->route_name);
        }

        if (!empty($this->url)) {
            return Str::startsWith($this->url, ['http://', 'https://']) ? $this->url : url($this->url);
        }

        if (!empty($this->slug)) {
            return url('/services/' . $this->slug);
        }

        return url('/');
    }
}

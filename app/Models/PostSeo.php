<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostSeo extends Model
{
    protected $table = 'post_seo';

    protected $fillable = [
        'post_id',
        'seo_title',
        'meta_description',
        'primary_keyword',
        'search_intent',
        'canonical_url',
        'robots_index',
        'robots_follow',
        'include_in_sitemap',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'twitter_card',
        'schema_type',
        'seo_score',
        'seo_status',
        'last_audited_at',
    ];

    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'include_in_sitemap' => 'boolean',
            'seo_score' => 'integer',
            'last_audited_at' => 'datetime',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(PostSeoKeyword::class, 'post_seo_id');
    }

    public function secondaryKeywords(): HasMany
    {
        return $this->hasMany(PostSeoKeyword::class, 'post_seo_id')->where('type', 'secondary');
    }

    public function relatedKeywords(): HasMany
    {
        return $this->hasMany(PostSeoKeyword::class, 'post_seo_id')->where('type', 'related');
    }

    public function entities(): HasMany
    {
        return $this->hasMany(PostSeoEntity::class, 'post_seo_id');
    }

    /**
     * Effective SEO Title (with fallback to post title)
     */
    public function getEffectiveTitleAttribute(): string
    {
        return !empty($this->seo_title) ? $this->seo_title : ($this->post ? $this->post->title : '');
    }

    /**
     * Effective Meta Description (with fallback to excerpt)
     */
    public function getEffectiveDescriptionAttribute(): string
    {
        return !empty($this->meta_description) ? $this->meta_description : ($this->post ? ($this->post->excerpt ?? '') : '');
    }

    /**
     * Effective Canonical URL (with fallback to current post URL)
     */
    public function getEffectiveCanonicalAttribute(): string
    {
        if (!empty($this->canonical_url)) {
            return $this->canonical_url;
        }
        return $this->post ? route('blog.show', $this->post->slug) : url()->current();
    }

    /**
     * Effective Robots Directive string
     */
    public function getRobotsDirectiveAttribute(): string
    {
        $index = $this->robots_index ? 'index' : 'noindex';
        $follow = $this->robots_follow ? 'follow' : 'nofollow';
        return "{$index}, {$follow}";
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'is_featured',
        'is_trending',
        'views_count',
        'reading_time',
        'enable_toc',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'schema_type',
        'custom_schema',
        'faqs',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'enable_toc' => 'boolean',
            'faqs' => 'array',
            'views_count' => 'integer',
            'reading_time' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->where('status', 'approved')->whereNull('parent_id')->with('replies');
    }

    public function seo()
    {
        return $this->hasOne(PostSeo::class);
    }

    public function seoAudits()
    {
        return $this->hasMany(SeoAudit::class)->orderByDesc('audited_at');
    }

    /**
     * Get or create SEO record for this post
     */
    public function getSeoDataAttribute(): PostSeo
    {
        if ($this->relationLoaded('seo') && $this->seo) {
            return $this->seo;
        }

        return $this->seo ?: new PostSeo([
            'post_id' => $this->id,
            'seo_title' => $this->meta_title ?: $this->title,
            'meta_description' => $this->meta_description ?: $this->excerpt,
            'primary_keyword' => null,
            'search_intent' => 'informational',
            'robots_index' => true,
            'robots_follow' => true,
            'include_in_sitemap' => true,
            'schema_type' => $this->schema_type ?: 'BlogPosting',
        ]);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where(function ($q) {
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', now());
                     });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'LIKE', "%{$term}%")
              ->orWhere('excerpt', 'LIKE', "%{$term}%")
              ->orWhere('content', 'LIKE', "%{$term}%");
        });
    }

    // Accessors & Helpers
    public function getImageUrlAttribute(): string
    {
        if ($this->featured_image) {
            if (str_starts_with($this->featured_image, 'http')) {
                return $this->featured_image;
            }
            if (file_exists(public_path('storage/' . $this->featured_image)) || \Illuminate\Support\Facades\Storage::disk('public')->exists($this->featured_image)) {
                return asset('storage/' . $this->featured_image);
            }
        }
        return 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80';
    }

    public static function calculateReadingTime(?string $content): int
    {
        $words = str_word_count(strip_tags($content ?? ''));
        return max(1, (int) ceil($words / 200));
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
                if (empty($title)) continue;

                $slug = \Illuminate\Support\Str::slug($title);
                if (empty($slug)) $slug = 'heading';

                $id = $slug;
                $i = 1;
                while (in_array($id, $usedIds)) {
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
     * Automatically inject anchor IDs to h2/h3 tags in content and enrich all media attributes
     */
    public function getProcessedContentAttribute(): string
    {
        if (empty($this->content)) {
            return '';
        }

        $usedIds = [];
        $contentWithAnchors = preg_replace_callback(
            '/<h([2-3])(\s+[^>]*)?>(.*?)<\/h\1>/is',
            function ($matches) use (&$usedIds) {
                $level = $matches[1];
                $attributes = $matches[2] ?? '';
                $innerHtml = $matches[3];
                $title = trim(strip_tags($innerHtml));
                $slug = \Illuminate\Support\Str::slug($title);
                if (empty($slug)) $slug = 'heading';

                $id = $slug;
                $i = 1;
                while (in_array($id, $usedIds)) {
                    $id = $slug . '-' . $i++;
                }
                $usedIds[] = $id;

                if (preg_match('/id=[\'"][^\'"]*[\'"]/i', $attributes)) {
                    $newAttributes = preg_replace('/id=[\'"][^\'"]*[\'"]/i', 'id="' . $id . '"', $attributes);
                } else {
                    $newAttributes = ' id="' . $id . '"' . $attributes;
                }

                return "<h{$level}{$newAttributes}>{$innerHtml}</h{$level}>";
            },
            $this->content
        );

        // Enrich all images, videos, iframes, audio, and documents with required SEO/accessibility attributes
        return \App\Services\Media\MediaAttributeEnricherService::enrich($contentWithAnchors, $this->title);
    }

    /**
     * Generate Schema.org JSON-LD structured data script
     */
    public function getSchemaJsonLdAttribute(): string
    {
        if (!empty($this->custom_schema)) {
            $trimmed = trim($this->custom_schema);
            if (str_starts_with($trimmed, '<script')) {
                return $trimmed;
            }
            json_decode($trimmed);
            if (json_last_error() === JSON_ERROR_NONE) {
                return '<script type="application/ld+json">' . "\n" . $trimmed . "\n" . '</script>';
            }
        }

        $siteName = \App\Models\Setting::get('site_name', 'LaravelBlog');
        $siteLogo = \App\Models\Setting::getLogoUrl() ?? asset('storage/default.jpg');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $this->schema_type ?: 'BlogPosting',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('blog.show', $this->slug),
            ],
            'headline' => $this->title,
            'description' => $this->meta_description ?: $this->excerpt,
            'image' => [
                $this->image_url,
            ],
            'datePublished' => $this->published_at ? $this->published_at->toIso8601String() : $this->created_at->toIso8601String(),
            'dateModified' => $this->updated_at ? $this->updated_at->toIso8601String() : $this->created_at->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $this->author ? $this->author->name : 'Editorial Staff',
                'url' => $this->author ? route('blog.author', $this->author->id) : route('home'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $siteLogo,
                ],
            ],
            'articleSection' => $this->category ? $this->category->name : 'General',
            'keywords' => $this->meta_keywords ?: ($this->tags->pluck('name')->implode(', ')),
            'wordCount' => str_word_count(strip_tags($this->content)),
            'timeRequired' => 'PT' . $this->reading_time . 'M',
        ];

        $scripts = '<script type="application/ld+json">' . "\n" . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n" . '</script>';

        // If post has custom FAQs, also output FAQPage schema for Google rich results
        if (!empty($this->faqs) && is_array($this->faqs)) {
            $validFaqs = array_filter($this->faqs, function ($item) {
                return !empty($item['question']) && !empty($item['answer']);
            });

            if (count($validFaqs) > 0) {
                $faqEntities = [];
                foreach ($validFaqs as $faq) {
                    $faqEntities[] = [
                        '@type' => 'Question',
                        'name' => $faq['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faq['answer'],
                        ],
                    ];
                }

                $faqSchema = [
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => $faqEntities,
                ];

                $scripts .= "\n" . '<script type="application/ld+json">' . "\n" . json_encode($faqSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n" . '</script>';
            }
        }

        return $scripts;
    }

    /**
     * Accessor for outgoing link count (excluding media files)
     */
    public function getOutgoingLinksCountAttribute(): int
    {
        preg_match_all('/<a\s+[^>]*href=["\']([^"\']+)["\']/i', $this->content ?? '', $matches);
        $hrefs = array_filter($matches[1] ?? [], fn($h) => !\App\Services\Media\MediaAttributeEnricherService::isMediaUrl($h));
        return count($hrefs);
    }

    /**
     * Accessor for incoming link count (excluding media files)
     */
    public function getIncomingLinksCountAttribute(): int
    {
        return static::where('id', '!=', $this->id)
            ->where(function ($q) {
                $q->where('content', 'LIKE', '%/blog/' . $this->slug . '%')
                  ->orWhere('content', 'LIKE', '%href="' . $this->slug . '"%')
                  ->orWhere('content', 'LIKE', '%href=\'' . $this->slug . '\'%');
            })
            ->count();
    }

    /**
     * Get list of published posts that link TO this post (Incoming Links / Backlinks)
     */
    public function getIncomingPostsAttribute()
    {
        return static::published()
            ->where('id', '!=', $this->id)
            ->where(function ($q) {
                $q->where('content', 'LIKE', '%/blog/' . $this->slug . '%')
                  ->orWhere('content', 'LIKE', '%href="' . $this->slug . '"%')
                  ->orWhere('content', 'LIKE', '%href=\'' . $this->slug . '\'%');
            })
            ->get(['id', 'title', 'slug', 'featured_image', 'created_at', 'reading_time']);
    }

    /**
     * Get list of published posts that this post links TO (Outgoing Links)
     */
    public function getOutgoingPostsAttribute()
    {
        preg_match_all('/<a\s+[^>]*href=["\']([^"\']+)["\']/i', $this->content ?? '', $matches);
        $hrefs = $matches[1] ?? [];
        $slugs = [];

        foreach ($hrefs as $href) {
            if (\App\Services\Media\MediaAttributeEnricherService::isMediaUrl($href)) {
                continue;
            }
            if (preg_match('/\/blog\/([a-z0-9-]+)/i', $href, $sm)) {
                $slugs[] = $sm[1];
            } elseif (!str_contains($href, 'http') && !str_starts_with($href, '#') && !str_starts_with($href, '/')) {
                $slugs[] = trim($href);
            }
        }

        $slugs = array_unique(array_filter($slugs, fn($s) => $s !== $this->slug));

        if (empty($slugs)) {
            return collect();
        }

        return static::published()->whereIn('slug', $slugs)->get(['id', 'title', 'slug', 'featured_image', 'created_at', 'reading_time']);
    }

    /**
     * Accessor for orphan post status (true if 0 incoming internal links from other articles)
     */
    public function getIsOrphanAttribute(): bool
    {
        return $this->incoming_links_count === 0;
    }

    /**
     * Compute batch link metrics across all posts (excluding media files)
     */
    public static function computeLinkMetrics($postsCollection)
    {
        $allPosts = static::select('id', 'title', 'slug', 'content')->get();
        $inlinksMap = [];
        $outlinksMap = [];
        $outgoingCounts = [];

        foreach ($allPosts as $p) {
            $inlinksMap[$p->slug] = [];
            $outlinksMap[$p->slug] = [];
        }

        foreach ($allPosts as $source) {
            preg_match_all('/<a\s+[^>]*href=["\']([^"\']+)["\']/i', $source->content ?? '', $matches);
            $rawHrefs = $matches[1] ?? [];

            // Exclude media files (images, video streams, audio, pdf/doc downloads) from standard content link metrics
            $hrefs = array_filter($rawHrefs, fn($h) => !\App\Services\Media\MediaAttributeEnricherService::isMediaUrl($h));

            $totalOut = count($hrefs);
            $internalOut = 0;
            $externalOut = 0;
            $linkedSlugsInSource = [];

            foreach ($hrefs as $href) {
                $isInternal = false;
                foreach ($allPosts as $target) {
                    if ($target->slug !== $source->slug) {
                        if (
                            str_contains($href, '/blog/' . $target->slug) ||
                            str_ends_with($href, '/' . $target->slug) ||
                            $href === $target->slug
                        ) {
                            $linkedSlugsInSource[$target->slug] = [
                                'id' => $source->id,
                                'title' => $source->title,
                                'slug' => $source->slug,
                            ];
                            $outlinksMap[$source->slug][] = [
                                'id' => $target->id,
                                'title' => $target->title,
                                'slug' => $target->slug,
                            ];
                            $isInternal = true;
                        }
                    }
                }

                if ($isInternal || str_starts_with($href, '/') || str_starts_with($href, '#')) {
                    $internalOut++;
                } else {
                    $externalOut++;
                }
            }

            foreach ($linkedSlugsInSource as $targetSlug => $sourceInfo) {
                if (isset($inlinksMap[$targetSlug])) {
                    $inlinksMap[$targetSlug][] = $sourceInfo;
                }
            }

            $outgoingCounts[$source->id] = [
                'total' => $totalOut,
                'internal' => $internalOut,
                'external' => $externalOut,
            ];
        }

        $orphanCount = 0;
        foreach ($allPosts as $p) {
            if (empty($inlinksMap[$p->slug])) {
                $orphanCount++;
            }
        }

        // Attach computed metrics to each item in collection
        foreach ($postsCollection as $post) {
            $incomingList = $inlinksMap[$post->slug] ?? [];
            $outgoingArticles = $outlinksMap[$post->slug] ?? [];

            $post->incoming_links = $incomingList;
            $post->incoming_links_count = count($incomingList);
            $post->outgoing_articles = $outgoingArticles;

            $outData = $outgoingCounts[$post->id] ?? ['total' => 0, 'internal' => 0, 'external' => 0];
            $post->outgoing_links_count = $outData['total'];
            $post->outgoing_internal_count = $outData['internal'];
            $post->outgoing_external_count = $outData['external'];

            $post->is_orphan = ($post->incoming_links_count === 0);
        }

        return [
            'inlinksMap' => $inlinksMap,
            'outlinksMap' => $outlinksMap,
            'outgoingCounts' => $outgoingCounts,
            'totalPosts' => $allPosts->count(),
            'orphanCount' => $orphanCount,
        ];
    }
}

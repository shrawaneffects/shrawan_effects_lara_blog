<?php

namespace App\Services\Seo;

use App\Models\Post;
use App\Models\PostSeo;
use Illuminate\Support\Str;

class SeoAuditService
{
    /**
     * Perform a complete SEO audit on a post (or preview data from article editor)
     */
    public static function audit(array $data, ?int $postId = null): array
    {
        $title = trim($data['title'] ?? '');
        $slug = trim($data['slug'] ?? Str::slug($title));
        $content = $data['content'] ?? '';
        $excerpt = $data['excerpt'] ?? '';
        $seoTitle = trim($data['seo_title'] ?? $title);
        $metaDescription = trim($data['meta_description'] ?? $excerpt);
        $primaryKeyword = trim(Str::lower($data['primary_keyword'] ?? ''));
        $searchIntent = $data['search_intent'] ?? 'informational';
        $canonicalUrl = trim($data['canonical_url'] ?? '');
        $robotsIndex = filter_var($data['robots_index'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $robotsFollow = filter_var($data['robots_follow'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $includeInSitemap = filter_var($data['include_in_sitemap'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $ogTitle = $data['og_title'] ?? '';
        $ogDescription = $data['og_description'] ?? '';
        $ogImage = $data['og_image'] ?? ($data['featured_image'] ?? '');
        $twitterCard = $data['twitter_card'] ?? 'summary_large_image';
        $schemaType = $data['schema_type'] ?? 'BlogPosting';

        $plainContent = strip_tags($content);
        $lowerContent = Str::lower($plainContent);
        $wordCount = str_word_count($plainContent);

        $results = [
            'technical' => [],
            'content' => [],
            'on_page' => [],
            'internal_linking' => [],
            'image' => [],
            'structured_data' => [],
            'social' => [],
        ];

        // ==========================================
        // 1. TECHNICAL SEO (25 Points)
        // ==========================================
        
        // Check 1.1: Robots Indexability
        if (!$robotsIndex) {
            $results['technical'][] = [
                'rule' => 'robots_noindex',
                'severity' => 'warning',
                'deduction' => 10,
                'passed' => false,
                'message' => 'Article is configured as "noindex".',
                'recommendation' => 'Search engines are instructed not to index this page. If this is intentional (e.g. private draft/staging), keep it. Otherwise, set robots to "Index".',
            ];
        } else {
            $results['technical'][] = [
                'rule' => 'robots_indexable',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => 'Article is set to index & follow for search crawlers.',
                'recommendation' => 'Good technical configuration.',
            ];
        }

        // Check 1.2: Sitemap Inclusion
        if (!$includeInSitemap && $robotsIndex) {
            $results['technical'][] = [
                'rule' => 'sitemap_excluded',
                'severity' => 'warning',
                'deduction' => 5,
                'passed' => false,
                'message' => 'Article is excluded from the dynamic XML sitemap.',
                'recommendation' => 'Include indexable public articles in the XML sitemap for faster discovery.',
            ];
        } else {
            $results['technical'][] = [
                'rule' => 'sitemap_included',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => 'Included in XML sitemap.',
                'recommendation' => 'Ensures search engine bots discover article updates.',
            ];
        }

        // Check 1.3: Canonical URL Validity
        if (!empty($canonicalUrl)) {
            if (!filter_var($canonicalUrl, FILTER_VALIDATE_URL) || !str_starts_with($canonicalUrl, 'http')) {
                $results['technical'][] = [
                    'rule' => 'canonical_invalid',
                    'severity' => 'critical',
                    'deduction' => 10,
                    'passed' => false,
                    'message' => 'Custom Canonical URL is not a valid absolute URL.',
                    'recommendation' => 'Provide a valid HTTPS canonical URL or leave blank to use the article default URL.',
                ];
            } else {
                $results['technical'][] = [
                    'rule' => 'canonical_custom_valid',
                    'severity' => 'info',
                    'deduction' => 0,
                    'passed' => true,
                    'message' => 'Valid custom canonical URL specified.',
                    'recommendation' => 'Canonical signal is well formatted.',
                ];
            }
        } else {
            $results['technical'][] = [
                'rule' => 'canonical_default',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => 'Self-referencing canonical URL will be generated automatically.',
                'recommendation' => 'Standard best practice for original content.',
            ];
        }

        // Check 1.4: URL Slug Hygiene
        if (empty($slug)) {
            $results['technical'][] = [
                'rule' => 'slug_missing',
                'severity' => 'critical',
                'deduction' => 10,
                'passed' => false,
                'message' => 'URL slug is missing.',
                'recommendation' => 'Add a descriptive, hyphen-separated URL slug.',
            ];
        } elseif (strlen($slug) > 80) {
            $results['technical'][] = [
                'rule' => 'slug_too_long',
                'severity' => 'warning',
                'deduction' => 3,
                'passed' => false,
                'message' => 'URL slug is relatively long (' . strlen($slug) . ' characters).',
                'recommendation' => 'Consider shortening the slug to 3-5 concise, meaningful words.',
            ];
        } elseif (preg_match('/[^a-z0-9\-]/', $slug)) {
            $results['technical'][] = [
                'rule' => 'slug_special_characters',
                'severity' => 'warning',
                'deduction' => 4,
                'passed' => false,
                'message' => 'Slug contains uppercase or special characters.',
                'recommendation' => 'Use only lowercase letters, numbers, and hyphens.',
            ];
        } else {
            $results['technical'][] = [
                'rule' => 'slug_healthy',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => 'URL slug is clean and descriptive.',
                'recommendation' => 'Clean URL structure facilitates indexing and user click-through.',
            ];
        }

        // ==========================================
        // 2. CONTENT SEO & HEADING HIERARCHY (25 Points)
        // ==========================================

        // Check 2.1: Content Length & Breadth
        if ($wordCount < 100) {
            $results['content'][] = [
                'rule' => 'content_too_short',
                'severity' => 'critical',
                'deduction' => 12,
                'passed' => false,
                'message' => "Content is very brief ({$wordCount} words).",
                'recommendation' => 'The article may need more depth and contextual detail to fully satisfy user search queries.',
            ];
        } elseif ($wordCount < 300) {
            $results['content'][] = [
                'rule' => 'content_thin',
                'severity' => 'warning',
                'deduction' => 5,
                'passed' => false,
                'message' => "Content is relatively short ({$wordCount} words).",
                'recommendation' => 'Consider expanding with examples, step-by-step guidance, or background context.',
            ];
        } else {
            $results['content'][] = [
                'rule' => 'content_depth_good',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => "Good article depth ({$wordCount} words).",
                'recommendation' => 'Substantial content allows thorough coverage of the topic.',
            ];
        }

        // Check 2.2: Heading Structure & Hierarchy
        preg_match_all('/<h([1-6])(?:\s+[^>]*)?>(.*?)<\/h\1>/is', $content, $headingMatches, PREG_SET_ORDER);
        $headings = [];
        $hasH1InContent = false;
        $skippedHierarchy = false;
        $lastLevel = 1;

        foreach ($headingMatches as $hm) {
            $lvl = (int) $hm[1];
            $hText = trim(strip_tags($hm[2]));
            if ($lvl === 1) $hasH1InContent = true;
            
            // Detect skipped levels like H2 directly to H4 without H3
            if ($lvl > $lastLevel + 1 && $lastLevel > 1) {
                $skippedHierarchy = true;
            }
            $lastLevel = $lvl;
            $headings[] = ['level' => $lvl, 'text' => $hText];
        }

        if (count($headings) === 0 && $wordCount > 250) {
            $results['content'][] = [
                'rule' => 'headings_missing',
                'severity' => 'warning',
                'deduction' => 6,
                'passed' => false,
                'message' => 'No sub-headings (H2, H3) detected in the article body.',
                'recommendation' => 'Break up long text with informative H2/H3 sub-headings to improve scannability and structural clarity.',
            ];
        } elseif ($skippedHierarchy) {
            $results['content'][] = [
                'rule' => 'heading_hierarchy_skipped',
                'severity' => 'warning',
                'deduction' => 4,
                'passed' => false,
                'message' => 'Heading hierarchy skips levels (e.g. H2 followed directly by H4 without an H3).',
                'recommendation' => 'Maintain logical heading hierarchy for screen readers and search engines.',
            ];
        } else {
            $results['content'][] = [
                'rule' => 'heading_hierarchy_good',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => 'Subheadings are well structured (' . count($headings) . ' subheadings found).',
                'recommendation' => 'Good content organization.',
            ];
        }

        // Check 2.3: Search Intent Matching Cues
        $intentWarnings = self::evaluateSearchIntent($searchIntent, $lowerContent, $headings);
        if (!empty($intentWarnings)) {
            $results['content'][] = [
                'rule' => 'search_intent_mismatch',
                'severity' => 'warning',
                'deduction' => 6,
                'passed' => false,
                'message' => $intentWarnings['message'],
                'recommendation' => $intentWarnings['recommendation'],
            ];
        } else {
            $results['content'][] = [
                'rule' => 'search_intent_aligned',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => "Content structure aligns well with selected " . ucfirst($searchIntent) . " search intent.",
                'recommendation' => 'Appropriate intent formatting.',
            ];
        }

        // ==========================================
        // 3. ON-PAGE SEO & KEYWORD PLACEMENT (20 Points)
        // ==========================================

        // Check 3.1: SEO Title
        $titleLen = mb_strlen($seoTitle);
        if (empty($seoTitle)) {
            $results['on_page'][] = [
                'rule' => 'seo_title_missing',
                'severity' => 'critical',
                'deduction' => 8,
                'passed' => false,
                'message' => 'SEO Title is missing.',
                'recommendation' => 'Add a descriptive SEO title for search engine snippets.',
            ];
        } elseif ($titleLen > 70) {
            $results['on_page'][] = [
                'rule' => 'seo_title_long',
                'severity' => 'warning',
                'deduction' => 3,
                'passed' => false,
                'message' => "SEO Title is long ({$titleLen} characters, ~" . ($titleLen * 9) . "px).",
                'recommendation' => 'Titles over 60-65 characters may be truncated in Google desktop/mobile SERP previews.',
            ];
        } elseif ($titleLen < 25) {
            $results['on_page'][] = [
                'rule' => 'seo_title_short',
                'severity' => 'warning',
                'deduction' => 2,
                'passed' => false,
                'message' => "SEO Title is short ({$titleLen} characters).",
                'recommendation' => 'Consider providing more descriptive detail to maximize click-through rate.',
            ];
        } else {
            $results['on_page'][] = [
                'rule' => 'seo_title_optimal',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => "SEO Title is well-proportioned ({$titleLen} characters).",
                'recommendation' => 'Fits comfortably within typical Google SERP display width.',
            ];
        }

        // Check 3.2: Meta Description
        $descLen = mb_strlen($metaDescription);
        if (empty($metaDescription)) {
            $results['on_page'][] = [
                'rule' => 'meta_description_missing',
                'severity' => 'warning',
                'deduction' => 5,
                'passed' => false,
                'message' => 'Meta description is missing.',
                'recommendation' => 'Add an engaging meta description to summarize the article for search results.',
            ];
        } elseif ($descLen > 165) {
            $results['on_page'][] = [
                'rule' => 'meta_description_long',
                'severity' => 'warning',
                'deduction' => 2,
                'passed' => false,
                'message' => "Meta description is somewhat long ({$descLen} characters).",
                'recommendation' => 'Descriptions above ~155-160 characters might be clipped on smaller mobile displays.',
            ];
        } elseif ($descLen < 50) {
            $results['on_page'][] = [
                'rule' => 'meta_description_short',
                'severity' => 'warning',
                'deduction' => 2,
                'passed' => false,
                'message' => "Meta description is brief ({$descLen} characters).",
                'recommendation' => 'Provide a compelling 1-2 sentence overview with value proposition for readers.',
            ];
        } else {
            $results['on_page'][] = [
                'rule' => 'meta_description_optimal',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => "Meta description has good length ({$descLen} characters).",
                'recommendation' => 'Effective length for search result snippets.',
            ];
        }

        // Check 3.3: Primary Keyword Natural Placement
        if (!empty($primaryKeyword)) {
            $inTitle = str_contains(Str::lower($seoTitle), $primaryKeyword);
            $inSlug = str_contains(Str::lower($slug), Str::slug($primaryKeyword));
            $inContent = str_contains($lowerContent, $primaryKeyword);
            $introText = Str::lower(Str::limit($plainContent, 400));
            $inIntro = str_contains($introText, $primaryKeyword);

            if (!$inTitle && !$inSlug) {
                $results['on_page'][] = [
                    'rule' => 'keyword_missing_title_slug',
                    'severity' => 'warning',
                    'deduction' => 4,
                    'passed' => false,
                    'message' => "Primary keyword '{$primaryKeyword}' was not found in SEO Title or URL Slug.",
                    'recommendation' => 'Including the core topic naturally in title or slug helps users immediately confirm relevance.',
                ];
            } else {
                $results['on_page'][] = [
                    'rule' => 'keyword_in_title_or_slug',
                    'severity' => 'info',
                    'deduction' => 0,
                    'passed' => true,
                    'message' => "Primary keyword is naturally represented in title/slug.",
                    'recommendation' => 'Clear thematic signal for readers and search engines.',
                ];
            }

            if (!$inContent) {
                $results['on_page'][] = [
                    'rule' => 'keyword_missing_content',
                    'severity' => 'warning',
                    'deduction' => 4,
                    'passed' => false,
                    'message' => "Primary keyword '{$primaryKeyword}' does not appear in the body text.",
                    'recommendation' => 'Ensure the primary subject is naturally discussed within the article.',
                ];
            }
        }

        // Check 3.4: Duplicate Meta Check against other articles
        $duplicates = self::findDuplicateMetadata($seoTitle, $metaDescription, $postId);
        if (!empty($duplicates['duplicate_title'])) {
            $results['on_page'][] = [
                'rule' => 'duplicate_title_found',
                'severity' => 'critical',
                'deduction' => 6,
                'passed' => false,
                'message' => 'SEO Title matches an existing article: "' . $duplicates['duplicate_title']->title . '"',
                'recommendation' => 'Create a unique title to avoid duplicate title tags and keyword cannibalization.',
            ];
        }

        // ==========================================
        // 4. INTERNAL LINKING (10 Points)
        // ==========================================
        preg_match_all('/<a\s+[^>]*href=["\']([^"\']+)["\']/i', $content, $linkMatches);
        $links = $linkMatches[1] ?? [];
        $internalLinksCount = 0;
        $externalLinksCount = 0;

        foreach ($links as $href) {
            if (str_starts_with($href, '/') || str_starts_with($href, '#') || str_contains($href, url('/'))) {
                $internalLinksCount++;
            } else {
                $externalLinksCount++;
            }
        }

        if ($internalLinksCount === 0 && $wordCount > 250) {
            $results['internal_linking'][] = [
                'rule' => 'no_internal_links',
                'severity' => 'warning',
                'deduction' => 5,
                'passed' => false,
                'message' => 'No outgoing internal links found in the article body.',
                'recommendation' => 'Link to 2-3 relevant articles or related guides on your site to distribute PageRank and keep readers engaged.',
            ];
        } else {
            $results['internal_linking'][] = [
                'rule' => 'internal_links_present',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => "{$internalLinksCount} internal link(s) found in content.",
                'recommendation' => 'Good internal site connectivity.',
            ];
        }

        // Check orphan status if post exists in DB
        if ($postId) {
            $postModel = Post::find($postId);
            if ($postModel && $postModel->incoming_links_count === 0 && $postModel->status === 'published') {
                $results['internal_linking'][] = [
                    'rule' => 'orphan_article',
                    'severity' => 'warning',
                    'deduction' => 5,
                    'passed' => false,
                    'message' => 'This article is currently an Orphan Page (0 incoming internal links from other blog posts).',
                    'recommendation' => 'Add links pointing to this article from other relevant published posts in the same category.',
                ];
            }
        }

        // ==========================================
        // 5. IMAGE SEO (10 Points)
        // ==========================================
        preg_match_all('/<img\s+[^>]*>/i', $content, $imgTags);
        $totalImagesInContent = count($imgTags[0] ?? []);
        $missingAltCount = 0;

        foreach ($imgTags[0] ?? [] as $imgTag) {
            if (!preg_match('/alt=[\'"][^\'"]+[\'"]/i', $imgTag)) {
                $missingAltCount++;
            }
        }

        if ($missingAltCount > 0) {
            $results['image'][] = [
                'rule' => 'images_missing_alt',
                'severity' => 'warning',
                'deduction' => min(6, $missingAltCount * 2),
                'passed' => false,
                'message' => "{$missingAltCount} image(s) in content are missing descriptive alt text.",
                'recommendation' => 'Add concise alt text describing the image content for accessibility and image search.',
            ];
        } else {
            $results['image'][] = [
                'rule' => 'images_alt_complete',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => ($totalImagesInContent > 0) ? 'All embedded images have alt attributes.' : 'No missing alt tags.',
                'recommendation' => 'Accessible image formatting.',
            ];
        }

        if (empty($data['featured_image']) && empty($data['image_url']) && (!isset($data['has_featured_image']) || !$data['has_featured_image'])) {
            $results['image'][] = [
                'rule' => 'featured_image_missing',
                'severity' => 'warning',
                'deduction' => 4,
                'passed' => false,
                'message' => 'No featured image set for this article.',
                'recommendation' => 'Add a high-quality featured cover image for social cards and rich search previews.',
            ];
        } else {
            $results['image'][] = [
                'rule' => 'featured_image_present',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => 'Featured cover image is configured.',
                'recommendation' => 'Enhances social shares and OpenGraph presentation.',
            ];
        }

        // ==========================================
        // 6. STRUCTURED DATA (5 Points)
        // ==========================================
        if (!in_array($schemaType, ['BlogPosting', 'Article', 'TechArticle', 'NewsArticle'])) {
            $results['structured_data'][] = [
                'rule' => 'schema_type_unknown',
                'severity' => 'warning',
                'deduction' => 2,
                'passed' => false,
                'message' => "Unrecognized Schema.org type '{$schemaType}'.",
                'recommendation' => 'Choose a standard Schema type such as BlogPosting or TechArticle.',
            ];
        } else {
            $results['structured_data'][] = [
                'rule' => 'schema_valid',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => "Configured for '{$schemaType}' JSON-LD structured data.",
                'recommendation' => 'Valid schema enhances Google rich results and author recognition.',
            ];
        }

        // ==========================================
        // 7. SOCIAL METADATA (5 Points)
        // ==========================================
        $hasSocialMeta = !empty($ogTitle) || !empty($ogDescription) || !empty($seoTitle);
        if ($hasSocialMeta) {
            $results['social'][] = [
                'rule' => 'social_meta_configured',
                'severity' => 'info',
                'deduction' => 0,
                'passed' => true,
                'message' => "Open Graph and Twitter/X metadata are configured ({$twitterCard}).",
                'recommendation' => 'Ensures rich link previews when shared across social channels.',
            ];
        } else {
            $results['social'][] = [
                'rule' => 'social_meta_missing',
                'severity' => 'warning',
                'deduction' => 3,
                'passed' => false,
                'message' => 'Social metadata could be optimized.',
                'recommendation' => 'Provide a custom OpenGraph title, description, and image.',
            ];
        }

        // Compute 100-point transparent score
        $scoreResult = SeoScoreService::calculate($results);

        // Check for keyword cannibalization
        $cannibalization = self::checkKeywordCannibalization($primaryKeyword, $postId);

        return array_merge($scoreResult, [
            'serp_preview' => [
                'url' => url('/blog/' . ($slug ?: 'example-slug')),
                'title' => $seoTitle ?: 'Article Title',
                'description' => $metaDescription ?: 'A captivating summary of your article will appear here in search engine results.',
                'title_length' => $titleLen,
                'description_length' => $descLen,
            ],
            'keyword_cannibalization' => $cannibalization,
        ]);
    }

    /**
     * Search Intent Evaluation
     */
    protected static function evaluateSearchIntent(string $intent, string $lowerContent, array $headings): ?array
    {
        $allText = $lowerContent;
        foreach ($headings as $h) {
            $allText .= ' ' . Str::lower($h['text']);
        }

        if ($intent === 'commercial') {
            $commercialKeywords = ['vs', 'versus', 'best', 'top', 'review', 'pricing', 'cost', 'compare', 'comparison', 'pros and cons', 'alternatives', 'features', 'worth it'];
            $matched = 0;
            foreach ($commercialKeywords as $ck) {
                if (str_contains($allText, $ck)) $matched++;
            }

            if ($matched === 0) {
                return [
                    'message' => 'Commercial search intent selected, but content lacks comparison or evaluation terms.',
                    'recommendation' => 'Articles targeting Commercial intent typically include product comparisons, pricing analysis, pros/cons, or buying criteria.',
                ];
            }
        } elseif ($intent === 'transactional') {
            $transactionalKeywords = ['buy', 'purchase', 'discount', 'coupon', 'deal', 'get started', 'order', 'download', 'signup', 'register', 'subscription', 'plans'];
            $matched = 0;
            foreach ($transactionalKeywords as $tk) {
                if (str_contains($allText, $tk)) $matched++;
            }

            if ($matched === 0) {
                return [
                    'message' => 'Transactional intent selected, but call-to-action or purchase guidance is missing.',
                    'recommendation' => 'Ensure the article offers clear actionable steps, download links, or signup guidance.',
                ];
            }
        }

        return null;
    }

    /**
     * Find duplicate SEO titles or meta descriptions across other posts
     */
    public static function findDuplicateMetadata(string $seoTitle, string $metaDescription, ?int $excludePostId = null): array
    {
        $res = [
            'duplicate_title' => null,
            'duplicate_description' => null,
        ];

        if (!empty($seoTitle)) {
            $dupPost = Post::where(function ($q) use ($seoTitle) {
                $q->where('meta_title', $seoTitle)
                  ->orWhere('title', $seoTitle);
            });
            if ($excludePostId) {
                $dupPost->where('id', '!=', $excludePostId);
            }
            $res['duplicate_title'] = $dupPost->first();
        }

        if (!empty($metaDescription)) {
            $dupDesc = Post::where('meta_description', $metaDescription);
            if ($excludePostId) {
                $dupDesc->where('id', '!=', $excludePostId);
            }
            $res['duplicate_description'] = $dupDesc->first();
        }

        return $res;
    }

    /**
     * Check if another post already targets the exact same primary keyword
     */
    public static function checkKeywordCannibalization(?string $primaryKeyword, ?int $excludePostId = null): ?array
    {
        if (empty($primaryKeyword)) {
            return null;
        }

        $query = PostSeo::where('primary_keyword', $primaryKeyword)->with('post');
        if ($excludePostId) {
            $query->where('post_id', '!=', $excludePostId);
        }

        $conflicts = $query->get();

        if ($conflicts->count() > 0) {
            $items = [];
            foreach ($conflicts as $c) {
                if ($c->post) {
                    $items[] = [
                        'id' => $c->post->id,
                        'title' => $c->post->title,
                        'slug' => $c->post->slug,
                        'url' => route('blog.show', $c->post->slug),
                    ];
                }
            }

            return [
                'has_cannibalization' => true,
                'keyword' => $primaryKeyword,
                'conflicting_posts' => $items,
                'message' => "Potential Keyword Cannibalization: {$conflicts->count()} other article(s) are targeting the exact keyword '{$primaryKeyword}'.",
                'recommendation' => "Consider differentiating keywords or consolidating articles to avoid competing against yourself in search rankings.",
            ];
        }

        return null;
    }
}

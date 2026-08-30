<?php

namespace App\Services\Seo;

use App\Models\Post;
use App\Models\Page;
use Illuminate\Support\Str;

class SeoLinkGraderService
{
    protected static array $genericAnchors = [
        'click here', 'click', 'read more', 'read this', 'link', 'this link',
        'here', 'source', 'website', 'page', 'learn more', 'check here',
        'more', 'this article', 'find out more', 'go here', 'visit'
    ];

    /**
     * Parse and deeply grade all links inside HTML content
     */
    public static function grade(string $content, int $wordCount = 0, ?int $currentPostId = null): array
    {
        $links = [];
        $siteUrl = url('/');
        $siteHost = parse_url($siteUrl, PHP_URL_HOST);

        preg_match_all('/<a\s+([^>]*?)>(.*?)<\/a>/is', $content, $matches, PREG_SET_ORDER);

        $internalDofollow = 0;
        $internalNofollow = 0;
        $externalDofollow = 0;
        $externalNofollow = 0;
        $externalSponsored = 0;
        $externalUgc = 0;
        $genericAnchorCount = 0;
        $securityWarnings = 0;
        $internalNofollowWarnings = 0;
        $brokenInternalCount = 0;

        foreach ($matches as $match) {
            $attrs = $match[1];
            $rawAnchor = $match[2];

            // Extract href
            preg_match('/href=["\']([^"\']*)["\']/i', $attrs, $hrefMatch);
            $href = trim($hrefMatch[1] ?? '');

            if (empty($href) || str_starts_with($href, '#') || str_starts_with($href, 'javascript:')) {
                continue;
            }

            // Extract rel
            preg_match('/rel=["\']([^"\']*)["\']/i', $attrs, $relMatch);
            $rel = strtolower(trim($relMatch[1] ?? ''));

            // Extract target
            preg_match('/target=["\']([^"\']*)["\']/i', $attrs, $targetMatch);
            $target = strtolower(trim($targetMatch[1] ?? '_self'));

            // Clean anchor text
            $cleanAnchor = trim(strip_tags($rawAnchor));
            if (empty($cleanAnchor)) {
                // Check if contains img with alt
                if (preg_match('/<img[^>]+alt=["\']([^"\']+)["\']/i', $rawAnchor, $altMatch)) {
                    $cleanAnchor = '[Image Alt: ' . trim($altMatch[1]) . ']';
                } else {
                    $cleanAnchor = '[Empty Anchor Text]';
                }
            }

            // Classify Internal vs External
            $isInternal = false;
            $parsedHref = parse_url($href);
            $hrefHost = $parsedHref['host'] ?? null;

            if (empty($hrefHost) || str_starts_with($href, '/') || ($siteHost && $hrefHost === $siteHost)) {
                $isInternal = true;
            }

            // Check follow / nofollow flags
            $isNofollow = str_contains($rel, 'nofollow');
            $isSponsored = str_contains($rel, 'sponsored');
            $isUgc = str_contains($rel, 'ugc');
            $isDofollow = !$isNofollow && !$isSponsored && !$isUgc;

            $hasNoopener = str_contains($rel, 'noopener');
            $hasNoreferrer = str_contains($rel, 'noreferrer');

            $isGeneric = in_array(strtolower($cleanAnchor), self::$genericAnchors);
            if ($isGeneric) {
                $genericAnchorCount++;
            }

            $issues = [];

            if ($isInternal) {
                if ($isDofollow) {
                    $internalDofollow++;
                } else {
                    $internalNofollow++;
                    $internalNofollowWarnings++;
                    $issues[] = [
                        'type' => 'internal_nofollow',
                        'severity' => 'warning',
                        'message' => 'Internal link has rel="nofollow". Avoid nofollowing internal links as it leaks PageRank.',
                    ];
                }

                // Check internal target validity (exclude media assets)
                if (!\App\Services\Media\MediaAttributeEnricherService::isMediaUrl($href)) {
                    $slug = ltrim($parsedHref['path'] ?? $href, '/');
                    if (str_starts_with($slug, 'blog/')) {
                        $postSlug = str_replace('blog/', '', $slug);
                        $exists = Post::where('slug', $postSlug)->exists();
                        if (!$exists) {
                            $brokenInternalCount++;
                            $issues[] = [
                                'type' => 'broken_internal',
                                'severity' => 'critical',
                                'message' => "Internal destination '/blog/{$postSlug}' does not match any published article.",
                            ];
                        }
                    }
                }
            } else {
                if ($isSponsored) {
                    $externalSponsored++;
                } elseif ($isUgc) {
                    $externalUgc++;
                } elseif ($isNofollow) {
                    $externalNofollow++;
                } else {
                    $externalDofollow++;
                }

                // Check security on blank targets
                if ($target === '_blank' && !$hasNoopener && !$hasNoreferrer) {
                    $securityWarnings++;
                    $issues[] = [
                        'type' => 'target_blank_security',
                        'severity' => 'warning',
                        'message' => 'External target="_blank" is missing rel="noopener" (Security vulnerability).',
                    ];
                }
            }

            if ($isGeneric) {
                $issues[] = [
                    'type' => 'generic_anchor',
                    'severity' => 'info',
                    'message' => "Generic anchor text '{$cleanAnchor}'. Use descriptive keyword-rich phrasing instead.",
                ];
            }

            $links[] = [
                'href' => $href,
                'anchor' => $cleanAnchor,
                'type' => $isInternal ? 'internal' : 'external',
                'rel' => $rel ?: 'dofollow',
                'target' => $target,
                'is_dofollow' => $isDofollow,
                'is_nofollow' => $isNofollow,
                'is_sponsored' => $isSponsored,
                'is_ugc' => $isUgc,
                'is_generic' => $isGeneric,
                'has_noopener' => $hasNoopener,
                'issues' => $issues,
            ];
        }

        $totalLinks = count($links);
        $totalInternal = $internalDofollow + $internalNofollow;
        $totalExternal = $externalDofollow + $externalNofollow + $externalSponsored + $externalUgc;

        // Density: links per 100 words
        $density = $wordCount > 0 ? round(($totalLinks / ($wordCount / 100)), 1) : 0;

        // Grade Score Calculation for Linking (out of 100)
        $linkScore = 100;

        if ($wordCount > 250 && $totalInternal === 0) {
            $linkScore -= 30; // Critical: No internal links
        } elseif ($totalInternal < 2 && $wordCount > 400) {
            $linkScore -= 10;
        }

        if ($internalNofollowWarnings > 0) {
            $linkScore -= ($internalNofollowWarnings * 8);
        }

        if ($brokenInternalCount > 0) {
            $linkScore -= ($brokenInternalCount * 15);
        }

        if ($genericAnchorCount > 0) {
            $linkScore -= min(15, $genericAnchorCount * 5);
        }

        if ($securityWarnings > 0) {
            $linkScore -= min(10, $securityWarnings * 3);
        }

        if ($density > 6.0) {
            $linkScore -= 15; // Overlinked content
        }

        $linkScore = max(0, min(100, $linkScore));

        return [
            'total_links' => $totalLinks,
            'internal' => [
                'total' => $totalInternal,
                'dofollow' => $internalDofollow,
                'nofollow' => $internalNofollow,
                'broken' => $brokenInternalCount,
            ],
            'external' => [
                'total' => $totalExternal,
                'dofollow' => $externalDofollow,
                'nofollow' => $externalNofollow,
                'sponsored' => $externalSponsored,
                'ugc' => $externalUgc,
            ],
            'density' => $density,
            'generic_anchors_count' => $genericAnchorCount,
            'security_warnings_count' => $securityWarnings,
            'internal_nofollow_warnings' => $internalNofollowWarnings,
            'link_score' => $linkScore,
            'links' => $links,
        ];
    }
}

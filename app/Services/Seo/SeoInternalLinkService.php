<?php

namespace App\Services\Seo;

use App\Models\Post;
use Illuminate\Support\Str;

class SeoInternalLinkService
{
    /**
     * Generate relevant internal link suggestions for an article
     */
    public static function getSuggestions(string $title, string $content, ?int $categoryId = null, ?int $currentPostId = null, int $limit = 5): array
    {
        $words = array_filter(explode(' ', Str::lower(preg_replace('/[^a-zA-Z0-9\s]/', '', $title))), fn($w) => strlen($w) > 3);
        $contentSample = Str::lower(Str::limit(strip_tags($content), 2000));

        $query = Post::published()
            ->with(['category', 'seo']);

        if ($currentPostId) {
            $query->where('id', '!=', $currentPostId);
        }

        $allPosts = $query->take(50)->get();
        $suggestions = [];

        foreach ($allPosts as $otherPost) {
            // Check if otherPost is already linked in current content
            if (str_contains($content, $otherPost->slug) || str_contains($content, '/blog/' . $otherPost->slug)) {
                continue;
            }

            $score = 0;
            $reasons = [];

            // 1. Same category bonus
            if ($categoryId && $otherPost->category_id === $categoryId) {
                $score += 30;
                $reasons[] = 'Same category (' . ($otherPost->category ? $otherPost->category->name : '') . ')';
            }

            // 2. Title word overlap
            $otherWords = array_filter(explode(' ', Str::lower(preg_replace('/[^a-zA-Z0-9\s]/', '', $otherPost->title))), fn($w) => strlen($w) > 3);
            $overlap = array_intersect($words, $otherWords);
            if (count($overlap) > 0) {
                $score += count($overlap) * 15;
                $reasons[] = 'Topical keyword overlap: ' . implode(', ', array_slice($overlap, 0, 3));
            }

            // 3. Primary keyword match
            if ($otherPost->seo && !empty($otherPost->seo->primary_keyword)) {
                if (str_contains($contentSample, Str::lower($otherPost->seo->primary_keyword))) {
                    $score += 25;
                    $reasons[] = "Target keyword '{$otherPost->seo->primary_keyword}' mentioned in text";
                }
            }

            if ($score > 0) {
                $anchorText = Str::lower($otherPost->title);
                $snippet = '<a href="' . route('blog.show', $otherPost->slug) . '">' . e($otherPost->title) . '</a>';

                $suggestions[] = [
                    'id' => $otherPost->id,
                    'title' => $otherPost->title,
                    'slug' => $otherPost->slug,
                    'url' => route('blog.show', $otherPost->slug),
                    'category' => $otherPost->category ? $otherPost->category->name : 'General',
                    'score' => $score,
                    'reasons' => $reasons,
                    'suggested_anchor' => $anchorText,
                    'html_snippet' => $snippet,
                ];
            }
        }

        // Sort by relevance score desc
        usort($suggestions, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($suggestions, 0, $limit);
    }
}

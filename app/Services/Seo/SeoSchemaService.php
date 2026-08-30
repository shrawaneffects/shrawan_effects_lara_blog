<?php

namespace App\Services\Seo;

use App\Models\Post;
use App\Models\Setting;

class SeoSchemaService
{
    /**
     * Generate complete Schema.org JSON-LD scripts for a post
     */
    public static function generate(Post $post): string
    {
        $siteName = Setting::get('site_name', 'LaravelBlog');
        $siteLogo = Setting::getLogoUrl() ?? asset('storage/default.jpg');
        $seo = $post->seo_data;

        $schemaType = $seo->schema_type ?: ($post->schema_type ?: 'BlogPosting');

        // 1. Main Article Schema
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => $schemaType,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('blog.show', $post->slug),
            ],
            'headline' => $seo->effective_title,
            'description' => $seo->effective_description,
            'image' => [
                $post->image_url,
            ],
            'datePublished' => $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String(),
            'dateModified' => $post->updated_at ? $post->updated_at->toIso8601String() : $post->created_at->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author ? $post->author->name : 'Editorial Staff',
                'url' => $post->author ? route('blog.author', $post->author->id) : route('home'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $siteLogo,
                ],
            ],
            'articleSection' => $post->category ? $post->category->name : 'General',
            'wordCount' => str_word_count(strip_tags($post->content ?? '')),
            'timeRequired' => 'PT' . ($post->reading_time ?: 1) . 'M',
        ];

        if (!empty($seo->primary_keyword)) {
            $articleSchema['keywords'] = $seo->primary_keyword;
        }

        $scripts = '<script type="application/ld+json">' . "\n" . json_encode($articleSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n" . '</script>';

        // 2. BreadcrumbList Schema
        $breadcrumbElements = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => route('home'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Articles',
                'item' => route('blog.index'),
            ],
        ];

        $pos = 3;
        if ($post->category) {
            $breadcrumbElements[] = [
                '@type' => 'ListItem',
                'position' => $pos++,
                'name' => $post->category->name,
                'item' => route('blog.category', $post->category->slug),
            ];
        }

        $breadcrumbElements[] = [
            '@type' => 'ListItem',
            'position' => $pos,
            'name' => $post->title,
            'item' => route('blog.show', $post->slug),
        ];

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbElements,
        ];

        $scripts .= "\n" . '<script type="application/ld+json">' . "\n" . json_encode($breadcrumbSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n" . '</script>';

        // 3. Optional FAQPage Schema if article has FAQs
        if (!empty($post->faqs) && is_array($post->faqs)) {
            $validFaqs = array_filter($post->faqs, function ($item) {
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
}

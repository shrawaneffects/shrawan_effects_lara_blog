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

        $imageUrl = $post->image_url;

        // Build ImageObject variants for SERP thumbnail eligibility.
        // Google requires at least 1200×675px (16:9) for large SERP thumbnails.
        // Providing multiple aspect-ratio variants (16:9, 4:3, 1:1) gives Google
        // maximum flexibility to pick the best-fit thumbnail.
        $imageObject16x9 = [
            '@type'  => 'ImageObject',
            'url'    => $imageUrl,
            'width'  => 1200,
            'height' => 675,
        ];
        $imageObject4x3 = [
            '@type'  => 'ImageObject',
            'url'    => $imageUrl,
            'width'  => 1200,
            'height' => 900,
        ];
        $imageObject1x1 = [
            '@type'  => 'ImageObject',
            'url'    => $imageUrl,
            'width'  => 1200,
            'height' => 1200,
        ];

        // 1. Main Article Schema (Structured for Google Rich Snippets & SERP Image Thumbnails)
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => $schemaType,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('blog.show', $post->slug),
                'primaryImageOfPage' => $imageObject16x9,
            ],
            'headline' => $seo->effective_title,
            'description' => $seo->effective_description,
            // Google picks from multiple aspect-ratio variants — 16:9 is preferred for SERP thumbnails
            'image' => [
                $imageObject16x9,
                $imageObject4x3,
                $imageObject1x1,
            ],
            'thumbnailUrl' => $imageUrl,
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

    /**
     * Generate complete Schema.org JSON-LD Knowledge Graph for Homepage & Agentic AI Crawlers
     */
    public static function generateHomepageSchema(): string
    {
        $siteName = Setting::get('site_name', 'Shrawan Effects');
        $siteLogo = Setting::getLogoUrl() ?? asset('storage/default.jpg');
        $siteUrl = rtrim(config('app.url', url('/')), '/');
        $twitterUrl = Setting::get('twitter_url', 'https://twitter.com');
        $githubUrl = Setting::get('github_url', 'https://github.com');
        $linkedinUrl = Setting::get('linkedin_url', 'https://linkedin.com');
        $contactEmail = Setting::get('contact_email', 'shrawaneffects@gmail.com');

        $sameAs = array_values(array_filter([
            $twitterUrl,
            $githubUrl,
            $linkedinUrl,
        ]));

        $graph = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    '@id' => $siteUrl . '/#website',
                    'url' => $siteUrl . '/',
                    'name' => $siteName,
                    'alternateName' => 'Shrawan Effects Tech & AI Innovations',
                    'description' => 'Cutting-edge fullstack web development, Laravel 12 & React architecture, AI automation solutions, and expert technology insights.',
                    'inLanguage' => 'en-US',
                    'publisher' => [
                        '@id' => $siteUrl . '/#organization',
                    ],
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => [
                            '@type' => 'EntryPoint',
                            'urlTemplate' => $siteUrl . '/articles?search={search_term_string}',
                        ],
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
                [
                    '@type' => 'Organization',
                    '@id' => $siteUrl . '/#organization',
                    'name' => $siteName,
                    'url' => $siteUrl . '/',
                    'logo' => [
                        '@type' => 'ImageObject',
                        '@id' => $siteUrl . '/#logo',
                        'url' => $siteLogo,
                        'caption' => $siteName,
                    ],
                    'image' => [
                        '@id' => $siteUrl . '/#logo',
                    ],
                    'description' => 'A premier fullstack software engineering and AI automation platform delivering modern web architectures, OpenSEO intelligence, and enterprise technology publications.',
                    'email' => $contactEmail,
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => 'House No. 1049, Jeevan Nagar, Gounchhi',
                        'addressLocality' => 'Faridabad',
                        'addressRegion' => 'Haryana',
                        'postalCode' => '121004',
                        'addressCountry' => 'IN',
                    ],
                    'sameAs' => $sameAs,
                ],
                [
                    '@type' => 'ProfessionalService',
                    '@id' => $siteUrl . '/#service',
                    'name' => $siteName . ' Development & AI Solutions',
                    'url' => $siteUrl . '/',
                    'parentOrganization' => [
                        '@id' => $siteUrl . '/#organization',
                    ],
                    'priceRange' => '$$',
                    'areaServed' => 'Worldwide',
                    'knowsAbout' => [
                        'Fullstack Web Development',
                        'Laravel 12 Architecture',
                        'React & Vue Frontend Engineering',
                        'Artificial Intelligence & Agentic Workflows',
                        'Technical SEO & OpenSEO Suite',
                        'REST & GraphQL APIs',
                        'Performance Optimization & Core Web Vitals',
                    ],
                ],
            ],
        ];

        return '<script type="application/ld+json">' . "\n" . json_encode($graph, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n" . '</script>';
    }
}

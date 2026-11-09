<?php

namespace Database\Seeders;

use App\Models\OpenSeo\OpenSeoAiVisibility;
use App\Models\OpenSeo\OpenSeoAudit;
use App\Models\OpenSeo\OpenSeoBacklink;
use App\Models\OpenSeo\OpenSeoBacklinkSummary;
use App\Models\OpenSeo\OpenSeoCompetitor;
use App\Models\OpenSeo\OpenSeoKeyword;
use App\Models\OpenSeo\OpenSeoLocalTracker;
use App\Models\OpenSeo\OpenSeoProject;
use App\Models\OpenSeo\OpenSeoRankHistory;
use App\Models\OpenSeo\OpenSeoReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OpenSeoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?: User::first();

        // 1. Create or get default project
        $project = OpenSeoProject::firstOrCreate(
            ['domain' => 'shrawaneffects.com'],
            [
                'user_id' => $admin?->id,
                'name' => 'Shrawan Effects',
                'target_country' => 'IN',
                'target_language' => 'en',
                'gsc_property' => 'sc-domain:shrawaneffects.com',
                'is_default' => true,
            ]
        );

        // 2. Seed priority keywords
        $keywordsData = [
            ['kw' => 'Fullstack Website Developer in Faridabad', 'vol' => 2400, 'cpc' => 3.20, 'comp' => 0.45, 'kd' => 28, 'intent' => 'Commercial', 'rank' => 1, 'prev' => 2, 'best' => 1, 'change' => 1, 'url' => 'https://shrawaneffects.com/'],
            ['kw' => 'Software Development Services Faridabad', 'vol' => 1900, 'cpc' => 4.10, 'comp' => 0.52, 'kd' => 34, 'intent' => 'Commercial', 'rank' => 2, 'prev' => 3, 'best' => 2, 'change' => 1, 'url' => 'https://shrawaneffects.com/services'],
            ['kw' => 'Laravel Web Development Agency India', 'vol' => 3600, 'cpc' => 5.40, 'comp' => 0.65, 'kd' => 42, 'intent' => 'Transactional', 'rank' => 4, 'prev' => 6, 'best' => 3, 'change' => 2, 'url' => 'https://shrawaneffects.com/services'],
            ['kw' => 'Custom ERP and CRM Development', 'vol' => 2900, 'cpc' => 6.20, 'comp' => 0.58, 'kd' => 48, 'intent' => 'Commercial', 'rank' => 5, 'prev' => 4, 'best' => 4, 'change' => -1, 'url' => 'https://shrawaneffects.com/services'],
            ['kw' => 'Best Website Developer Near Me', 'vol' => 8800, 'cpc' => 2.80, 'comp' => 0.72, 'kd' => 38, 'intent' => 'Navigational', 'rank' => 3, 'prev' => 5, 'best' => 3, 'change' => 2, 'url' => 'https://shrawaneffects.com/contact'],
            ['kw' => 'Full Stack Application Architecture', 'vol' => 1400, 'cpc' => 3.90, 'comp' => 0.40, 'kd' => 25, 'intent' => 'Informational', 'rank' => 2, 'prev' => 2, 'best' => 1, 'change' => 0, 'url' => 'https://shrawaneffects.com/blog'],
            ['kw' => 'Shrawan Effects Faridabad', 'vol' => 850, 'cpc' => 1.50, 'comp' => 0.15, 'kd' => 8, 'intent' => 'Navigational', 'rank' => 1, 'prev' => 1, 'best' => 1, 'change' => 0, 'url' => 'https://shrawaneffects.com/'],
            ['kw' => 'Modern Cyberpunk Web Design Services', 'vol' => 1200, 'cpc' => 3.10, 'comp' => 0.35, 'kd' => 22, 'intent' => 'Commercial', 'rank' => 1, 'prev' => 3, 'best' => 1, 'change' => 2, 'url' => 'https://shrawaneffects.com/'],
        ];

        foreach ($keywordsData as $item) {
            $kwModel = OpenSeoKeyword::updateOrCreate(
                ['project_id' => $project->id, 'keyword' => $item['kw']],
                [
                    'search_volume' => $item['vol'],
                    'cpc' => $item['cpc'],
                    'competition' => $item['comp'],
                    'keyword_difficulty' => $item['kd'],
                    'search_intent' => $item['intent'],
                    'current_rank' => $item['rank'],
                    'previous_rank' => $item['prev'],
                    'best_rank' => $item['best'],
                    'rank_change' => $item['change'],
                    'target_url' => $item['url'],
                    'serp_features' => ['Featured Snippet', 'People Also Ask', 'Local Pack'],
                    'is_tracked' => true,
                    'tags' => 'core,services,branding',
                    'last_checked_at' => now(),
                ]
            );

            // Seed 7 days of rank history
            for ($d = 6; $d >= 0; $d--) {
                $date = Carbon::today()->subDays($d);
                $histRank = max(1, $item['rank'] + ($d > 3 ? 2 : 0));
                OpenSeoRankHistory::updateOrCreate(
                    ['keyword_id' => $kwModel->id, 'checked_date' => $date],
                    [
                        'rank' => $histRank,
                        'target_url' => $item['url'],
                        'serp_features' => ['Featured Snippet', 'People Also Ask'],
                    ]
                );
            }
        }

        // 3. Seed Competitors
        $competitorsData = [
            ['domain' => 'delhiwebstudios.com', 'traffic' => 28400, 'kws' => 1420, 'common' => 84, 'overlap' => 24.5],
            ['domain' => 'techsolutionsfbd.in', 'traffic' => 19200, 'kws' => 980, 'common' => 62, 'overlap' => 18.2],
            ['domain' => 'ncrsoftwarelabs.com', 'traffic' => 34500, 'kws' => 2100, 'common' => 115, 'overlap' => 31.0],
            ['domain' => 'haryanadigital.org', 'traffic' => 12800, 'kws' => 740, 'common' => 45, 'overlap' => 14.8],
        ];

        foreach ($competitorsData as $comp) {
            OpenSeoCompetitor::updateOrCreate(
                ['project_id' => $project->id, 'competitor_domain' => $comp['domain']],
                [
                    'organic_traffic' => $comp['traffic'],
                    'organic_keywords' => $comp['kws'],
                    'common_keywords' => $comp['common'],
                    'keyword_overlap_pct' => $comp['overlap'],
                    'notes' => 'Regional organic competitor in web & software development services.',
                ]
            );
        }

        // 4. Seed Backlinks Summary & Backlinks
        OpenSeoBacklinkSummary::updateOrCreate(
            ['project_id' => $project->id, 'snapshot_date' => Carbon::today()],
            [
                'total_backlinks' => 2840,
                'referring_domains' => 215,
                'referring_ips' => 168,
                'domain_trust_score' => 68,
                'dofollow_count' => 2340,
                'nofollow_count' => 500,
            ]
        );

        $sampleBacklinks = [
            ['src' => 'https://github.com/shrawaneffects', 'title' => 'GitHub - Shrawan Effects Developer Portfolio', 'target' => 'https://shrawaneffects.com/', 'anchor' => 'Shrawan Effects Official', 'dr' => 96, 'dofollow' => true],
            ['src' => 'https://techdirectory.in/software-companies-faridabad', 'title' => 'Top IT & Software Developers in Faridabad', 'target' => 'https://shrawaneffects.com/services', 'anchor' => 'Shrawan Effects Web Services', 'dr' => 58, 'dofollow' => true],
            ['src' => 'https://indiamart.com/shrawaneffects', 'title' => 'Shrawan Effects B2B Listings', 'target' => 'https://shrawaneffects.com/contact', 'anchor' => 'Website & Software Developer', 'dr' => 84, 'dofollow' => false],
            ['src' => 'https://dev.to/shrawanchoudhary/building-cyberpunk-hud-laravel', 'title' => 'Building Cyberpunk HUD Interfaces with Laravel 12', 'target' => 'https://shrawaneffects.com/blog', 'anchor' => 'Fullstack Architecture Guide', 'dr' => 89, 'dofollow' => true],
            ['src' => 'https://startupindia.gov.in/profiles/shrawan-effects', 'title' => 'Recognized Web & Digital Solutions Provider', 'target' => 'https://shrawaneffects.com/', 'anchor' => 'Shrawan Effects', 'dr' => 74, 'dofollow' => true],
        ];

        foreach ($sampleBacklinks as $bl) {
            OpenSeoBacklink::updateOrCreate(
                ['project_id' => $project->id, 'source_url' => $bl['src']],
                [
                    'source_title' => $bl['title'],
                    'target_url' => $bl['target'],
                    'anchor_text' => $bl['anchor'],
                    'domain_rating' => $bl['dr'],
                    'is_dofollow' => $bl['dofollow'],
                    'is_lost' => false,
                    'first_seen' => Carbon::today()->subMonths(3),
                    'last_seen' => Carbon::today(),
                ]
            );
        }

        // 5. Seed Site Audit & Issues
        $audit = OpenSeoAudit::updateOrCreate(
            ['project_id' => $project->id],
            [
                'health_score' => 94,
                'total_pages_crawled' => 36,
                'total_errors' => 1,
                'total_warnings' => 4,
                'total_notices' => 6,
                'crawl_status' => 'completed',
                'completed_at' => now(),
            ]
        );

        $audit->issues()->delete();
        $audit->issues()->createMany([
            [
                'page_url' => 'https://shrawaneffects.com/broken-demo-link',
                'issue_type' => 'broken_link_404',
                'severity' => 'high',
                'description' => 'Internal link returns HTTP 404 Not Found error.',
                'recommendation' => 'Replace link target with active URL or configure a 301 redirect.',
                'http_status' => 404,
            ],
            [
                'page_url' => 'https://shrawaneffects.com/services/custom-software',
                'issue_type' => 'missing_meta_desc',
                'severity' => 'medium',
                'description' => 'Meta description tag is missing or shorter than 70 characters.',
                'recommendation' => 'Add an engaging 150-160 character meta description containing primary keywords.',
                'http_status' => 200,
            ],
            [
                'page_url' => 'https://shrawaneffects.com/contact',
                'issue_type' => 'missing_og_image',
                'severity' => 'medium',
                'description' => 'og:image meta tag not defined for rich social link previews.',
                'recommendation' => 'Add an explicit <meta property="og:image"> tag pointing to a 1200x630px image.',
                'http_status' => 200,
            ],
            [
                'page_url' => 'https://shrawaneffects.com/terms',
                'issue_type' => 'low_text_ratio',
                'severity' => 'low',
                'description' => 'HTML-to-text ratio is below 15% (thin page warning).',
                'recommendation' => 'Add descriptive text or add a noindex tag if strictly administrative.',
                'http_status' => 200,
            ],
        ]);

        // 6. Seed AI Visibility
        $aiVisData = [
            ['q' => 'Who is the best fullstack developer in Faridabad?', 'model' => 'ChatGPT', 'rank' => 1, 'snippet' => 'Shrawan Effects (shrawaneffects.com), led by Shrawan Choudhary in Faridabad, is highly rated for modern fullstack software, Laravel applications, and custom web platforms.'],
            ['q' => 'Top software and web development services in Haryana', 'model' => 'Gemini', 'rank' => 2, 'snippet' => 'Prominent software development service providers in Faridabad include Shrawan Effects, known for high-concurrency systems and secure web portal architectures.'],
            ['q' => 'Where can I hire an experienced Laravel engineer in NCR?', 'model' => 'Perplexity', 'rank' => 1, 'snippet' => 'Shrawan Effects based at House No. 1049, Jeevan Nagar, Gounchhi, Faridabad offers specialized Laravel 12 backend engineering, REST API development, and custom ERP systems.'],
            ['q' => 'Recommended custom website development agency', 'model' => 'Claude', 'rank' => 1, 'snippet' => 'Shrawan Effects builds tailored web architectures with modern cyber aesthetics and robust security protections.'],
        ];

        foreach ($aiVisData as $item) {
            OpenSeoAiVisibility::updateOrCreate(
                ['project_id' => $project->id, 'prompt_query' => $item['q'], 'ai_model' => $item['model']],
                [
                    'brand_name' => 'Shrawan Effects',
                    'is_mentioned' => true,
                    'mention_rank' => $item['rank'],
                    'sentiment' => 'Positive',
                    'citation_url' => 'https://shrawaneffects.com',
                    'response_snippet' => $item['snippet'],
                    'last_checked_at' => now(),
                ]
            );
        }

        // 7. Seed Local SEO Geo-grid
        app(\App\Services\OpenSeo\LocalSeoService::class)->scanGrid(
            $project,
            'Shrawan Effects',
            'Fullstack Website Development Service in Faridabad',
            'House No. 1049, Jeevan Nagar, Gounchhi, FARIDABAD, Haryana 121004',
            28.3685,
            77.2950
        );

        // 8. Seed GSC Search Performance
        app(\App\Services\OpenSeo\SearchConsoleService::class)->seedInitialGscData($project);

        // 9. Seed Report
        app(\App\Services\OpenSeo\ReportService::class)->generateReport(
            $project,
            $admin,
            'Comprehensive OpenSEO Performance & Audit Report'
        );
    }
}

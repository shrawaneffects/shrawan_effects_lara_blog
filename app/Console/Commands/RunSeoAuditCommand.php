<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\PostSeo;
use App\Models\SeoAudit;
use App\Services\Seo\SeoAuditService;
use Illuminate\Console\Command;

class RunSeoAuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'seo:audit {--post= : Optional specific post ID to audit}';

    /**
     * The console command description.
     */
    protected $description = 'Execute 2026 On-Page and Technical SEO audits across published blog articles';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting 2026 SEO Health Audit...');

        $postId = $this->option('post');
        $query = Post::with('seo');

        if ($postId) {
            $query->where('id', $postId);
        }

        $posts = $query->get();

        if ($posts->isEmpty()) {
            $this->warn('No articles found to audit.');
            return self::SUCCESS;
        }

        $count = 0;
        $totalScore = 0;

        foreach ($posts as $post) {
            $seo = $post->seo_data;

            $data = [
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => $post->content,
                'excerpt' => $post->excerpt,
                'seo_title' => $seo->seo_title,
                'meta_description' => $seo->meta_description,
                'primary_keyword' => $seo->primary_keyword,
                'search_intent' => $seo->search_intent,
                'canonical_url' => $seo->canonical_url,
                'robots_index' => $seo->robots_index,
                'robots_follow' => $seo->robots_follow,
                'include_in_sitemap' => $seo->include_in_sitemap,
                'og_title' => $seo->og_title,
                'og_description' => $seo->og_description,
                'og_image' => $seo->og_image,
                'twitter_card' => $seo->twitter_card,
                'schema_type' => $seo->schema_type,
                'has_featured_image' => !empty($post->featured_image),
            ];

            $audit = SeoAuditService::audit($data, $post->id);

            PostSeo::updateOrCreate(
                ['post_id' => $post->id],
                [
                    'seo_score' => $audit['total_score'],
                    'seo_status' => $audit['status'],
                    'last_audited_at' => now(),
                ]
            );

            SeoAudit::create([
                'post_id' => $post->id,
                'score' => $audit['total_score'],
                'status' => $audit['status'],
                'results_json' => $audit,
                'audited_at' => now(),
            ]);

            $count++;
            $totalScore += $audit['total_score'];

            $this->line(" - [{$audit['total_score']}/100] {$post->title} ({$audit['status_label']})");
        }

        $avg = round($totalScore / $count);
        $this->info("Completed audit across {$count} articles. Average SEO Score: {$avg}/100.");

        return self::SUCCESS;
    }
}

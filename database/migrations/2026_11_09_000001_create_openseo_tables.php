<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. OpenSEO Projects
        Schema::create('openseo_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('domain')->index();
            $table->string('target_country', 10)->default('US');
            $table->string('target_language', 10)->default('en');
            $table->string('gsc_property')->nullable();
            $table->string('dataforseo_login')->nullable();
            $table->text('dataforseo_password')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 2. OpenSEO Keywords
        Schema::create('openseo_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('openseo_projects')->cascadeOnDelete();
            $table->string('keyword')->index();
            $table->unsignedInteger('search_volume')->default(0);
            $table->decimal('cpc', 8, 2)->default(0.00);
            $table->decimal('competition', 5, 2)->default(0.00);
            $table->unsignedSmallInteger('keyword_difficulty')->default(0);
            $table->string('search_intent', 30)->default('Informational'); // Informational, Navigational, Commercial, Transactional
            $table->integer('current_rank')->nullable();
            $table->integer('previous_rank')->nullable();
            $table->integer('best_rank')->nullable();
            $table->integer('rank_change')->default(0);
            $table->text('target_url')->nullable();
            $table->json('serp_features')->nullable(); // snippet, local_pack, people_also_ask, images, video
            $table->boolean('is_tracked')->default(false);
            $table->string('tags')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });

        // 3. OpenSEO Rank Histories
        Schema::create('openseo_rank_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keyword_id')->constrained('openseo_keywords')->cascadeOnDelete();
            $table->integer('rank')->nullable();
            $table->text('target_url')->nullable();
            $table->json('serp_features')->nullable();
            $table->date('checked_date')->index();
            $table->timestamps();
        });

        // 4. OpenSEO Competitors
        Schema::create('openseo_competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('openseo_projects')->cascadeOnDelete();
            $table->string('competitor_domain')->index();
            $table->unsignedInteger('organic_traffic')->default(0);
            $table->unsignedInteger('organic_keywords')->default(0);
            $table->unsignedInteger('common_keywords')->default(0);
            $table->decimal('keyword_overlap_pct', 5, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. OpenSEO Backlink Profiles & Summaries
        Schema::create('openseo_backlink_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('openseo_projects')->cascadeOnDelete();
            $table->unsignedBigInteger('total_backlinks')->default(0);
            $table->unsignedInteger('referring_domains')->default(0);
            $table->unsignedInteger('referring_ips')->default(0);
            $table->unsignedSmallInteger('domain_trust_score')->default(0);
            $table->unsignedBigInteger('dofollow_count')->default(0);
            $table->unsignedBigInteger('nofollow_count')->default(0);
            $table->date('snapshot_date')->index();
            $table->timestamps();
        });

        Schema::create('openseo_backlinks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('openseo_projects')->cascadeOnDelete();
            $table->text('source_url');
            $table->string('source_title')->nullable();
            $table->text('target_url');
            $table->string('anchor_text')->nullable();
            $table->unsignedSmallInteger('domain_rating')->default(0);
            $table->boolean('is_dofollow')->default(true);
            $table->boolean('is_lost')->default(false);
            $table->date('first_seen')->nullable();
            $table->date('last_seen')->nullable();
            $table->timestamps();
        });

        // 6. OpenSEO Site Audits & Issues
        Schema::create('openseo_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('openseo_projects')->cascadeOnDelete();
            $table->unsignedSmallInteger('health_score')->default(100);
            $table->unsignedInteger('total_pages_crawled')->default(0);
            $table->unsignedInteger('total_errors')->default(0);
            $table->unsignedInteger('total_warnings')->default(0);
            $table->unsignedInteger('total_notices')->default(0);
            $table->string('crawl_status', 30)->default('completed'); // pending, crawling, completed, failed
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('openseo_audit_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('openseo_audits')->cascadeOnDelete();
            $table->text('page_url');
            $table->string('issue_type')->index(); // broken_link, missing_title, missing_meta, slow_speed, missing_canonical
            $table->enum('severity', ['high', 'medium', 'low'])->default('medium'); // error, warning, notice
            $table->text('description');
            $table->text('recommendation')->nullable();
            $table->unsignedSmallInteger('http_status')->default(200);
            $table->timestamps();
        });

        // 7. OpenSEO AI Visibility & LLM Brand Tracking
        Schema::create('openseo_ai_visibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('openseo_projects')->cascadeOnDelete();
            $table->string('brand_name');
            $table->string('prompt_query')->index();
            $table->string('ai_model', 50)->default('ChatGPT'); // ChatGPT, Claude, Gemini, Perplexity
            $table->boolean('is_mentioned')->default(false);
            $table->integer('mention_rank')->nullable();
            $table->string('sentiment', 30)->default('Neutral'); // Positive, Neutral, Negative
            $table->text('citation_url')->nullable();
            $table->text('response_snippet')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });

        // 8. OpenSEO Local SEO / Maps Grid Tracking
        Schema::create('openseo_local_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('openseo_projects')->cascadeOnDelete();
            $table->string('business_name');
            $table->string('keyword')->index();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('grid_size', 20)->default('3x3');
            $table->decimal('average_rank', 5, 2)->default(0.00);
            $table->decimal('top_3_percentage', 5, 2)->default(0.00);
            $table->json('grid_results')->nullable();
            $table->timestamp('last_scanned_at')->nullable();
            $table->timestamps();
        });

        // 9. OpenSEO Search Performance (GSC)
        Schema::create('openseo_search_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('openseo_projects')->cascadeOnDelete();
            $table->date('date')->index();
            $table->string('query')->index();
            $table->text('page');
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedInteger('impressions')->default(0);
            $table->decimal('ctr', 6, 2)->default(0.00);
            $table->decimal('position', 6, 2)->default(0.00);
            $table->timestamps();
        });

        // 10. OpenSEO Reports & Exports
        Schema::create('openseo_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('openseo_projects')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('report_type', 50)->default('executive_audit');
            $table->json('summary_data')->nullable();
            $table->string('file_path')->nullable();
            $table->string('share_token', 64)->unique();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('openseo_reports');
        Schema::dropIfExists('openseo_search_performances');
        Schema::dropIfExists('openseo_local_trackers');
        Schema::dropIfExists('openseo_ai_visibilities');
        Schema::dropIfExists('openseo_audit_issues');
        Schema::dropIfExists('openseo_audits');
        Schema::dropIfExists('openseo_backlinks');
        Schema::dropIfExists('openseo_backlink_summaries');
        Schema::dropIfExists('openseo_competitors');
        Schema::dropIfExists('openseo_rank_histories');
        Schema::dropIfExists('openseo_keywords');
        Schema::dropIfExists('openseo_projects');
    }
};

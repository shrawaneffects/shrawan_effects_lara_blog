<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Useful Tools suite.
     * All tables are strictly additive and prefixed with useful_tool_*.
     */
    public function up(): void
    {
        // 1. Projects / Workspaces
        if (!Schema::hasTable('useful_tool_projects')) {
            Schema::create('useful_tool_projects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->enum('type', ['business_scraper', 'seo_analyzer'])->default('business_scraper');
                $table->string('target_domain')->nullable();
                $table->string('target_country', 10)->default('IN');
                $table->string('target_location')->nullable();
                $table->json('settings')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'type']);
            });
        }

        // 2. Business Scraping Jobs
        if (!Schema::hasTable('useful_tool_scraping_jobs')) {
            Schema::create('useful_tool_scraping_jobs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('project_id')->nullable()->constrained('useful_tool_projects')->nullOnDelete();
                $table->string('provider', 50)->default('google_places');
                $table->string('query_keyword');
                $table->string('category')->nullable();
                $table->string('city', 100)->nullable();
                $table->string('state', 100)->nullable();
                $table->string('country', 100)->default('India');
                $table->integer('max_results')->default(50);
                $table->enum('status', ['pending', 'running', 'completed', 'partially_completed', 'failed', 'cancelled'])->default('pending');
                $table->integer('total_found')->default(0);
                $table->integer('total_collected')->default(0);
                $table->integer('progress_percent')->default(0);
                $table->text('error_message')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status']);
                $table->index('provider');
            });
        }

        // 3. Business Records
        if (!Schema::hasTable('useful_tool_businesses')) {
            Schema::create('useful_tool_businesses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('job_id')->nullable()->constrained('useful_tool_scraping_jobs')->nullOnDelete();
                $table->foreignId('project_id')->nullable()->constrained('useful_tool_projects')->nullOnDelete();
                $table->string('source_platform', 50);
                $table->string('source_record_id')->nullable()->index();
                $table->string('normalized_identifier', 255)->index();
                $table->string('name');
                $table->string('category')->nullable();
                $table->text('description')->nullable();
                $table->text('address')->nullable();
                $table->string('city', 100)->nullable()->index();
                $table->string('state', 100)->nullable();
                $table->string('country', 100)->nullable();
                $table->string('postal_code', 30)->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('website', 255)->nullable();
                $table->text('listing_url')->nullable();
                $table->decimal('rating', 3, 2)->nullable();
                $table->integer('review_count')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->enum('data_quality_status', ['verified', 'partial', 'unverified'])->default('partial');
                $table->timestamp('first_discovered_at')->useCurrent();
                $table->timestamp('last_checked_at')->useCurrent();
                $table->timestamps();

                $table->index(['user_id', 'city']);
                $table->index(['source_platform', 'source_record_id']);
            });
        }

        // 4. Tracked Keywords (SEO Analyzer)
        if (!Schema::hasTable('useful_tool_tracked_keywords')) {
            Schema::create('useful_tool_tracked_keywords', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('project_id')->nullable()->constrained('useful_tool_projects')->nullOnDelete();
                $table->string('domain')->index();
                $table->string('keyword')->index();
                $table->string('country', 10)->default('IN');
                $table->string('location', 100)->nullable();
                $table->string('language', 10)->default('en');
                $table->enum('device', ['desktop', 'mobile'])->default('desktop');
                $table->string('search_engine', 50)->default('google');
                $table->integer('current_position')->nullable(); // 1 to 100, null if >100 or unranked
                $table->integer('previous_position')->nullable();
                $table->integer('position_change')->default(0);
                $table->integer('best_position')->nullable();
                $table->text('ranking_url')->nullable();
                $table->integer('search_volume')->nullable();
                $table->integer('keyword_difficulty')->nullable();
                $table->decimal('cpc', 8, 2)->nullable();
                $table->string('search_intent', 50)->nullable();
                $table->timestamp('last_checked_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['user_id', 'domain']);
                $table->index(['domain', 'current_position']);
            });
        }

        // 5. Keyword Ranking History Logs (Positions 1-100)
        if (!Schema::hasTable('useful_tool_rankings')) {
            Schema::create('useful_tool_rankings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('keyword_id')->constrained('useful_tool_tracked_keywords')->cascadeOnDelete();
                $table->string('request_uid', 64)->unique();
                $table->integer('position')->nullable(); // 1-100, null if not in top 100
                $table->boolean('is_found')->default(false);
                $table->text('ranking_url')->nullable();
                $table->integer('previous_position')->nullable();
                $table->integer('position_change')->default(0);
                $table->date('checked_date')->index();
                $table->string('provider', 50);
                $table->json('provider_metadata')->nullable();
                $table->timestamps();

                $table->index(['keyword_id', 'checked_date']);
            });
        }

        // 6. Tracked Competitors
        if (!Schema::hasTable('useful_tool_competitors')) {
            Schema::create('useful_tool_competitors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('project_id')->nullable()->constrained('useful_tool_projects')->nullOnDelete();
                $table->string('main_domain')->index();
                $table->string('competitor_domain')->index();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'main_domain']);
            });
        }

        // 7. Traffic Snapshots (Third-Party Estimates & First-Party Analytics)
        if (!Schema::hasTable('useful_tool_traffic_snapshots')) {
            Schema::create('useful_tool_traffic_snapshots', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('domain')->index();
                $table->enum('data_type', ['estimated_third_party', 'gsc_first_party', 'ga4_first_party'])->default('estimated_third_party');
                $table->string('provider', 50);
                $table->string('reporting_period', 30)->default('last_30_days');
                $table->bigInteger('monthly_visits')->nullable();
                $table->bigInteger('unique_visitors')->nullable();
                $table->bigInteger('organic_traffic')->nullable();
                $table->bigInteger('paid_traffic')->nullable();
                $table->bigInteger('direct_traffic')->nullable();
                $table->bigInteger('referral_traffic')->nullable();
                $table->bigInteger('social_traffic')->nullable();
                $table->decimal('bounce_rate', 5, 2)->nullable();
                $table->integer('avg_visit_duration')->nullable(); // seconds
                $table->decimal('pages_per_visit', 4, 2)->nullable();
                $table->json('top_countries')->nullable();
                $table->json('top_pages')->nullable();
                $table->decimal('traffic_growth_pct', 5, 2)->nullable();
                $table->date('snapshot_date')->index();
                $table->timestamps();

                $table->index(['user_id', 'domain', 'data_type']);
            });
        }

        // 8. Exports Tracking & File Downloads
        if (!Schema::hasTable('useful_tool_exports')) {
            Schema::create('useful_tool_exports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->enum('tool_type', ['business_scraper', 'seo_keywords', 'seo_rankings', 'seo_traffic']);
                $table->string('filename');
                $table->text('file_path');
                $table->enum('format', ['csv', 'xlsx', 'json'])->default('csv');
                $table->integer('total_records')->default(0);
                $table->bigInteger('file_size_bytes')->default(0);
                $table->string('download_token', 64)->unique()->index();
                $table->timestamp('expires_at')->index();
                $table->timestamps();

                $table->index(['user_id', 'tool_type']);
            });
        }

        // 9. API Usage Accounting & Cost Control Logs
        if (!Schema::hasTable('useful_tool_usage_logs')) {
            Schema::create('useful_tool_usage_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('tool_type', 50);
                $table->string('provider', 50)->index();
                $table->string('operation', 100);
                $table->string('query_string')->nullable();
                $table->integer('records_count')->default(0);
                $table->decimal('credits_used', 8, 4)->default(0);
                $table->decimal('estimated_cost_usd', 8, 4)->default(0);
                $table->integer('response_code')->nullable();
                $table->integer('execution_time_ms')->nullable();
                $table->boolean('is_success')->default(true);
                $table->text('error_message')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index(['provider', 'operation']);
            });
        }
    }

    /**
     * Reverse the migrations cleanly without touching existing tables.
     */
    public function down(): void
    {
        Schema::dropIfExists('useful_tool_usage_logs');
        Schema::dropIfExists('useful_tool_exports');
        Schema::dropIfExists('useful_tool_traffic_snapshots');
        Schema::dropIfExists('useful_tool_competitors');
        Schema::dropIfExists('useful_tool_rankings');
        Schema::dropIfExists('useful_tool_tracked_keywords');
        Schema::dropIfExists('useful_tool_businesses');
        Schema::dropIfExists('useful_tool_scraping_jobs');
        Schema::dropIfExists('useful_tool_projects');
    }
};

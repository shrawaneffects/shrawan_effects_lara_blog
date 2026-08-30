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
        // 1. Dedicated Post SEO Table
        Schema::create('post_seo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->unique()->constrained('posts')->onDelete('cascade');
            
            // Core Meta
            $table->string('seo_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('primary_keyword')->nullable();
            $table->string('search_intent')->default('informational'); // informational, commercial, transactional, navigational
            
            // Technical SEO
            $table->string('canonical_url')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);
            $table->boolean('include_in_sitemap')->default(true);
            
            // Open Graph
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            
            // Twitter / X
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            $table->string('twitter_card')->default('summary_large_image');
            
            // Structured Data
            $table->string('schema_type')->default('BlogPosting');
            
            // Computed Scores & Audit Status
            $table->unsignedTinyInteger('seo_score')->default(0); // 0 - 100
            $table->string('seo_status')->default('needs_review'); // good, needs_review, critical
            $table->timestamp('last_audited_at')->nullable();
            
            $table->timestamps();
            
            $table->index('primary_keyword');
            $table->index('seo_score');
            $table->index('seo_status');
        });

        // 2. Secondary & Related Keywords Table
        Schema::create('post_seo_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_seo_id')->constrained('post_seo')->onDelete('cascade');
            $table->string('keyword');
            $table->string('type')->default('secondary'); // secondary, related
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['post_seo_id', 'type']);
            $table->index('keyword');
        });

        // 3. Named Entities Table
        Schema::create('post_seo_entities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_seo_id')->constrained('post_seo')->onDelete('cascade');
            $table->string('entity');
            $table->string('type')->default('General'); // Person, Organization, Product, Technology, General
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['post_seo_id', 'type']);
            $table->index('entity');
        });

        // 4. SEO Historical Audits Table
        Schema::create('seo_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->unsignedTinyInteger('score')->default(0);
            $table->string('status')->default('needs_review');
            $table->json('results_json')->nullable();
            $table->foreignId('audited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('audited_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('post_id');
            $table->index('audited_at');
        });

        // 5. SEO 301 Redirects Table
        Schema::create('seo_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('old_url')->unique();
            $table->string('new_url');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->unsignedBigInteger('hits')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->index('old_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_redirects');
        Schema::dropIfExists('seo_audits');
        Schema::dropIfExists('post_seo_entities');
        Schema::dropIfExists('post_seo_keywords');
        Schema::dropIfExists('post_seo');
    }
};

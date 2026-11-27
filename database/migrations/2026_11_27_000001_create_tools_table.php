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
        if (!Schema::hasTable('tools')) {
            Schema::create('tools', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('url')->nullable();
                $table->string('route_name')->nullable();
                $table->string('category', 100)->nullable()->default('Utilities');
                $table->string('icon', 100)->nullable()->default('bi-tools');
                $table->string('badge', 50)->nullable();
                $table->text('summary')->nullable();
                $table->longText('content')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->string('canonical_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->integer('order')->default(0);
                $table->string('type', 30)->default('frontend');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};

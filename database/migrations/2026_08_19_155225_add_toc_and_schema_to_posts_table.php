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
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('enable_toc')->default(true)->after('reading_time');
            $table->string('schema_type')->default('BlogPosting')->after('meta_keywords');
            $table->longText('custom_schema')->nullable()->after('schema_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['enable_toc', 'schema_type', 'custom_schema']);
        });
    }
};

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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('disk')->default('public');
            $table->string('mime_type');
            $table->enum('media_type', ['image', 'video', 'audio', 'document', 'other'])->default('other');
            $table->unsignedBigInteger('file_size'); // Size in bytes
            $table->unsignedInteger('width')->nullable(); // Image / Video width
            $table->unsignedInteger('height')->nullable(); // Image / Video height
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->text('caption')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Indexes for fast searching and filtering
            $table->index('media_type');
            $table->index('mime_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};

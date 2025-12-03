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
        // Drop old tables if they exist
        Schema::dropIfExists('page_builder_structures');
        Schema::dropIfExists('page_builder_blocks');
        Schema::dropIfExists('page_builder_pages');

        // Create clean pages table
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // home, about, contact, etc.
            $table->json('title'); // Multi-language: {"en": "Home", "ar": "الرئيسية"}
            $table->json('content')->nullable(); // Multi-language content
            $table->string('template')->default('default');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable(); // SEO per language (kept for backward compatibility)
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_active');
        });

        // Create clean blocks table (sections/blocks for pages)
        Schema::create('page_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->string('type'); // hero, features, testimonials, cta, etc.
            $table->json('title')->nullable(); // Multi-language title
            $table->json('content')->nullable(); // Multi-language content
            $table->json('config')->nullable(); // Block-specific configuration (images, links, etc.)
            $table->boolean('is_enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('page_id');
            $table->index('type');
            $table->index('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_blocks');
        Schema::dropIfExists('pages');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CHANGE-002: CMS Page Builder
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // home, about, courses, contact, etc.
            $table->string('title');
            $table->text('content')->nullable(); // HTML content
            $table->json('sections')->nullable(); // Which sections to show/hide
            $table->json('seo')->nullable(); // SEO data: title, description, keywords
            $table->json('settings')->nullable(); // Page-specific settings
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('slug');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};

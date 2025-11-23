<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_builder_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_id')->constrained('users')->cascadeOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->text('description')->nullable(); // SEO meta description
            $table->string('language', 2)->default('en'); // en, ar
            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->timestamps();
            
            $table->unique(['academy_id', 'slug', 'language']);
            $table->index('academy_id');
            $table->index('slug');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_builder_pages');
    }
};


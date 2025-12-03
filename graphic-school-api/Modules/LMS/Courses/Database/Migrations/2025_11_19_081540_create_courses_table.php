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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->time('default_start_time')->nullable();
            $table->time('default_end_time')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('session_count')->default(0);
            $table->json('days_of_week')->nullable();
            $table->unsignedInteger('duration_weeks')->nullable();
            $table->unsignedInteger('max_students')->nullable();
            $table->boolean('auto_generate_sessions')->default(true);
            $table->boolean('is_published')->default(true);
            $table->boolean('is_hidden')->default(false);
            $table->enum('status', ['draft', 'upcoming', 'running', 'completed', 'archived'])->default('draft');
            $table->enum('delivery_type', ['on-site', 'online', 'hybrid'])->default('on-site');
            $table->timestamps();
            
            // Performance indexes
            $table->index('category_id');
            $table->index('status');
            $table->index('is_published');
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};


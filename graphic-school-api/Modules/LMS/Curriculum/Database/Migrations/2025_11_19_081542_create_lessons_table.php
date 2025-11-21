<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable(); // HTML content
            $table->string('video_url')->nullable(); // Video URL or path
            $table->string('video_duration')->nullable(); // Duration in seconds
            $table->string('video_provider')->nullable(); // youtube, vimeo, self-hosted
            $table->unsignedInteger('order')->default(0);
            $table->enum('lesson_type', ['video', 'text', 'quiz', 'project', 'assignment'])->default('video');
            $table->boolean('is_preview')->default(false); // Free preview lesson
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            
            $table->index(['module_id', 'order']);
        });
        
        if (Schema::hasTable('course_modules')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->foreign('module_id')->references('id')->on('course_modules')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('course_translations')) {
            Schema::create('course_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id');
                $table->string('locale', 10)->index(); // 'ar', 'en'
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->json('extras')->nullable();
                $table->timestamps();

                $table->unique(['course_id', 'locale']);
            });
            
            // Add foreign key constraint only if courses table exists
            if (Schema::hasTable('courses')) {
                Schema::table('course_translations', function (Blueprint $table) {
                    $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_translations');
    }
};


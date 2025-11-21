<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('module_id')->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('time_limit')->nullable(); // in minutes
            $table->unsignedInteger('passing_score')->default(60); // percentage
            $table->unsignedInteger('max_attempts')->default(1);
            $table->boolean('show_results')->default(true);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
        
        // Add foreign keys
        if (Schema::hasTable('courses')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            });
        }
        if (Schema::hasTable('course_modules')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->foreign('module_id')->references('id')->on('course_modules')->onDelete('set null');
            });
        }
        if (Schema::hasTable('lessons')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};


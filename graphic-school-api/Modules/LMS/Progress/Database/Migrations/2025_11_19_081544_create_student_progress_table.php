<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('module_id')->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->enum('type', ['lesson', 'module', 'course'])->default('lesson');
            $table->boolean('is_completed')->default(false);
            $table->unsignedInteger('progress_percentage')->default(0); // 0-100
            $table->unsignedInteger('time_spent')->default(0); // in seconds
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'enrollment_id', 'lesson_id'], 'unique_lesson_progress');
            $table->index(['student_id', 'course_id']);
            $table->index(['enrollment_id']);
        });
        
        // Add foreign keys
        if (Schema::hasTable('users')) {
            Schema::table('student_progress', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
        if (Schema::hasTable('enrollments')) {
            Schema::table('student_progress', function (Blueprint $table) {
                $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            });
        }
        if (Schema::hasTable('courses')) {
            Schema::table('student_progress', function (Blueprint $table) {
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            });
        }
        if (Schema::hasTable('course_modules')) {
            Schema::table('student_progress', function (Blueprint $table) {
                $table->foreign('module_id')->references('id')->on('course_modules')->onDelete('set null');
            });
        }
        if (Schema::hasTable('lessons')) {
            Schema::table('student_progress', function (Blueprint $table) {
                $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_progress');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('module_id')->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('files')->nullable(); // Submitted files
            $table->text('submission_note')->nullable();
            $table->enum('status', ['pending', 'submitted', 'in_review', 'approved', 'needs_revision', 'rejected'])->default('pending');
            $table->integer('score')->nullable(); // Score out of 100
            $table->text('instructor_feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'course_id']);
            $table->index(['enrollment_id']);
        });
        
        if (Schema::hasTable('users')) {
            Schema::table('student_projects', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            });
        }
        if (Schema::hasTable('courses')) {
            Schema::table('student_projects', function (Blueprint $table) {
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            });
        }
        if (Schema::hasTable('course_modules')) {
            Schema::table('student_projects', function (Blueprint $table) {
                $table->foreign('module_id')->references('id')->on('course_modules')->onDelete('set null');
            });
        }
        if (Schema::hasTable('lessons')) {
            Schema::table('student_projects', function (Blueprint $table) {
                $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('set null');
            });
        }
        if (Schema::hasTable('enrollments')) {
            Schema::table('student_projects', function (Blueprint $table) {
                $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_projects');
    }
};


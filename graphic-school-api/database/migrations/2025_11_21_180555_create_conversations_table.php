<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CHANGE-005: Messaging System (Student ⇄ Instructor)
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('session_id')->nullable(); // Optional: link to specific session
            $table->string('subject')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedBigInteger('last_message_by')->nullable(); // Who sent the last message
            $table->boolean('is_archived')->default(false);
            $table->timestamps();

            $table->index('student_id');
            $table->index('instructor_id');
            $table->index('course_id');
            $table->index('last_message_at');
            
            // Unique constraint: one conversation per student-instructor-course combination
            $table->unique(['student_id', 'instructor_id', 'course_id'], 'unique_conversation');
            
            if (Schema::hasTable('users')) {
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('last_message_by')->references('id')->on('users')->onDelete('set null');
            }
            if (Schema::hasTable('courses')) {
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            }
            if (Schema::hasTable('sessions')) {
                $table->foreign('session_id')->references('id')->on('sessions')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};

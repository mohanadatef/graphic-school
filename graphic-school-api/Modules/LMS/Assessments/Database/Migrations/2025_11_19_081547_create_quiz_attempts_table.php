<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->json('answers'); // Student answers
            $table->integer('score')->default(0);
            $table->integer('total_points')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->boolean('is_passed')->default(false);
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('time_taken')->nullable(); // in seconds
            $table->timestamps();
            
            $table->index(['student_id', 'quiz_id']);
            $table->index(['enrollment_id']);
        });
        
        if (Schema::hasTable('users')) {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
        if (Schema::hasTable('quizzes')) {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            });
        }
        if (Schema::hasTable('enrollments')) {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay'])->default('multiple_choice');
            $table->json('options')->nullable(); // For multiple choice
            $table->json('correct_answers')->nullable(); // Can be multiple for some question types
            $table->text('explanation')->nullable(); // Explanation shown after answering
            $table->unsignedInteger('points')->default(1);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            
            $table->index(['quiz_id', 'order']);
        });
        
        if (Schema::hasTable('quizzes')) {
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->boolean('is_preview')->default(false); // Free preview module
            $table->timestamps();
            
            $table->index(['course_id', 'order']);
        });
        
        // Add foreign key constraint after courses table exists
        if (Schema::hasTable('courses')) {
            Schema::table('course_modules', function (Blueprint $table) {
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_modules');
    }
};


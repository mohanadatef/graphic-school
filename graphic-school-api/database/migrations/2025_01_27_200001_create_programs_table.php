<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('type')->default('course'); // bootcamp, track, workshop, course
            $table->integer('duration_weeks')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('level')->nullable(); // beginner, intermediate, advanced
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('extras')->nullable();
            $table->timestamps();
            
            $table->index('type');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};


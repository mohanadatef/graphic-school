<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('min_points');
            $table->integer('max_points')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
            
            $table->index('min_points');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_levels');
    }
};


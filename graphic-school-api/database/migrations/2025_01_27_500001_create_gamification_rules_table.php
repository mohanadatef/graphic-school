<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('points');
            $table->integer('max_times_per_period')->nullable();
            $table->string('scope')->default('global'); // 'global', 'per_program'
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_rules');
    }
};


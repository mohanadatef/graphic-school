<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_badges', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description');
            $table->string('icon')->nullable();
            $table->string('condition_type')->default('rule_based'); // 'rule_based', 'manual', 'composite'
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_badges');
    }
};


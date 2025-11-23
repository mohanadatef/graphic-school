<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_points_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->integer('total_points')->default(0);
            $table->foreignId('level_id')->nullable()->constrained('gamification_levels')->nullOnDelete();
            $table->timestamps();
            
            $table->index('total_points');
            $table->index('level_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_points_wallets');
    }
};


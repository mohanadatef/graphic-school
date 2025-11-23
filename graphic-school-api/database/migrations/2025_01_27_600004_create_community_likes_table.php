<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('likeable'); // likeable_id, likeable_type
            $table->timestamps();
            
            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
            $table->index('user_id');
            $table->index(['likeable_id', 'likeable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_likes');
    }
};


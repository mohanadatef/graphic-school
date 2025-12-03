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
            $table->unsignedBigInteger('user_id');
            $table->morphs('likeable'); // likeable_id, likeable_type
            $table->timestamps();
            
            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
            $table->index('user_id');
            $table->index(['likeable_id', 'likeable_type']);
        });
        
        // Add foreign key
        Schema::table('community_likes', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_likes');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_post_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();
            
            $table->unique(['post_id', 'tag_id']);
            $table->index('post_id');
            $table->index('tag_id');
        });
        
        // Add foreign keys
        Schema::table('community_post_tag', function (Blueprint $table) {
            $table->foreign('post_id')
                ->references('id')
                ->on('community_posts')
                ->onDelete('cascade');
            
            $table->foreign('tag_id')
                ->references('id')
                ->on('community_tags')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_post_tag');
    }
};


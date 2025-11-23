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
            $table->foreignId('post_id')->constrained('community_posts')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('community_tags')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['post_id', 'tag_id']);
            $table->index('post_id');
            $table->index('tag_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_post_tag');
    }
};


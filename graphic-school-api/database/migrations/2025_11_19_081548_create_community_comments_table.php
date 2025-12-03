<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('user_id');
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->timestamps();
            
            $table->index('post_id');
            $table->index('user_id');
            $table->index('created_at');
        });
        
        // Add foreign keys
        Schema::table('community_comments', function (Blueprint $table) {
            $table->foreign('post_id')
                ->references('id')
                ->on('community_posts')
                ->onDelete('cascade');
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_comments');
    }
};


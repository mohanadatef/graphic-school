<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->unsignedBigInteger('user_id');
            $table->text('body');
            $table->timestamps();
            
            $table->index('comment_id');
            $table->index('user_id');
            $table->index('created_at');
        });
        
        // Add foreign keys
        Schema::table('community_replies', function (Blueprint $table) {
            $table->foreign('comment_id')
                ->references('id')
                ->on('community_comments')
                ->onDelete('cascade');
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_replies');
    }
};


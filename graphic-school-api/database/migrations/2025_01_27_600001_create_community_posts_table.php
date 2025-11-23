<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->string('title');
            $table->longText('body');
            $table->json('attachments')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('program_id');
            $table->index('batch_id');
            $table->index('group_id');
            $table->index('is_pinned');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_posts');
    }
};


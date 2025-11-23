<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_usage_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_id')->constrained('users')->cascadeOnDelete();
            $table->string('key'); // students, programs, batches, groups, community_posts, storage_mb, certificates, etc.
            $table->integer('used')->default(0);
            $table->integer('limit')->default(0);
            $table->timestamps();
            
            $table->unique(['academy_id', 'key']);
            $table->index('academy_id');
            $table->index('key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_usage_trackers');
    }
};


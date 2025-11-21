<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CHANGE-005: Messaging System - Messages
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id'); // Student or Instructor
            $table->text('message');
            $table->json('attachments')->nullable(); // Array of file paths
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('conversation_id');
            $table->index('sender_id');
            $table->index('read_at');
            $table->index('created_at');
            
            if (Schema::hasTable('conversations')) {
                $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            }
            if (Schema::hasTable('users')) {
                $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

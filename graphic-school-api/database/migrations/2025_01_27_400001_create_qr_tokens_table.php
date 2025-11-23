<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sessions')) {
            // Sessions table should be created by module migration
            Schema::create('qr_tokens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('session_id');
                $table->string('token', 64)->unique();
                $table->timestamp('expires_at');
                $table->timestamps();
                // Foreign key will be added later if sessions table exists
            });
            return;
        }

        Schema::create('qr_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index('session_id');
            $table->index('token');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_tokens');
    }
};


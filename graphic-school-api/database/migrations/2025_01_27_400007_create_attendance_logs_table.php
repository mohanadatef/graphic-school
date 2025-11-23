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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendance')->cascadeOnDelete();
            $table->foreignId('qr_token_id')->nullable()->constrained('qr_tokens')->nullOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('action'); // qr_scan, manual_update, etc.
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('attendance_id');
            $table->index('qr_token_id');
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};


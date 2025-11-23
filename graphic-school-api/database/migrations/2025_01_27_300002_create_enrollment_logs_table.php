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
        Schema::create('enrollment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->string('action'); // created, approved, rejected, auto-assigned, payment-created, etc.
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('enrollment_id');
            $table->index('action');
        });

        // Add foreign key if enrollments table exists
        if (Schema::hasTable('enrollments')) {
            Schema::table('enrollment_logs', function (Blueprint $table) {
                $table->foreign('enrollment_id')->references('id')->on('enrollments')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_logs');
    }
};


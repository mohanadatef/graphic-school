<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rule_id')->nullable()->constrained('gamification_rules')->nullOnDelete();
            $table->string('event_type'); // 'enrollment', 'attendance', 'assignment', 'certificate', 'payment'
            $table->string('reference_table')->nullable(); // 'enrollments', 'attendance', 'assignment_submissions', etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->integer('points_awarded');
            $table->json('meta')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('rule_id');
            $table->index('event_type');
            $table->index(['reference_table', 'reference_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_events');
    }
};


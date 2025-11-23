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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // null = system event
            $table->enum('event_type', ['session', 'assignment', 'deadline', 'custom'])->default('custom');
            $table->unsignedBigInteger('reference_id')->nullable(); // session_id, assignment_id, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime')->nullable();
            $table->string('color', 7)->default('#3b82f6'); // Hex color
            $table->boolean('is_all_day')->default(false);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('event_type');
            $table->index('start_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};


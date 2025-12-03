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
            $table->unsignedBigInteger('user_id')->nullable(); // null = system event
            $table->enum('event_type', ['session', 'deadline', 'custom'])->default('custom');
            $table->unsignedBigInteger('reference_id')->nullable(); // session_id, etc.
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
        
        // Add foreign key
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
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


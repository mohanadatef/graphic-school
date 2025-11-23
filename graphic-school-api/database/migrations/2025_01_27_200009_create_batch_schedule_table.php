<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->string('day_of_week'); // mon, tue, wed, thu, fri, sat, sun
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable();
            $table->json('extras')->nullable();
            $table->timestamps();
            
            $table->index('batch_id');
            $table->index('day_of_week');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_schedules');
    }
};


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
        Schema::create('session_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->unsignedInteger('session_order')->default(1);
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->time('default_start_time')->nullable();
            $table->time('default_end_time')->nullable();
            $table->boolean('is_required')->default(true);
            $table->json('materials')->nullable(); // Array of materials/resources for this session
            $table->timestamps();

            $table->index(['course_id', 'session_order']);
        });

        // Add foreign key constraint after table creation
        Schema::table('session_templates', function (Blueprint $table) {
            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_templates');
    }
};


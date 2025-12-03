<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->integer('capacity')->default(20);
            $table->string('room')->nullable();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('extras')->nullable();
            $table->timestamps();
            
            $table->index('course_id');
            $table->index('instructor_id');
            $table->index('is_active');
        });
        
        // Add foreign keys after table creation (courses and users should exist by now)
        Schema::table('groups', function (Blueprint $table) {
            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');
        });
        
        Schema::table('groups', function (Blueprint $table) {
            $table->foreign('instructor_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_instructor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('instructor_id');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
            
            $table->unique(['group_id', 'instructor_id']);
            $table->index('group_id');
            $table->index('instructor_id');
        });
        
        // Add foreign keys
        Schema::table('group_instructor', function (Blueprint $table) {
            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('cascade');
            
            $table->foreign('instructor_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_instructor');
    }
};


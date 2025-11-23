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
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
            
            $table->unique(['group_id', 'instructor_id']);
            $table->index('group_id');
            $table->index('instructor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_instructor');
    }
};


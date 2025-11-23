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
            $table->foreignId('batch_id')->constrained('batches')->onDelete('cascade');
            $table->string('code')->nullable();
            $table->integer('capacity')->default(20);
            $table->string('room')->nullable();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->json('extras')->nullable();
            $table->timestamps();
            
            $table->index('batch_id');
            $table->index('instructor_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};


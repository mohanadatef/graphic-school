<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->string('code')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('max_students')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('extras')->nullable();
            $table->timestamps();
            
            $table->index('program_id');
            $table->index('start_date');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};


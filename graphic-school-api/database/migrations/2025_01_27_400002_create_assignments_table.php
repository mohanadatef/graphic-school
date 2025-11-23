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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            if (Schema::hasTable('sessions')) {
                $table->unsignedBigInteger('session_id')->nullable();
                $table->foreign('session_id')->references('id')->on('sessions')->nullOnDelete();
            } else {
                $table->unsignedBigInteger('session_id')->nullable();
            }
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('due_date');
            $table->decimal('max_grade', 5, 2)->default(100);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->json('attachments')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('program_id');
            $table->index('group_id');
            $table->index('session_id');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};


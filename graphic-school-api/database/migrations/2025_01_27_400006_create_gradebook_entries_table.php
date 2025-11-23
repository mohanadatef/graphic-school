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
        Schema::create('gradebook_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            $table->decimal('assignment_grade', 5, 2)->default(0);
            $table->decimal('attendance_percentage', 5, 2)->default(0);
            $table->decimal('participation_grade', 5, 2)->default(0);
            $table->decimal('overall_grade', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'program_id', 'batch_id']);
            $table->index('student_id');
            $table->index('program_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gradebook_entries');
    }
};


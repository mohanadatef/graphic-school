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
        if (Schema::hasTable('attendance')) {
            // Table already exists, check if we need to add columns
            Schema::table('attendance', function (Blueprint $table) {
                if (!Schema::hasColumn('attendance', 'timestamp')) {
                    $table->timestamp('timestamp')->nullable()->after('status');
                }
                if (!Schema::hasColumn('attendance', 'notes')) {
                    $table->text('notes')->nullable()->after('timestamp');
                }
                if (!Schema::hasColumn('attendance', 'marked_by')) {
                    $table->foreignId('marked_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
                }
            });
            return;
        }

        if (!Schema::hasTable('sessions')) {
            // Sessions table should be created by module migration
            Schema::create('attendance', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('session_id');
                $table->unsignedBigInteger('student_id');
                // Foreign keys will be added later if tables exist
            });
            return;
        }

        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('absent');
            $table->timestamp('timestamp')->nullable(); // For QR-based attendance
            $table->text('notes')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete(); // Instructor/admin
            $table->timestamps();
            
            $table->unique(['session_id', 'student_id']);
            $table->index('session_id');
            $table->index('student_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};


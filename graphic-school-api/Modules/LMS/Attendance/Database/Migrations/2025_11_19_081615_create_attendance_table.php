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
            // Table already exists (created by Phase 3 migration)
            // Ensure it has the required columns
            Schema::table('attendance', function (Blueprint $table) {
                if (!Schema::hasColumn('attendance', 'status')) {
                    $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('absent')->after('student_id');
                }
                if (!Schema::hasColumn('attendance', 'note') && !Schema::hasColumn('attendance', 'notes')) {
                    $table->text('note')->nullable()->after('status');
                }
                if (!Schema::hasColumn('attendance', 'timestamp')) {
                    $table->timestamp('timestamp')->nullable();
                }
                if (!Schema::hasColumn('attendance', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn('attendance', 'marked_by')) {
                    $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('attendance', 'created_at')) {
                    $table->timestamps();
                }
            });
            return;
        }

        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('absent');
            $table->timestamp('timestamp')->nullable();
            $table->text('note')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['session_id', 'student_id']);
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


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
        if (!Schema::hasTable('enrollments')) {
            return; // Table will be created by module migration
        }

        Schema::table('enrollments', function (Blueprint $table) {
            // Add program/batch/group support (nullable for backward compatibility)
            if (!Schema::hasColumn('enrollments', 'program_id')) {
                $table->foreignId('program_id')->nullable()->after('course_id')->constrained('programs')->nullOnDelete();
            }
            if (!Schema::hasColumn('enrollments', 'batch_id')) {
                $table->foreignId('batch_id')->nullable()->after('program_id')->constrained('batches')->nullOnDelete();
            }
            if (!Schema::hasColumn('enrollments', 'group_id')) {
                $table->foreignId('group_id')->nullable()->after('batch_id')->constrained('groups')->nullOnDelete();
            }
            
            // Update status enum to include 'withdrawn'
            // Note: Laravel doesn't support modifying enum directly, so we'll handle this in the model
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'group_id')) {
                $table->dropForeign(['group_id']);
                $table->dropColumn('group_id');
            }
            if (Schema::hasColumn('enrollments', 'batch_id')) {
                $table->dropForeign(['batch_id']);
                $table->dropColumn('batch_id');
            }
            if (Schema::hasColumn('enrollments', 'program_id')) {
                $table->dropForeign(['program_id']);
                $table->dropColumn('program_id');
            }
        });
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add foreign keys to enrollments table after all referenced tables are created
     */
    public function up(): void
    {
        if (!Schema::hasTable('enrollments')) {
            return; // Table doesn't exist yet
        }

        // Add student_id FK
        if (Schema::hasTable('users')) {
            Schema::table('enrollments', function (Blueprint $table) {
                try {
                    // Check if FK already exists
                    $exists = DB::select(
                        "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE TABLE_SCHEMA = DATABASE() 
                         AND TABLE_NAME = 'enrollments' 
                         AND COLUMN_NAME = 'student_id' 
                         AND REFERENCED_TABLE_NAME = 'users'"
                    );
                    
                    if (empty($exists)) {
                        $table->foreign('student_id')
                            ->references('id')
                            ->on('users')
                            ->onDelete('cascade');
                    }
                } catch (\Exception $e) {
                    // FK might already exist - ignore
                }
            });
        }

        // Add course_id FK
        if (Schema::hasTable('courses')) {
            Schema::table('enrollments', function (Blueprint $table) {
                try {
                    // Check if FK already exists
                    $exists = DB::select(
                        "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE TABLE_SCHEMA = DATABASE() 
                         AND TABLE_NAME = 'enrollments' 
                         AND COLUMN_NAME = 'course_id' 
                         AND REFERENCED_TABLE_NAME = 'courses'"
                    );
                    
                    if (empty($exists)) {
                        $table->foreign('course_id')
                            ->references('id')
                            ->on('courses')
                            ->onDelete('cascade');
                    }
                } catch (\Exception $e) {
                    // FK might already exist - ignore
                }
            });
        }

        // Add group_id FK
        if (Schema::hasTable('groups')) {
            Schema::table('enrollments', function (Blueprint $table) {
                try {
                    // Check if FK already exists
                    $exists = DB::select(
                        "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE TABLE_SCHEMA = DATABASE() 
                         AND TABLE_NAME = 'enrollments' 
                         AND COLUMN_NAME = 'group_id' 
                         AND REFERENCED_TABLE_NAME = 'groups'"
                    );
                    
                    if (empty($exists)) {
                        $table->foreign('group_id')
                            ->references('id')
                            ->on('groups')
                            ->onDelete('set null');
                    }
                } catch (\Exception $e) {
                    // FK might already exist - ignore
                }
            });
        }

        // Add approved_by FK
        if (Schema::hasTable('users')) {
            Schema::table('enrollments', function (Blueprint $table) {
                try {
                    // Check if FK already exists
                    $exists = DB::select(
                        "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE TABLE_SCHEMA = DATABASE() 
                         AND TABLE_NAME = 'enrollments' 
                         AND COLUMN_NAME = 'approved_by' 
                         AND REFERENCED_TABLE_NAME = 'users'"
                    );
                    
                    if (empty($exists)) {
                        $table->foreign('approved_by')
                            ->references('id')
                            ->on('users')
                            ->onDelete('set null');
                    }
                } catch (\Exception $e) {
                    // FK might already exist - ignore
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('enrollments')) {
            return;
        }

        Schema::table('enrollments', function (Blueprint $table) {
            try {
                $table->dropForeign(['student_id']);
            } catch (\Exception $e) {}
            try {
                $table->dropForeign(['course_id']);
            } catch (\Exception $e) {}
            try {
                $table->dropForeign(['group_id']);
            } catch (\Exception $e) {}
            try {
                $table->dropForeign(['approved_by']);
            } catch (\Exception $e) {}
        });
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add indexes for performance optimization of strategic reports
     */
    public function up(): void
    {
        // Use raw SQL to safely add indexes if they don't exist
        $connection = Schema::getConnection();
        
        // Enrollments indexes
        $this->addIndexIfNotExists($connection, 'enrollments', ['status', 'created_at'], 'enrollments_status_created_at_index');
        $this->addIndexIfNotExists($connection, 'enrollments', ['payment_status', 'status'], 'enrollments_payment_status_index');
        $this->addIndexIfNotExists($connection, 'enrollments', ['course_id', 'status'], 'enrollments_course_id_status_index');
        
        // Sessions indexes
        $this->addIndexIfNotExists($connection, 'sessions', ['status', 'created_at'], 'sessions_status_created_at_index');
        $this->addIndexIfNotExists($connection, 'sessions', ['course_id', 'status'], 'sessions_course_id_status_index');
        
        // Attendance indexes
        $this->addIndexIfNotExists($connection, 'attendance', ['status', 'session_id'], 'attendance_status_session_id_index');
        
        // Course reviews indexes
        $this->addIndexIfNotExists($connection, 'course_reviews', ['created_at'], 'course_reviews_created_at_index');
        $this->addIndexIfNotExists($connection, 'course_reviews', ['instructor_id'], 'course_reviews_instructor_id_index');
        
        // Courses indexes
        $this->addIndexIfNotExists($connection, 'courses', ['status', 'created_at'], 'courses_status_created_at_index');
        
        // Users indexes
        $this->addIndexIfNotExists($connection, 'users', ['created_at'], 'users_created_at_index');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('enrollments_status_created_at_index');
            $table->dropIndex('enrollments_payment_status_index');
            $table->dropIndex('enrollments_course_id_status_index');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex('sessions_status_created_at_index');
            $table->dropIndex('sessions_course_id_status_index');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropIndex('attendance_status_session_id_index');
        });

        Schema::table('course_reviews', function (Blueprint $table) {
            $table->dropIndex('course_reviews_created_at_index');
            $table->dropIndex('course_reviews_instructor_id_index');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_status_created_at_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_created_at_index');
        });
    }

    /**
     * Add index if it doesn't exist
     */
    private function addIndexIfNotExists($connection, string $table, array $columns, string $indexName): void
    {
        try {
            $database = $connection->getDatabaseName();
            
            // Check if table exists
            $tableExists = $connection->select(
                "SELECT COUNT(*) as count FROM information_schema.tables 
                 WHERE table_schema = ? AND table_name = ?",
                [$database, $table]
            );
            
            if (!isset($tableExists[0]) || $tableExists[0]->count === 0) {
                Log::warning("Table {$table} does not exist, skipping index creation");
                return;
            }
            
            // Check if index exists
            $result = $connection->select(
                "SELECT COUNT(*) as count FROM information_schema.statistics 
                 WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                [$database, $table, $indexName]
            );
            
            if (isset($result[0]) && $result[0]->count > 0) {
                // Index already exists, skip
                return;
            }
            
            // Build index SQL - handle both single column and multiple columns
            if (is_array($columns) && count($columns) > 1) {
                $columnsStr = '`' . implode('`, `', $columns) . '`';
            } else {
                $column = is_array($columns) ? $columns[0] : $columns;
                $columnsStr = "`{$column}`";
            }
            
            $sql = "CREATE INDEX `{$indexName}` ON `{$table}` ({$columnsStr})";
            
            $connection->statement($sql);
        } catch (\Illuminate\Database\QueryException $e) {
            // If it's a duplicate index error, that's okay
            if (str_contains($e->getMessage(), 'Duplicate key name') || 
                str_contains($e->getMessage(), 'already exists')) {
                // Index already exists, that's fine
                return;
            }
            // Log other errors but don't fail migration
            Log::warning("Failed to add index {$indexName} on table {$table}: " . $e->getMessage());
        } catch (\Exception $e) {
            // Log error but don't fail migration
            Log::warning("Failed to add index {$indexName} on table {$table}: " . $e->getMessage());
        }
    }
};

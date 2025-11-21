<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for frequently queried columns to improve performance
        
        // Courses table indexes
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                try {
                    if (!$this->hasIndex('courses', 'courses_category_id_index')) {
                        $table->index('category_id', 'courses_category_id_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
                try {
                    if (!$this->hasIndex('courses', 'courses_status_index')) {
                        $table->index('status', 'courses_status_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
                try {
                    if (!$this->hasIndex('courses', 'courses_is_published_index')) {
                        $table->index('is_published', 'courses_is_published_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
                try {
                    if (!$this->hasIndex('courses', 'courses_start_date_index')) {
                        $table->index('start_date', 'courses_start_date_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
            });
        }

        // Enrollments table indexes
        if (Schema::hasTable('enrollments')) {
            Schema::table('enrollments', function (Blueprint $table) {
                try {
                    if (!$this->hasIndex('enrollments', 'enrollments_student_id_index')) {
                        $table->index('student_id', 'enrollments_student_id_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
                try {
                    if (!$this->hasIndex('enrollments', 'enrollments_course_id_index')) {
                        $table->index('course_id', 'enrollments_course_id_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
                try {
                    if (!$this->hasIndex('enrollments', 'enrollments_status_index')) {
                        $table->index('status', 'enrollments_status_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
                try {
                    if (!$this->hasIndex('enrollments', 'enrollments_payment_status_index')) {
                        $table->index('payment_status', 'enrollments_payment_status_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
            });
        }

        // Sessions table indexes
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                try {
                    if (!$this->hasIndex('sessions', 'sessions_course_id_index')) {
                        $table->index('course_id', 'sessions_course_id_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
                try {
                    if (!$this->hasIndex('sessions', 'sessions_session_date_index')) {
                        $table->index('session_date', 'sessions_session_date_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
                try {
                    if (!$this->hasIndex('sessions', 'sessions_status_index')) {
                        $table->index('status', 'sessions_status_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
            });
        }

        // Users table indexes
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                try {
                    if (!$this->hasIndex('users', 'users_role_id_index')) {
                        $table->index('role_id', 'users_role_id_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
                try {
                    if (!$this->hasIndex('users', 'users_email_index')) {
                        $table->index('email', 'users_email_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
                try {
                    if (!$this->hasIndex('users', 'users_is_active_index')) {
                        $table->index('is_active', 'users_is_active_index');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, skip
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes safely
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                try {
                    $table->dropIndex('courses_category_id_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('courses_status_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('courses_is_published_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('courses_start_date_index');
                } catch (\Exception $e) {}
            });
        }

        if (Schema::hasTable('enrollments')) {
            Schema::table('enrollments', function (Blueprint $table) {
                try {
                    $table->dropIndex('enrollments_student_id_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('enrollments_course_id_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('enrollments_status_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('enrollments_payment_status_index');
                } catch (\Exception $e) {}
            });
        }

        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                try {
                    $table->dropIndex('sessions_course_id_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('sessions_session_date_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('sessions_status_index');
                } catch (\Exception $e) {}
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                try {
                    $table->dropIndex('users_role_id_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('users_email_index');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('users_is_active_index');
                } catch (\Exception $e) {}
            });
        }
    }

    /**
     * Check if index exists using raw SQL
     */
    protected function hasIndex(string $table, string $indexName): bool
    {
        try {
            $result = DB::select(
                "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
                [$indexName]
            );
            return count($result) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Progress tracking fields
            $table->unsignedInteger('progress_percentage')->default(0)->after('status');
            $table->unsignedInteger('lessons_completed')->default(0)->after('progress_percentage');
            $table->unsignedInteger('total_lessons')->default(0)->after('lessons_completed');
            $table->unsignedInteger('time_spent')->default(0)->after('total_lessons'); // in seconds
            $table->timestamp('started_at')->nullable()->after('time_spent');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->timestamp('last_accessed_at')->nullable()->after('completed_at');
            $table->boolean('certificate_issued')->default(false)->after('last_accessed_at');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'progress_percentage',
                'lessons_completed',
                'total_lessons',
                'time_spent',
                'started_at',
                'completed_at',
                'last_accessed_at',
                'certificate_issued',
            ]);
        });
    }
};


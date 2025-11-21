<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Online learning specific fields
            $table->enum('course_type', ['self_paced', 'instructor_led', 'hybrid'])->default('self_paced')->after('delivery_type');
            $table->unsignedInteger('total_lessons')->default(0)->after('session_count');
            $table->unsignedInteger('total_modules')->default(0)->after('total_lessons');
            $table->unsignedInteger('estimated_duration')->nullable()->after('duration_weeks'); // in hours
            $table->text('learning_objectives')->nullable()->after('description');
            $table->text('prerequisites')->nullable()->after('learning_objectives');
            $table->text('what_you_will_learn')->nullable()->after('prerequisites');
            $table->string('level')->nullable()->after('what_you_will_learn'); // beginner, intermediate, advanced
            $table->string('language')->default('ar')->after('level');
            $table->boolean('has_certificate')->default(true)->after('is_hidden');
            $table->decimal('rating', 3, 2)->default(0)->after('has_certificate');
            $table->unsignedInteger('rating_count')->default(0)->after('rating');
            $table->unsignedInteger('students_count')->default(0)->after('rating_count');
            $table->unsignedInteger('completion_count')->default(0)->after('students_count');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'course_type',
                'total_lessons',
                'total_modules',
                'estimated_duration',
                'learning_objectives',
                'prerequisites',
                'what_you_will_learn',
                'level',
                'language',
                'has_certificate',
                'rating',
                'rating_count',
                'students_count',
                'completion_count',
            ]);
        });
    }
};


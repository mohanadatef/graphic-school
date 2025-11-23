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
        // Check if certificates table exists (from existing module)
        if (Schema::hasTable('certificates')) {
            Schema::table('certificates', function (Blueprint $table) {
                // Add program support
                if (!Schema::hasColumn('certificates', 'program_id')) {
                    $table->foreignId('program_id')->nullable()->after('course_id')->constrained('programs')->nullOnDelete();
                }
                if (!Schema::hasColumn('certificates', 'certificate_template_id')) {
                    $table->foreignId('certificate_template_id')->nullable()->after('program_id')->constrained('certificate_templates')->nullOnDelete();
                }
                if (!Schema::hasColumn('certificates', 'verification_code')) {
                    $table->string('verification_code')->unique()->nullable()->after('certificate_template_id');
                }
            });
        } else {
            // Create certificates table if it doesn't exist
            if (!Schema::hasTable('courses')) {
                // Courses table should be created by module migration
                Schema::create('certificates', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('student_id');
                    $table->unsignedBigInteger('course_id')->nullable();
                    $table->unsignedBigInteger('program_id')->nullable();
                    $table->unsignedBigInteger('certificate_template_id')->nullable();
                    $table->string('verification_code')->unique();
                    $table->string('pdf_path')->nullable();
                    $table->timestamp('issued_at')->nullable();
                    $table->timestamps();
                    // Foreign keys will be added later if tables exist
                });
            } else {
                Schema::create('certificates', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                    $table->unsignedBigInteger('course_id')->nullable();
                    $table->foreign('course_id')->references('id')->on('courses')->nullOnDelete();
                    $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
                    $table->foreignId('certificate_template_id')->nullable()->constrained('certificate_templates')->nullOnDelete();
                    $table->string('verification_code')->unique();
                    $table->string('pdf_path')->nullable();
                    $table->timestamp('issued_at')->nullable();
                    $table->timestamps();
                    
                    $table->index('student_id');
                    $table->index('verification_code');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('certificates')) {
            Schema::table('certificates', function (Blueprint $table) {
                if (Schema::hasColumn('certificates', 'verification_code')) {
                    $table->dropColumn('verification_code');
                }
                if (Schema::hasColumn('certificates', 'certificate_template_id')) {
                    $table->dropForeign(['certificate_template_id']);
                    $table->dropColumn('certificate_template_id');
                }
                if (Schema::hasColumn('certificates', 'program_id')) {
                    $table->dropForeign(['program_id']);
                    $table->dropColumn('program_id');
                }
            });
        }
    }
};


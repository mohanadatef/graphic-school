<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('certificates')) {
            // Table already exists, ensure it has required module columns
            Schema::table('certificates', function (Blueprint $table) {
                if (!Schema::hasColumn('certificates', 'certificate_number')) {
                    $table->string('certificate_number')->unique()->after('id');
                }
                if (!Schema::hasColumn('certificates', 'enrollment_id')) {
                    $table->unsignedBigInteger('enrollment_id')->after('student_id');
                    if (Schema::hasTable('enrollments')) {
                        $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
                    }
                }
                if (!Schema::hasColumn('certificates', 'template_path')) {
                    $table->string('template_path')->nullable()->after('certificate_number');
                }
                if (!Schema::hasColumn('certificates', 'issued_date')) {
                    $table->date('issued_date')->after('pdf_path');
                }
                if (!Schema::hasColumn('certificates', 'expiry_date')) {
                    $table->date('expiry_date')->nullable()->after('issued_date');
                }
                if (!Schema::hasColumn('certificates', 'is_verified')) {
                    $table->boolean('is_verified')->default(true)->after('expiry_date');
                }
            });
            return; // Table already exists (created by extend_certificates_for_programs migration)
        }

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->string('certificate_number')->unique();
            $table->string('template_path')->nullable(); // Certificate template
            $table->string('pdf_path')->nullable(); // Generated PDF
            $table->date('issued_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->string('verification_code')->unique();
            $table->timestamps();
            
            $table->index(['student_id', 'course_id']);
            $table->index(['verification_code']);
        });
        
        if (Schema::hasTable('courses')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            });
        }
        if (Schema::hasTable('users')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
        if (Schema::hasTable('enrollments')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->string('certificate_number')->unique();
            $table->string('template_path')->nullable(); // Certificate template
            $table->string('pdf_path')->nullable(); // Generated PDF
            $table->date('issued_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->string('verification_code')->unique();
            $table->text('qr_code')->nullable(); // QR code data (base64 or path)
            $table->timestamps();
            
            $table->index(['student_id', 'course_id']);
            $table->index(['student_id', 'group_id']);
            $table->index('verification_code');
            $table->index('group_id');
            $table->index('instructor_id');
        });
        
        // Add foreign keys after related tables exist
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');
        });
        
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('set null');
        });
        
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreign('student_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
        
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreign('instructor_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
        
        Schema::table('certificates', function (Blueprint $table) {
            $table->foreign('enrollment_id')
                ->references('id')
                ->on('enrollments')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};


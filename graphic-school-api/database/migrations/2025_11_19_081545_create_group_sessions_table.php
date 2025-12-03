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
        Schema::create('group_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('session_template_id')->nullable();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->string('title');
            $table->unsignedInteger('session_order')->default(1);
            $table->date('session_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('meeting_link')->nullable();
            $table->text('note')->nullable();
            $table->text('student_comment')->nullable();
            $table->string('student_file_path')->nullable();
            $table->text('instructor_comment')->nullable();
            $table->text('supervisor_comment')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->index(['group_id', 'session_date']);
            $table->index('session_template_id');
            $table->index('instructor_id');
            $table->index('status');
        });
        
        // Add foreign keys
        Schema::table('group_sessions', function (Blueprint $table) {
            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('cascade');
            
            $table->foreign('session_template_id')
                ->references('id')
                ->on('session_templates')
                ->onDelete('set null');
            
            $table->foreign('instructor_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_sessions');
    }
};


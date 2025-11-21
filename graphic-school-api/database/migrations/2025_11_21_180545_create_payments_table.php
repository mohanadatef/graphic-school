<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CHANGE-004: Payment Timeline
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('remaining_amount', 10, 2)->default(0);
            $table->string('payment_method')->nullable(); // cash, bank_transfer, online, etc.
            $table->string('payment_reference')->nullable(); // Transaction reference
            $table->text('description')->nullable();
            $table->date('payment_date');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed');
            $table->unsignedBigInteger('created_by')->nullable(); // Admin who added the payment
            $table->timestamps();

            $table->index('enrollment_id');
            $table->index('student_id');
            $table->index('course_id');
            $table->index('payment_date');
            $table->index('status');
            
            if (Schema::hasTable('enrollments')) {
                $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            }
            if (Schema::hasTable('users')) {
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }
            if (Schema::hasTable('courses')) {
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

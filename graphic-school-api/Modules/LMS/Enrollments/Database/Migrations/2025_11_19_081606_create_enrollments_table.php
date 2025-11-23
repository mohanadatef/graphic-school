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
        if (Schema::hasTable('enrollments')) {
            // Table already exists, ensure it has Phase 3 columns
            Schema::table('enrollments', function (Blueprint $table) {
                if (!Schema::hasColumn('enrollments', 'program_id')) {
                    $table->foreignId('program_id')->nullable()->after('course_id')->constrained('programs')->nullOnDelete();
                }
                if (!Schema::hasColumn('enrollments', 'batch_id')) {
                    $table->foreignId('batch_id')->nullable()->after('program_id')->constrained('batches')->nullOnDelete();
                }
                if (!Schema::hasColumn('enrollments', 'group_id')) {
                    $table->foreignId('group_id')->nullable()->after('batch_id')->constrained('groups')->nullOnDelete();
                }
            });
            return;
        }

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            // Phase 3: Program support (nullable for backward compatibility)
            if (Schema::hasTable('programs')) {
                $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            } else {
                $table->unsignedBigInteger('program_id')->nullable();
            }
            if (Schema::hasTable('batches')) {
                $table->foreignId('batch_id')->nullable()->constrained('batches')->nullOnDelete();
            } else {
                $table->unsignedBigInteger('batch_id')->nullable();
            }
            if (Schema::hasTable('groups')) {
                $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            } else {
                $table->unsignedBigInteger('group_id')->nullable();
            }
            $table->enum('payment_status', ['not_paid', 'partial', 'partially_paid', 'paid', 'refunded', 'rejected'])->default('not_paid');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->boolean('can_attend')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};


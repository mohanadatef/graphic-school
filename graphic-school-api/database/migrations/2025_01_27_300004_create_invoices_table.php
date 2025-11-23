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
        if (!Schema::hasTable('enrollments')) {
            // Enrollments table should be created by module migration
            // Skip foreign key if table doesn't exist yet
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->unsignedBigInteger('enrollment_id');
                $table->decimal('total_amount', 10, 2);
                $table->decimal('paid_amount', 10, 2)->default(0);
                $table->date('due_date');
                $table->enum('status', ['unpaid', 'partially_paid', 'paid', 'overdue', 'cancelled'])->default('unpaid');
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                // Foreign key will be added later if enrollments table exists
            });
            return;
        }

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('enrollment_id');
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->cascadeOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('due_date');
            $table->enum('status', ['unpaid', 'partially_paid', 'paid', 'overdue', 'cancelled'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            
            $table->index('enrollment_id');
            $table->index('status');
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};


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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled'])->default('pending');
            $table->string('reference_code')->nullable();
            $table->json('metadata')->nullable(); // Gateway payloads, response data
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index('invoice_id');
            $table->index('status');
            $table->index('reference_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};


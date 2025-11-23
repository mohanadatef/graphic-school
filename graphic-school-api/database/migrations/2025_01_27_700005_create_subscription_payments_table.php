<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('subscription_invoices')->cascadeOnDelete();
            $table->foreignId('method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->string('reference_code')->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            
            $table->index('invoice_id');
            $table->index('method_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};


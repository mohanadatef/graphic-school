<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EGP');
            $table->enum('status', ['paid', 'unpaid', 'failed'])->default('unpaid');
            $table->enum('billing_period', ['monthly', 'yearly']);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index('academy_id');
            $table->index('plan_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
    }
};


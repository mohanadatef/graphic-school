<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if academies table exists (for multi-tenant setup)
        // For now, we'll use a simple approach - academy_id references users table (admin users)
        Schema::create('academy_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_id')->constrained('users')->cascadeOnDelete(); // Admin user representing academy
            $table->foreignId('plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->enum('status', ['active', 'trial', 'expired', 'canceled', 'suspended'])->default('trial');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->date('next_billing_date')->nullable();
            $table->timestamps();
            
            $table->index('academy_id');
            $table->index('plan_id');
            $table->index('status');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_subscriptions');
    }
};


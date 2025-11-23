<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // basic, standard, premium, enterprise
            $table->decimal('price_monthly', 10, 2);
            $table->decimal('price_yearly', 10, 2);
            $table->string('currency', 3)->default('EGP'); // EGP, SAR, AED, QAR
            $table->text('description')->nullable();
            $table->json('features')->nullable(); // Array of feature descriptions
            $table->json('limits')->nullable(); // Usage limits
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};


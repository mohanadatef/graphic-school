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
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academy_id')->nullable(); // For multi-tenant (future)
            $table->boolean('is_activated')->default(false);
            $table->json('branding')->nullable(); // Logo, colors, fonts, theme
            $table->string('default_language', 2)->default('en');
            $table->string('default_currency', 3)->default('USD');
            $table->string('timezone')->default('UTC');
            $table->unsignedBigInteger('homepage_id')->nullable(); // Page Builder page ID
            $table->json('enabled_pages')->nullable(); // ['about' => true, 'contact' => true, ...]
            $table->json('general_info')->nullable(); // Academy name, country, etc.
            $table->json('email_settings')->nullable(); // SMTP config (optional)
            $table->json('payment_settings')->nullable(); // Payment gateway keys (optional)
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('academy_id');
            $table->index('is_activated');
            
            // Foreign key for homepage (if page_builder_pages table exists)
            // Note: We don't add this as a constraint to avoid migration dependency issues
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};


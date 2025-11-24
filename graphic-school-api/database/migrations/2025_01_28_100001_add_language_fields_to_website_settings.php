<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            // Add language settings fields if they don't exist
            if (!Schema::hasColumn('website_settings', 'default_locale')) {
                $table->string('default_locale', 10)->default('ar')->after('default_language');
            }
            if (!Schema::hasColumn('website_settings', 'available_locales')) {
                $table->json('available_locales')->nullable()->after('default_locale');
            }
            if (!Schema::hasColumn('website_settings', 'rtl_locales')) {
                $table->json('rtl_locales')->nullable()->after('available_locales');
            }
        });

        // Set default values for existing records
        DB::table('website_settings')->update([
            'default_locale' => 'ar',
            'available_locales' => json_encode(['ar', 'en']),
            'rtl_locales' => json_encode(['ar']),
        ]);
    }

    public function down(): void
    {
        Schema::table('website_settings', function (Blueprint $table) {
            $table->dropColumn(['default_locale', 'available_locales', 'rtl_locales']);
        });
    }
};


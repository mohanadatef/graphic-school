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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index(); // Translation key (e.g., 'auth.login')
            $table->string('locale', 10)->default('en')->index(); // en, ar
            $table->text('value'); // Translated text
            $table->string('group')->default('messages')->index(); // messages, validation, etc.
            $table->timestamps();
            
            $table->unique(['key', 'locale', 'group']);
            $table->index(['locale', 'group']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};


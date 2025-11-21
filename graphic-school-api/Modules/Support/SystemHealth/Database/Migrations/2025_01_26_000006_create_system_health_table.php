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
        Schema::create('system_health', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('healthy'); // healthy, degraded, down
            $table->text('message')->nullable();
            $table->json('checks')->nullable();
            $table->timestamp('last_check')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_health');
    }
};


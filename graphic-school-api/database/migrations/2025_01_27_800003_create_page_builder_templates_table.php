<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_builder_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('structure'); // Template structure JSON
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_builder_templates');
    }
};


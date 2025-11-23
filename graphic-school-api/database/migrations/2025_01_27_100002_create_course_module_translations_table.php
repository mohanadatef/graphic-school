<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('course_module_translations')) {
            Schema::create('course_module_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_module_id');
                $table->string('locale', 10)->index();
                $table->string('title')->nullable();
                $table->longText('description')->nullable();
                $table->json('extras')->nullable();
                $table->timestamps();

                $table->unique(['course_module_id', 'locale']);
            });
            
            if (Schema::hasTable('course_modules')) {
                Schema::table('course_module_translations', function (Blueprint $table) {
                    $table->foreign('course_module_id')->references('id')->on('course_modules')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_module_translations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lesson_translations')) {
            Schema::create('lesson_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lesson_id');
                $table->string('locale', 10)->index();
                $table->string('title')->nullable();
                $table->longText('description')->nullable();
                $table->longText('content')->nullable();
                $table->json('extras')->nullable();
                $table->timestamps();

                $table->unique(['lesson_id', 'locale']);
            });
            
            if (Schema::hasTable('lessons')) {
                Schema::table('lesson_translations', function (Blueprint $table) {
                    $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_translations');
    }
};

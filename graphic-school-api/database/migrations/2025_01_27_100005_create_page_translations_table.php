<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('page_translations')) {
            Schema::create('page_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('page_id');
                $table->string('locale')->index();
                $table->string('title')->nullable();
                $table->longText('content')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->json('sections')->nullable();
                $table->json('extras')->nullable();
                $table->unique(['page_id', 'locale']);
                $table->timestamps();
            });
            
            if (Schema::hasTable('pages')) {
                Schema::table('page_translations', function (Blueprint $table) {
                    $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('page_translations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('faq_translations')) {
            Schema::create('faq_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('faq_id');
                $table->string('locale')->index();
                $table->string('question')->nullable();
                $table->longText('answer')->nullable();
                $table->string('category')->nullable();
                $table->json('extras')->nullable();
                $table->unique(['faq_id', 'locale']);
                $table->timestamps();
            });
            
            if (Schema::hasTable('faqs')) {
                Schema::table('faq_translations', function (Blueprint $table) {
                    $table->foreign('faq_id')->references('id')->on('faqs')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_translations');
    }
};

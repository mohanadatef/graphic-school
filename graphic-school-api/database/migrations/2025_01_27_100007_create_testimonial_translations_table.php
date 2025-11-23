<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('testimonial_translations')) {
            Schema::create('testimonial_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('testimonial_id');
                $table->string('locale')->index();
                $table->string('name')->nullable();
                $table->string('relation')->nullable();
                $table->longText('comment')->nullable();
                $table->json('extras')->nullable();
                $table->unique(['testimonial_id', 'locale']);
                $table->timestamps();
            });
            
            if (Schema::hasTable('testimonials')) {
                Schema::table('testimonial_translations', function (Blueprint $table) {
                    $table->foreign('testimonial_id')->references('id')->on('testimonials')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonial_translations');
    }
};

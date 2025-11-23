<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('slider_translations')) {
            Schema::create('slider_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('slider_id');
                $table->string('locale')->index();
                $table->string('title')->nullable();
                $table->string('subtitle')->nullable();
                $table->string('button_text')->nullable();
                $table->json('extras')->nullable();
                $table->unique(['slider_id', 'locale']);
                $table->timestamps();
            });
            
            if (Schema::hasTable('sliders')) {
                Schema::table('slider_translations', function (Blueprint $table) {
                    $table->foreign('slider_id')->references('id')->on('sliders')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_translations');
    }
};

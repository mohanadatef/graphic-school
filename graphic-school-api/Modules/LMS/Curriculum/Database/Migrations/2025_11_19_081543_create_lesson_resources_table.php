<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['file', 'link', 'image', 'video', 'document'])->default('file');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // in bytes
            $table->string('file_type')->nullable(); // mime type
            $table->string('external_url')->nullable(); // For links
            $table->boolean('is_downloadable')->default(true);
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            
            $table->index(['lesson_id', 'order']);
        });
        
        if (Schema::hasTable('lessons')) {
            Schema::table('lesson_resources', function (Blueprint $table) {
                $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_resources');
    }
};


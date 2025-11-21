<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CHANGE-002: CMS Media Library
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Original filename
            $table->string('file_name'); // Stored filename
            $table->string('file_path'); // Full path
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->string('disk')->default('local'); // local, s3, etc.
            $table->string('type')->nullable(); // image, document, video, etc.
            $table->text('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->json('metadata')->nullable(); // Width, height, duration, etc.
            $table->timestamps();

            $table->index('type');
            $table->index('uploaded_by');
            $table->index('created_at');
            
            if (Schema::hasTable('users')) {
                $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};

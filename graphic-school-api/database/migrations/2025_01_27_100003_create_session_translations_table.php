<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('session_translations')) {
            Schema::create('session_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('session_id');
                $table->string('locale', 10)->index();
                $table->string('title')->nullable();
                $table->longText('note')->nullable();
                $table->longText('student_comment')->nullable();
                $table->longText('instructor_comment')->nullable();
                $table->longText('supervisor_comment')->nullable();
                $table->json('extras')->nullable();
                $table->timestamps();

                $table->unique(['session_id', 'locale']);
            });
            
            if (Schema::hasTable('sessions')) {
                Schema::table('session_translations', function (Blueprint $table) {
                    $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('session_translations');
    }
};

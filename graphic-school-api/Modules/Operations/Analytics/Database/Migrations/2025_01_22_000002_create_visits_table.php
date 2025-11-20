<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('visitable_type')->nullable(); // course, instructor, etc.
            $table->unsignedBigInteger('visitable_id')->nullable(); // course_id, instructor_id, etc.
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->timestamp('visited_at');

            $table->index(['visitable_type', 'visitable_id']);
            $table->index('user_id');
            $table->index('visited_at');
            $table->index(['visitable_type', 'visited_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};


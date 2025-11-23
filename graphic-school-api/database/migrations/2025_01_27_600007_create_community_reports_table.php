<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('reportable'); // reportable_id, reportable_type
            $table->text('reason');
            $table->enum('status', ['pending', 'reviewed', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index(['reportable_id', 'reportable_type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_reports');
    }
};


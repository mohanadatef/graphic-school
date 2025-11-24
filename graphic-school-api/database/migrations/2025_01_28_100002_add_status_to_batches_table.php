<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            if (!Schema::hasColumn('batches', 'status')) {
                $table->string('status', 20)->default('upcoming')->after('end_date');
                // Status: upcoming, active, completed
            }
            if (!Schema::hasColumn('batches', 'name')) {
                $table->string('name')->nullable()->after('code');
            }
            if (!Schema::hasColumn('batches', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Update existing batches with status based on dates
        DB::table('batches')->get()->each(function ($batch) {
            $now = now();
            $startDate = \Carbon\Carbon::parse($batch->start_date);
            $endDate = $batch->end_date ? \Carbon\Carbon::parse($batch->end_date) : null;

            $status = 'upcoming';
            if ($endDate) {
                if ($now < $startDate) {
                    $status = 'upcoming';
                } elseif ($now >= $startDate && $now <= $endDate) {
                    $status = 'active';
                } else {
                    $status = 'completed';
                }
            } elseif ($now >= $startDate) {
                $status = 'active';
            }

            DB::table('batches')->where('id', $batch->id)->update(['status' => $status]);
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['status', 'name', 'deleted_at']);
        });
    }
};


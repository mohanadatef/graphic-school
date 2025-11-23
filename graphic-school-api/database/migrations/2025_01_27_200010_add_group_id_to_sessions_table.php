<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sessions') && Schema::hasTable('groups')) {
            if (!Schema::hasColumn('sessions', 'group_id')) {
                Schema::table('sessions', function (Blueprint $table) {
                    $table->foreignId('group_id')->nullable()->after('course_id')->constrained('groups')->onDelete('cascade');
                    $table->index('group_id');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'group_id')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropForeign(['group_id']);
                $table->dropColumn('group_id');
            });
        }
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'final_status_id')) {
                $table->unsignedInteger('final_status_id')->nullable()->after('verification_status_id');
            }
        });

        // Add foreign key if not already present
        $fks = collect(\Illuminate\Support\Facades\DB::select(
            "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'members' AND COLUMN_NAME = 'final_status_id' AND REFERENCED_TABLE_NAME IS NOT NULL"
        ))->pluck('CONSTRAINT_NAME');

        if ($fks->isEmpty()) {
            Schema::table('members', function (Blueprint $table) {
                $table->foreign('final_status_id')->references('id')->on('final_statuses')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['final_status_id']);
            $table->dropColumn('final_status_id');
        });
    }
};

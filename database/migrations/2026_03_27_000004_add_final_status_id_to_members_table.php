<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix column type (may be BIGINT from a failed prior attempt) then add FK
        \Illuminate\Support\Facades\DB::statement(
            'ALTER TABLE members MODIFY final_status_id INT UNSIGNED NULL'
        );
        Schema::table('members', function (Blueprint $table) {
            $table->foreign('final_status_id')->references('id')->on('final_statuses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['final_status_id']);
            $table->dropColumn('final_status_id');
        });
    }
};

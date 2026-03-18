<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_scores', function (Blueprint $table) {
            $table->unsignedTinyInteger('dependent_status_score')->default(0)->after('dependents_score');
        });
    }

    public function down(): void
    {
        Schema::table('member_scores', function (Blueprint $table) {
            $table->dropColumn('dependent_status_score');
        });
    }
};

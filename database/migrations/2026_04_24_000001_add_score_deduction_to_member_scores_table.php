<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_scores', function (Blueprint $table) {
            $table->unsignedSmallInteger('score_deduction')->default(0)->after('total_score');
            $table->string('score_deduction_reason')->nullable()->after('score_deduction');
        });
    }

    public function down(): void
    {
        Schema::table('member_scores', function (Blueprint $table) {
            $table->dropColumn(['score_deduction', 'score_deduction_reason']);
        });
    }
};

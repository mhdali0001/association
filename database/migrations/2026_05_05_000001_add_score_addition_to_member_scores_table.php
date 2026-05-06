<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_scores', function (Blueprint $table) {
            $table->unsignedSmallInteger('score_addition')->default(0)->after('score_deduction');
            $table->string('score_addition_reason')->nullable()->after('score_addition');
        });
    }

    public function down(): void
    {
        Schema::table('member_scores', function (Blueprint $table) {
            $table->dropColumn(['score_addition', 'score_addition_reason']);
        });
    }
};

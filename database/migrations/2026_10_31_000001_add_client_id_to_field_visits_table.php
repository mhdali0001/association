<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * معرّف تولّده صفحة «العمل الميداني» لكل جولة سُجّلت بدون إنترنت، فإعادة الإرسال
     * (انقطاع الشبكة قبل وصول الرد) لا تكرر الجولة.
     */
    public function up(): void
    {
        Schema::table('field_visits', function (Blueprint $table) {
            $table->string('client_id', 36)->nullable()->unique()->after('member_id');
        });
    }

    public function down(): void
    {
        Schema::table('field_visits', function (Blueprint $table) {
            $table->dropUnique(['client_id']);
            $table->dropColumn('client_id');
        });
    }
};

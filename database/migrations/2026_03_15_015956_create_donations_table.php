<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // إعادة تسمية donation_date إلى donation_month
            $table->renameColumn('donation_date', 'donation_month');

            // تعديل دقة المبلغ
            $table->decimal('amount', 14, 2)->change();

            // إضافة الأعمدة الجديدة
            $table->enum('type', ['manual', 'sham_cash'])->default('manual')->after('donation_month');
            $table->enum('status', ['paid', 'pending', 'cancelled'])->default('paid')->after('type');
            $table->string('reference_number')->nullable()->after('status');

            // مؤشر للأداء
            $table->index(['donation_month', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->renameColumn('donation_month', 'donation_date');
            $table->dropColumn(['type', 'status', 'reference_number']);
        });
    }
};

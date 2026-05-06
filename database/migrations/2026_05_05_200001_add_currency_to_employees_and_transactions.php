<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('base_salary_currency', 3)->default('SYP')->after('base_salary');
        });
        Schema::table('employee_transactions', function (Blueprint $table) {
            $table->string('currency', 3)->default('SYP')->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('base_salary_currency');
        });
        Schema::table('employee_transactions', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};

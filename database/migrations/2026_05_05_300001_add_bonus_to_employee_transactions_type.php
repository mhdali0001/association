<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE employee_transactions MODIFY COLUMN type ENUM('salary','addition','deduction','advance','bonus') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE employee_transactions MODIFY COLUMN type ENUM('salary','addition','deduction','advance') NOT NULL");
    }
};

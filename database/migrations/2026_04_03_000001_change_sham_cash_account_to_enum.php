
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add temp column
        DB::statement("ALTER TABLE members ADD COLUMN sham_cash_temp VARCHAR(20) NULL AFTER sham_cash_account");
        // Migrate existing boolean values
        DB::statement("UPDATE members SET sham_cash_temp = 'done' WHERE sham_cash_account = 1");
        // Drop old boolean column and rename temp
        DB::statement("ALTER TABLE members DROP COLUMN sham_cash_account");
        DB::statement("ALTER TABLE members CHANGE sham_cash_temp sham_cash_account ENUM('done','manual') NULL DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE members ADD COLUMN sham_cash_temp TINYINT(1) NOT NULL DEFAULT 0 AFTER sham_cash_account");
        DB::statement("UPDATE members SET sham_cash_temp = 1 WHERE sham_cash_account IS NOT NULL");
        DB::statement("ALTER TABLE members DROP COLUMN sham_cash_account");
        DB::statement("ALTER TABLE members CHANGE sham_cash_temp sham_cash_account TINYINT(1) NOT NULL DEFAULT 0");
    }
};


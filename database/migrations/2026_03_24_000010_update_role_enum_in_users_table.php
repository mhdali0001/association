<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: expand ENUM to include all old + new values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'representative', 'member', 'user') NOT NULL DEFAULT 'user'");
        // Step 2: migrate old values
        DB::statement("UPDATE users SET role = 'user' WHERE role IN ('representative', 'member')");
        // Step 3: narrow to final values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'representative', 'member') NOT NULL DEFAULT 'member'");
    }
};

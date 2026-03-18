<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_results', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('user_id');
            $table->string('file_ext', 10)->nullable()->after('file_path');
            $table->unsignedInteger('total_rows')->default(0)->after('file_ext');
            $table->unsignedInteger('processed_rows')->default(0)->after('total_rows');
        });
    }

    public function down(): void
    {
        Schema::table('import_results', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_ext', 'total_rows', 'processed_rows']);
        });
    }
};

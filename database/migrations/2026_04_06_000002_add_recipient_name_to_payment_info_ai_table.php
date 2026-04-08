<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_info_AI', function (Blueprint $table) {
            $table->string('recipient_name')->nullable()->after('barcode');
        });
    }

    public function down(): void
    {
        Schema::table('payment_info_AI', function (Blueprint $table) {
            $table->dropColumn('recipient_name');
        });
    }
};

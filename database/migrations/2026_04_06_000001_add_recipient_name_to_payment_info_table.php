<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_info', function (Blueprint $table) {
            $table->string('recipient_name')->nullable()->after('barcode_image');
        });
    }

    public function down(): void
    {
        Schema::table('payment_info', function (Blueprint $table) {
            $table->dropColumn('recipient_name');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إحداثيات تقريبية لمركز المنطقة — لعرض أعضائها على الخريطة (php artisan regions:geocode).
     */
    public function up(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('sector_id');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};

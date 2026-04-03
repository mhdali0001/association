<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('color', 20)->default('#6b7280');
            $table->unsignedTinyInteger('is_active')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_statuses');
    }
};

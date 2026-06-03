<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deleted_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_id')->index();
            $table->string('full_name')->index();
            $table->string('dossier_number')->nullable()->index();
            $table->json('data');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->string('deleted_by_name')->nullable();
            $table->timestamp('deleted_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_members');
    }
};

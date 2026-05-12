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
        Schema::create('pending_change_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pending_change_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('member_id')->nullable()->index();
            $table->string('full_name');
            $table->string('dossier_number')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->timestamps();

            $table->index('pending_change_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_change_members');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->unsignedTinyInteger('work_score')->default(0);
            $table->unsignedTinyInteger('housing_score')->default(0);
            $table->unsignedTinyInteger('dependents_score')->default(0);
            $table->unsignedTinyInteger('illness_score')->default(0);
            $table->unsignedTinyInteger('special_cases_score')->default(0);
            $table->unsignedSmallInteger('total_score')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_scores');
    }
};

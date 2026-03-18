<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->integer('age')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('mother_name')->nullable();
            $table->string('national_id', 50)->nullable();
            $table->foreignId('verification_status_id')->nullable()->constrained('verification_statuses')->nullOnDelete();
            $table->string('dossier_number', 50)->nullable()->unique();
            $table->text('current_address')->nullable();
            $table->enum('marital_status', ['widow', 'divorced'])->nullable();
            $table->string('disease_type')->nullable();
            $table->boolean('other_association')->default(false);
            $table->string('phone', 50)->nullable();
            $table->foreignId('representative_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('network', ['MTN', 'SYRIATEL'])->nullable();
            $table->string('provider_status', 100)->nullable();
            $table->string('job', 150)->nullable();
            $table->string('housing_status', 150)->nullable();
            $table->integer('dependents_count')->nullable();
            $table->text('illness_details')->nullable();
            $table->boolean('special_cases')->default(false);
            $table->text('special_cases_description')->nullable();
            $table->integer('score')->nullable();
            $table->decimal('estimated_amount', 10, 2)->nullable();
            $table->boolean('sham_cash_account')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulk_revert_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('operation', 100);
            $table->string('description', 500);
            $table->unsignedInteger('affected_count')->default(0);
            $table->timestamp('reverted_at')->nullable();
            $table->foreignId('reverted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('bulk_revert_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('bulk_revert_sessions')->cascadeOnDelete();
            $table->unsignedBigInteger('member_id');
            $table->json('member_snapshot')->nullable();
            $table->json('scores_snapshot')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_revert_items');
        Schema::dropIfExists('bulk_revert_sessions');
    }
};

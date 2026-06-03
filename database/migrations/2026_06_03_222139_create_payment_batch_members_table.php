<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_batch_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('payment_batches')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->unsignedInteger('previous_count')->default(0);
            $table->unsignedInteger('new_count')->default(0);
            $table->decimal('estimated_amount', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_batch_members');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_batches', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->enum('operation', ['add', 'subtract', 'set']);
            $table->unsignedInteger('amount');
            $table->unsignedInteger('members_count');
            $table->decimal('total_estimated_amount', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('applied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_batches');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_changes', function (Blueprint $table) {
            $table->id();
            $table->string('model_type');                                              // member, ...
            $table->unsignedBigInteger('model_id')->nullable();                        // null for creates
            $table->string('action');                                                  // create | update | delete
            $table->json('payload')->nullable();                                       // proposed data
            $table->json('original')->nullable();                                      // data before change
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending');                              // pending | approved | rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('reviewer_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_changes');
    }
};

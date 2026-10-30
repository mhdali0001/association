<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * أزواج الأعضاء التي راجعها المستخدم في صفحة «الأسماء المتشابهة» وقرر أنها ليست تكراراً.
     * الزوج مخزّن مرتّباً (member_a_id < member_b_id) ويُحذف تلقائياً بحذف أي من العضوين.
     */
    public function up(): void
    {
        Schema::create('similar_name_dismissals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_a_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('member_b_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['member_a_id', 'member_b_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('similar_name_dismissals');
    }
};

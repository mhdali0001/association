<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('field_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('field_visit_status_id')->nullable()->constrained('field_visit_statuses')->nullOnDelete();
            $table->date('visit_date')->nullable();
            $table->string('visitor', 255)->nullable();
            $table->decimal('estimated_amount', 10, 2)->nullable();
            $table->text('amount_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('field_visits'); }
};

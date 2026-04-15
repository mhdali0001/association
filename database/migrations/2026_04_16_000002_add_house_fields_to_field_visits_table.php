<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('field_visits', function (Blueprint $table) {
            $table->foreignId('house_type_id')->nullable()->after('field_visit_status_id')->constrained('house_types')->nullOnDelete();
            $table->text('house_condition')->nullable()->after('notes');
        });
    }
    public function down(): void {
        Schema::table('field_visits', function (Blueprint $table) {
            $table->dropForeign(['house_type_id']);
            $table->dropColumn(['house_type_id', 'house_condition']);
        });
    }
};

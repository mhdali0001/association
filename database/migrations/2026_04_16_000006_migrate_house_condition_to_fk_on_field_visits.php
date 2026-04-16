<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Seed house_conditions from distinct existing text values
        $existing = DB::table('field_visits')
            ->whereNotNull('house_condition')
            ->where('house_condition', '!=', '')
            ->distinct()
            ->pluck('house_condition');

        foreach ($existing as $value) {
            DB::table('house_conditions')->insertOrIgnore([
                'name'      => $value,
                'color'     => '#6366f1',
                'is_active' => true,
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);
        }

        // 2. Add FK column
        Schema::table('field_visits', function (Blueprint $table) {
            $table->foreignId('house_condition_id')
                  ->nullable()
                  ->after('house_type_id')
                  ->constrained('house_conditions')
                  ->nullOnDelete();
        });

        // 3. Migrate existing text values → FK IDs
        $map = DB::table('house_conditions')->pluck('id', 'name');
        foreach ($map as $name => $id) {
            DB::table('field_visits')
              ->where('house_condition', $name)
              ->update(['house_condition_id' => $id]);
        }

        // 4. Drop old text column
        Schema::table('field_visits', function (Blueprint $table) {
            $table->dropColumn('house_condition');
        });
    }

    public function down(): void {
        // Restore text column
        Schema::table('field_visits', function (Blueprint $table) {
            $table->text('house_condition')->nullable()->after('notes');
        });

        // Back-fill from FK
        $rows = DB::table('field_visits')
            ->join('house_conditions', 'field_visits.house_condition_id', '=', 'house_conditions.id')
            ->select('field_visits.id', 'house_conditions.name')
            ->get();
        foreach ($rows as $row) {
            DB::table('field_visits')->where('id', $row->id)->update(['house_condition' => $row->name]);
        }

        // Drop FK column
        Schema::table('field_visits', function (Blueprint $table) {
            $table->dropForeign(['house_condition_id']);
            $table->dropColumn('house_condition_id');
        });
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Seed initial values from existing data
        $existingValues = DB::table('members')
            ->whereNotNull('housing_status')
            ->where('housing_status', '!=', '')
            ->distinct()
            ->pluck('housing_status');

        foreach ($existingValues as $value) {
            DB::table('housing_statuses')->insertOrIgnore([
                'name'       => $value,
                'color'      => '#059669',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Add FK column
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('housing_status_id')
                  ->nullable()
                  ->after('housing_status')
                  ->constrained('housing_statuses')
                  ->nullOnDelete();
        });

        // Migrate existing string values to FK
        $statuses = DB::table('housing_statuses')->pluck('id', 'name');
        foreach ($statuses as $name => $id) {
            DB::table('members')
                ->where('housing_status', $name)
                ->update(['housing_status_id' => $id]);
        }

        // Drop old string column
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('housing_status');
        });
    }

    public function down(): void {
        Schema::table('members', function (Blueprint $table) {
            $table->string('housing_status', 150)->nullable()->after('housing_status_id');
        });

        // Restore string values from FK
        DB::table('members')
            ->join('housing_statuses', 'members.housing_status_id', '=', 'housing_statuses.id')
            ->update(['members.housing_status' => DB::raw('housing_statuses.name')]);

        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['housing_status_id']);
            $table->dropColumn('housing_status_id');
        });
    }
};

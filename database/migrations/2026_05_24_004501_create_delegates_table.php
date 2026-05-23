<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delegates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Seed existing delegate names from member records
        $existing = DB::table('members')
            ->whereNotNull('delegate')
            ->where('delegate', '!=', '')
            ->distinct()
            ->pluck('delegate');

        foreach ($existing as $name) {
            DB::table('delegates')->insertOrIgnore([
                'name'       => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('delegates');
    }
};

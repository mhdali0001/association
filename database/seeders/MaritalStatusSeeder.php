<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaritalStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('marital_statuses')->insert([
            ['id' => 1, 'name' => 'أرملة',    'is_active' => 1],
            ['id' => 2, 'name' => 'مطلقة',    'is_active' => 1],
            ['id' => 3, 'name' => 'عذباء',    'is_active' => 1],
            ['id' => 4, 'name' => 'متزوج',    'is_active' => 1],
            ['id' => 5, 'name' => 'عازب',     'is_active' => 1],
            ['id' => 6, 'name' => 'مفقود',    'is_active' => 1],
            ['id' => 7, 'name' => 'مسجون',    'is_active' => 1],
            ['id' => 8, 'name' => 'أيتام',    'is_active' => 1],
            ['id' => 9, 'name' => 'متخاذل',   'is_active' => 1],
            ['id' => 10, 'name' => 'متزوجة',   'is_active' => 1],
        ]);
    }
}

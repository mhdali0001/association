<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssociationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('associations')->insert([
            ['id' => 1, 'name' => 'جمعية التميز',          'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'جمعية الغيث',            'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'جمعية معضمية الشام',    'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

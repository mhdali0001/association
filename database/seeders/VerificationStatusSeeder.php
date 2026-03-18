<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerificationStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('verification_statuses')->insert([
            ['id' =>  1, 'name' => 'تم',           'color' => '#eab308', 'is_active' => 1],
            ['id' =>  2, 'name' => 'نقص اوراق',    'color' => '#10b981', 'is_active' => 1],
            ['id' =>  3, 'name' => 'أجاب',         'color' => '#ef4444', 'is_active' => 1],
            ['id' =>  4, 'name' => 'لايجيب',       'color' => '#3b82f6', 'is_active' => 1],
            ['id' =>  5, 'name' => 'مكرر',         'color' => '#6b7280', 'is_active' => 1],
            ['id' =>  6, 'name' => 'تعذر',         'color' => '#8b5cf6', 'is_active' => 1],
            ['id' =>  7, 'name' => 'تقييد',        'color' => '#eab308', 'is_active' => 1],
            ['id' =>  8, 'name' => 'رفض',          'color' => '#10b981', 'is_active' => 1],
            ['id' =>  9, 'name' => 'لايوجد وتس',  'color' => '#ef4444', 'is_active' => 1],
            ['id' => 10, 'name' => 'طلب إلغاء',   'color' => '#3b82f6', 'is_active' => 1],
            ['id' => 11, 'name' => 'تعديل',        'color' => '#6b7280', 'is_active' => 1],
            ['id' => 12, 'name' => 'تحقيق',        'color' => '#eab308', 'is_active' => 1],
            ['id' => 13, 'name' => 'ام ياسين',     'color' => '#8b5cf6', 'is_active' => 1],
        ]);
    }
}

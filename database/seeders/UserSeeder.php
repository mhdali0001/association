<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id'         => 1,
                'name'       => 'أحمد المدير',
                'email'      => 'admin@esam.org',
                'password'   => '$2y$10$x62d7OWeiwXuFwXdfxNY..UoXh8smAYe4PyjMMYJhnFsk1kegro8u',
                'role'       => 'admin',
                'phone'      => '0933123456',
                'created_at' => '2026-03-05 20:27:26',
                'updated_at' => null,
            ],
            [
                'id'         => 2,
                'name'       => 'محمد خالد',
                'email'      => 'm.khaled@esam.org',
                'password'   => '$2y$10$Dj29uMzeCp.M4vqh6A96zu42YuCOpJ1cxrFlvCLhwTyDtOWioXW36',
                'role'       => 'user',
                'phone'      => '0944556677',
                'created_at' => '2026-03-05 20:27:26',
                'updated_at' => null,
            ],
            [
                'id'         => 3,
                'name'       => 'سارة علي',
                'email'      => 'sara.ali@esam.org',
                'password'   => '$2y$10$FzdaX7JFjkt0Jd452LxKOOhxKY5c8gYrFlvCLhwTyDtOWioXW36',
                'role'       => 'user',
                'phone'      => '0988776655',
                'created_at' => '2026-03-05 20:27:26',
                'updated_at' => null,
            ],
        ]);
    }
}

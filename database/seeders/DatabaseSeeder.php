<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MaritalStatusSeeder::class,
            VerificationStatusSeeder::class,
            AssociationSeeder::class,
            MemberSeeder::class,
        ]);
    }
}

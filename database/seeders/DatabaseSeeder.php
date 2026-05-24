<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamamos al seeder de roles para que se ejecute de manera automática
        $this->call([
            RoleSeeder::class,
        ]);
    }
}
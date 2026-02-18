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
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TableSeeder::class,
            CategorySeeder::class,
            MenuItemSeeder::class,
            InventoryItemSeeder::class,
            MenuItemInventorySeeder::class,
            SampleReservationSeeder::class,
            SampleOrderSeeder::class,
        ]);
    }
}

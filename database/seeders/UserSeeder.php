<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $waiterRole = Role::where('name', 'waiter')->first();
        $chefRole = Role::where('name', 'chef')->first();
        $cashierRole = Role::where('name', 'cashier')->first();

        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@restaurant.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567890',
                'role_id' => $adminRole->id,
                'is_active' => true,
            ],
            [
                'name' => 'Restaurant Manager',
                'email' => 'manager@restaurant.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567891',
                'role_id' => $managerRole->id,
                'is_active' => true,
            ],
            [
                'name' => 'John Waiter',
                'email' => 'john.waiter@restaurant.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567892',
                'role_id' => $waiterRole->id,
                'is_active' => true,
            ],
            [
                'name' => 'Sarah Waiter',
                'email' => 'sarah.waiter@restaurant.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567893',
                'role_id' => $waiterRole->id,
                'is_active' => true,
            ],
            [
                'name' => 'Chef Mario',
                'email' => 'chef.mario@restaurant.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567894',
                'role_id' => $chefRole->id,
                'is_active' => true,
            ],
            [
                'name' => 'Chef Anna',
                'email' => 'chef.anna@restaurant.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567895',
                'role_id' => $chefRole->id,
                'is_active' => true,
            ],
            [
                'name' => 'Mike Cashier',
                'email' => 'mike.cashier@restaurant.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567896',
                'role_id' => $cashierRole->id,
                'is_active' => true,
            ]
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}

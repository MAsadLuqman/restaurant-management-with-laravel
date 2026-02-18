<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'System Administrator with full access'
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Restaurant Manager with management privileges'
            ],
            [
                'name' => 'waiter',
                'display_name' => 'Waiter',
                'description' => 'Waiter/Server responsible for taking orders'
            ],
            [
                'name' => 'chef',
                'display_name' => 'Chef',
                'description' => 'Kitchen Chef responsible for food preparation'
            ],
            [
                'name' => 'cashier',
                'display_name' => 'Cashier',
                'description' => 'Cashier responsible for payments'
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'General staff member'
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}

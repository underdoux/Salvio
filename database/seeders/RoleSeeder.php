<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Only create roles if they don't exist
        if (Role::count() === 0) {
            $roles = ['Admin', 'Cashier', 'Sales'];

            foreach ($roles as $role) {
                Role::create([
                    'name' => $role,
                    'guard_name' => 'web'
                ]);
            }
        }
    }
}

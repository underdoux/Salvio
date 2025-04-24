<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $cashier = Role::firstOrCreate(['name' => 'Cashier']);
        $sales = Role::firstOrCreate(['name' => 'Sales']);

        // Define permissions
        $permissions = [
            'manage products',
            'process transactions',
            'view reports',
            'manage commissions',
            'distribute profits',
            'manage users',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $admin->syncPermissions($permissions);
        $cashier->syncPermissions(['process transactions', 'view reports']);
        $sales->syncPermissions(['process transactions', 'view reports']);

        // Assign admin role to admin user
        $adminUser = User::where('email', 'admin@salvio.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($admin);
        }
    }
}

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
            // Admin permissions
            'manage users',
            'manage roles',
            'manage products',
            'manage categories',
            'override price changes',
            'configure commissions',
            'distribute profits',
            'access all reports',
            'view logs',

            // Cashier permissions
            'process transactions',
            'adjust prices within limit',
            'view stock',
            'view sales reports',

            // Sales permissions
            'add products',
            'process orders',
            'adjust prices within limit',
            'view personal commission',
            'view basic reports',

            // Common permissions
            'view dashboard',
            'view notifications',
            'view insights'
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $admin->syncPermissions($permissions); // Admin gets all permissions

        $cashier->syncPermissions([
            'process transactions',
            'adjust prices within limit',
            'view stock',
            'view sales reports',
            'view dashboard',
            'view notifications',
            'view insights'
        ]);

        $sales->syncPermissions([
            'process orders',
            'add products',
            'adjust prices within limit',
            'view personal commission',
            'view basic reports',
            'view dashboard',
            'view notifications',
            'view insights'
        ]);

        // Assign admin role to admin user
        $adminUser = User::where('email', 'admin@salvio.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($admin);
        }
    }
}

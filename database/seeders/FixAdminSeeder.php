<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FixAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Create admin user if it doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@salvio.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
            ]
        );

        // Make sure admin has Admin role
        if (!$admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }
    }
}

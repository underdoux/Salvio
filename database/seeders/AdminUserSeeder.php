<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@salvio.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin123!'),
            ]
        );

        $admin->assignRole('Admin');
    }
}

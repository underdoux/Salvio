<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        
        $admin = User::firstOrCreate(
            ['email' => 'admin@salvio.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin123!'),
            ]
        );

        $admin->roles()->sync([$adminRole->id]);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Console\Command;

class FixRoles extends Command
{
    protected $signature = 'roles:fix';
    protected $description = 'Fix roles and permissions';

    public function handle()
    {
        $this->info('Fixing roles...');

        // Create Admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        // Get admin user
        $admin = User::where('email', 'admin@salvio.com')->first();

        if ($admin) {
            // Clear and reassign role
            $admin->syncRoles([]);
            $admin->assignRole($adminRole);
            $this->info('Admin role assigned successfully.');
        } else {
            $this->error('Admin user not found.');
        }
    }
}

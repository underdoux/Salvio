<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class FixAdminRole extends Command
{
    protected $signature = 'admin:fix-role';
    protected $description = 'Fix admin user role assignment';

    public function handle()
    {
        $this->info('Starting admin role fix...');

        $user = User::where('email', 'admin@salvio.com')->first();
        if (!$user) {
            $this->error('Admin user not found');
            return 1;
        }

        $role = Role::where('name', 'Admin')->first();
        if (!$role) {
            $this->info('Creating admin role...');
            $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        }

        $user->syncRoles([$role]);

        $this->info('Admin role assigned successfully');
        $this->info('Current roles: ' . $user->roles()->pluck('name')->implode(', '));

        return 0;
    }
}

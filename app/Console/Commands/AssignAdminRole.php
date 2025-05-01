<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignAdminRole extends Command
{
    protected $signature = 'role:assign-admin';
    protected $description = 'Assign Admin role to admin user';

    public function handle()
    {
        $user = User::where('email', 'admin@salvio.com')->first();
        $role = Role::where('name', 'Admin')->first();

        if (!$user) {
            $this->error('Admin user not found');
            return;
        }

        if (!$role) {
            $this->error('Admin role not found');
            return;
        }

        $user->syncRoles([$role]);
        $this->info('Admin role assigned successfully');
    }
}

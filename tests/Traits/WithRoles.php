<?php

namespace Tests\Traits;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

trait WithRoles
{
    protected function setupRoles(): void
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Ensure no active transactions
        while (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        // Create roles with web guard
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $salesRole = Role::create(['name' => 'sales', 'guard_name' => 'web']);
        $cashierRole = Role::create(['name' => 'cashier', 'guard_name' => 'web']);

        // Create permissions with web guard
        $permissions = [
            // Dashboard permissions
            'view dashboard',
            'view sales stats',
            'view financial stats',

            // Product permissions
            'view products',
            'create products',
            'edit products',
            'delete products',

            // Order permissions
            'view any orders',
            'view own orders',
            'create orders',
            'update any orders',
            'update own orders',
            'delete any orders',
            'delete own orders',
            'export orders',
            'process any orders',
            'process own orders',
            'cancel any orders',
            'cancel own orders',

            // Commission permissions
            'view any commissions',
            'view own commissions',
            'create commission rules',
            'edit commission rules',
            'delete commission rules',
            'export commissions',

            // Insight permissions
            'view insights',
            'view sales insights',
            'view financial insights',
            'view product insights',
            'export insights',
            'schedule reports',
            'view customer analytics',
            'view commission analytics',
            'customize insights',
            'share insights',
            'create custom reports',
            'manage report schedules'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to admin role
        $adminRole->givePermissionTo($permissions);

        // Assign permissions to sales role
        $salesRole->givePermissionTo([
            'view dashboard',
            'view sales stats',
            'view products',
            'view own orders',
            'create orders',
            'update own orders',
            'delete own orders',
            'process own orders',
            'cancel own orders',
            'view own commissions',
            'view sales insights',
            'view product insights'
        ]);

        // Assign permissions to cashier role
        $cashierRole->givePermissionTo([
            'view dashboard',
            'view products',
            'view own orders',
            'create orders',
            'update own orders',
            'process own orders',
            'view own commissions'
        ]);
    }

    protected function clearRolesAndPermissions(): void
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Ensure no active transactions
        while (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        // Delete all roles and permissions
        DB::table('role_has_permissions')->delete();
        DB::table('model_has_roles')->delete();
        DB::table('model_has_permissions')->delete();
        DB::table('roles')->delete();
        DB::table('permissions')->delete();
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Clear and set up roles and permissions
        $this->clearRolesAndPermissions();
        $this->setupRoles();
    }
}

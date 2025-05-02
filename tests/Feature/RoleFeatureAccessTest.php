<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Tests\Traits\WithRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleFeatureAccessTest extends TestCase
{
    use RefreshDatabase, WithRoles;

    public function test_admin_access_all_features(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertTrue($admin->can('view dashboard'));
        $this->assertTrue($admin->can('view sales stats'));
        $this->assertTrue($admin->can('view financial stats'));
        $this->assertTrue($admin->can('view products'));
        $this->assertTrue($admin->can('create products'));
        $this->assertTrue($admin->can('edit products'));
        $this->assertTrue($admin->can('delete products'));
        $this->assertTrue($admin->can('view any orders'));
        $this->assertTrue($admin->can('create orders'));
        $this->assertTrue($admin->can('update any orders'));
        $this->assertTrue($admin->can('delete any orders'));
        $this->assertTrue($admin->can('export orders'));
        $this->assertTrue($admin->can('view any commissions'));
        $this->assertTrue($admin->can('create commission rules'));
        $this->assertTrue($admin->can('edit commission rules'));
        $this->assertTrue($admin->can('delete commission rules'));
        $this->assertTrue($admin->can('view insights'));
        $this->assertTrue($admin->can('view sales insights'));
        $this->assertTrue($admin->can('view financial insights'));
        $this->assertTrue($admin->can('export insights'));
    }

    public function test_sales_access_limited_features(): void
    {
        /** @var User $sales */
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        // Can access
        $this->assertTrue($sales->can('view dashboard'));
        $this->assertTrue($sales->can('view sales stats'));
        $this->assertTrue($sales->can('view products'));
        $this->assertTrue($sales->can('view own orders'));
        $this->assertTrue($sales->can('create orders'));
        $this->assertTrue($sales->can('update own orders'));
        $this->assertTrue($sales->can('delete own orders'));
        $this->assertTrue($sales->can('view own commissions'));
        $this->assertTrue($sales->can('view sales insights'));

        // Cannot access
        $this->assertFalse($sales->can('view financial stats'));
        $this->assertFalse($sales->can('create products'));
        $this->assertFalse($sales->can('edit products'));
        $this->assertFalse($sales->can('delete products'));
        $this->assertFalse($sales->can('view any orders'));
        $this->assertFalse($sales->can('update any orders'));
        $this->assertFalse($sales->can('delete any orders'));
        $this->assertFalse($sales->can('export orders'));
        $this->assertFalse($sales->can('view any commissions'));
        $this->assertFalse($sales->can('create commission rules'));
        $this->assertFalse($sales->can('edit commission rules'));
        $this->assertFalse($sales->can('delete commission rules'));
        $this->assertFalse($sales->can('view financial insights'));
        $this->assertFalse($sales->can('export insights'));
    }

    public function test_cashier_access_limited_features(): void
    {
        /** @var User $cashier */
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        // Can access
        $this->assertTrue($cashier->can('view dashboard'));
        $this->assertTrue($cashier->can('view products'));
        $this->assertTrue($cashier->can('view own orders'));
        $this->assertTrue($cashier->can('create orders'));
        $this->assertTrue($cashier->can('update own orders'));
        $this->assertTrue($cashier->can('view own commissions'));

        // Cannot access
        $this->assertFalse($cashier->can('view sales stats'));
        $this->assertFalse($cashier->can('view financial stats'));
        $this->assertFalse($cashier->can('create products'));
        $this->assertFalse($cashier->can('edit products'));
        $this->assertFalse($cashier->can('delete products'));
        $this->assertFalse($cashier->can('view any orders'));
        $this->assertFalse($cashier->can('update any orders'));
        $this->assertFalse($cashier->can('delete any orders'));
        $this->assertFalse($cashier->can('export orders'));
        $this->assertFalse($cashier->can('view any commissions'));
        $this->assertFalse($cashier->can('create commission rules'));
        $this->assertFalse($cashier->can('edit commission rules'));
        $this->assertFalse($cashier->can('delete commission rules'));
        $this->assertFalse($cashier->can('view insights'));
        $this->assertFalse($cashier->can('view sales insights'));
        $this->assertFalse($cashier->can('view financial insights'));
        $this->assertFalse($cashier->can('export insights'));
    }
}

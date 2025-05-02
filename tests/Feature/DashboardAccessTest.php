<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Tests\Traits\WithRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase, WithRoles;

    public function test_admin_can_access_full_dashboard(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertSee('Financial Overview');
        $response->assertSee('Sales Analytics');
        $response->assertSee('Product Performance');
    }

    public function test_sales_can_access_limited_dashboard(): void
    {
        /** @var User $sales */
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $response = $this->actingAs($sales)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertSee('Sales Analytics');
        $response->assertSee('Product Performance');
        $response->assertDontSee('Financial Overview');
    }

    public function test_cashier_can_access_basic_dashboard(): void
    {
        /** @var User $cashier */
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $response = $this->actingAs($cashier)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertDontSee('Financial Overview');
        $response->assertDontSee('Sales Analytics');
        $response->assertSee('Recent Orders');
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_all_stats(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/dashboard/stats');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'financial',
            'sales',
            'products',
            'orders'
        ]);
    }

    public function test_sales_can_view_limited_stats(): void
    {
        /** @var User $sales */
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $response = $this->actingAs($sales)->get('/dashboard/stats');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'sales',
            'products',
            'orders'
        ]);
        $response->assertJsonMissing(['financial']);
    }

    public function test_cashier_can_view_basic_stats(): void
    {
        /** @var User $cashier */
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $response = $this->actingAs($cashier)->get('/dashboard/stats');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'orders'
        ]);
        $response->assertJsonMissing([
            'financial',
            'sales',
            'products'
        ]);
    }
}

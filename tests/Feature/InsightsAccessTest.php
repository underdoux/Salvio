<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Tests\Traits\WithRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InsightsAccessTest extends TestCase
{
    use RefreshDatabase, WithRoles;

    public function test_admin_can_access_all_insights(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/insights');

        $response->assertStatus(200);
        $response->assertSee('Sales Insights');
        $response->assertSee('Financial Insights');
        $response->assertSee('Product Performance');
        $response->assertSee('Customer Analytics');
        $response->assertSee('Commission Analytics');
        $response->assertSee('Export Report');
    }

    public function test_sales_can_access_limited_insights(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $response = $this->actingAs($sales)->get('/insights');

        $response->assertStatus(200);
        $response->assertSee('Sales Insights');
        $response->assertSee('Product Performance');
        $response->assertDontSee('Financial Insights');
        $response->assertDontSee('Commission Analytics');
        $response->assertDontSee('Export Report');
    }

    public function test_cashier_cannot_access_insights(): void
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $response = $this->actingAs($cashier)->get('/insights');

        $response->assertForbidden();
    }

    public function test_admin_can_view_financial_insights(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/insights/financial');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'revenue' => [
                'daily',
                'weekly',
                'monthly',
                'yearly'
            ],
            'profit_margins',
            'expenses',
            'top_revenue_sources'
        ]);
    }

    public function test_sales_cannot_view_financial_insights(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $response = $this->actingAs($sales)->get('/insights/financial');

        $response->assertForbidden();
    }

    public function test_admin_can_view_sales_insights(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/insights/sales');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'sales_trends',
            'top_products',
            'sales_by_category',
            'sales_by_location',
            'peak_sales_hours'
        ]);
    }

    public function test_sales_can_view_sales_insights(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $response = $this->actingAs($sales)->get('/insights/sales');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'sales_trends',
            'top_products',
            'sales_by_category'
        ]);
        $response->assertJsonMissing([
            'sales_by_location',
            'peak_sales_hours'
        ]);
    }

    public function test_admin_can_view_product_insights(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/insights/products');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'best_sellers',
            'low_stock_alerts',
            'product_performance',
            'category_performance',
            'profit_margins_by_product'
        ]);
    }

    public function test_sales_can_view_limited_product_insights(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $response = $this->actingAs($sales)->get('/insights/products');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'best_sellers',
            'low_stock_alerts',
            'product_performance'
        ]);
        $response->assertJsonMissing([
            'profit_margins_by_product'
        ]);
    }

    public function test_admin_can_export_insights(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/insights/export', [
            'type' => 'sales',
            'period' => 'monthly'
        ]);

        $response->assertDownload('insights_report.csv');
    }

    public function test_sales_cannot_export_insights(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $response = $this->actingAs($sales)->get('/insights/export', [
            'type' => 'sales',
            'period' => 'monthly'
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_schedule_reports(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/insights/schedule-report', [
            'type' => 'sales',
            'frequency' => 'weekly',
            'email' => 'admin@example.com'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('scheduled_reports', [
            'type' => 'sales',
            'frequency' => 'weekly',
            'email' => 'admin@example.com'
        ]);
    }

    public function test_sales_cannot_schedule_reports(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $response = $this->actingAs($sales)->post('/insights/schedule-report', [
            'type' => 'sales',
            'frequency' => 'weekly',
            'email' => 'sales@example.com'
        ]);

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_insights(): void
    {
        $response = $this->get('/insights');

        $response->assertRedirect('/login');
    }
}

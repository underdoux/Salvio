<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Commission;
use App\Models\CommissionRule;
use App\Models\Order;
use Tests\TestCase;
use Tests\Traits\WithRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommissionManagementAccessTest extends TestCase
{
    use RefreshDatabase, WithRoles;

    public function test_admin_can_view_all_commissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $users = User::factory()->count(3)->create();
        foreach ($users as $user) {
            Commission::factory()->create([
                'user_id' => $user->id
            ]);
        }

        $response = $this->actingAs($admin)->get('/commissions');

        $response->assertStatus(200);
        foreach ($users as $user) {
            $response->assertSee($user->name);
        }
        $response->assertSee('Commission Rules');
        $response->assertSee('Export');
    }

    public function test_sales_can_view_own_commissions(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $otherUser = User::factory()->create();

        $ownCommission = Commission::factory()->create([
            'user_id' => $sales->id
        ]);
        $otherCommission = Commission::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($sales)->get('/commissions');

        $response->assertStatus(200);
        $response->assertSee($ownCommission->amount);
        $response->assertDontSee($otherCommission->amount);
        $response->assertDontSee('Commission Rules');
        $response->assertDontSee('Export');
    }

    public function test_cashier_can_view_own_commissions(): void
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $otherUser = User::factory()->create();

        $ownCommission = Commission::factory()->create([
            'user_id' => $cashier->id
        ]);
        $otherCommission = Commission::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($cashier)->get('/commissions');

        $response->assertStatus(200);
        $response->assertSee($ownCommission->amount);
        $response->assertDontSee($otherCommission->amount);
        $response->assertDontSee('Commission Rules');
        $response->assertDontSee('Export');
    }

    public function test_admin_can_manage_commission_rules(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Create commission rule
        $response = $this->actingAs($admin)->post('/commission-rules', [
            'name' => 'Test Rule',
            'type' => 'percentage',
            'value' => 10,
            'min_amount' => 1000,
            'max_amount' => 5000,
            'role' => 'sales'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('commission_rules', [
            'name' => 'Test Rule',
            'type' => 'percentage',
            'value' => 10
        ]);

        $rule = CommissionRule::where('name', 'Test Rule')->first();

        // Update commission rule
        $response = $this->actingAs($admin)->put("/commission-rules/{$rule->id}", [
            'name' => 'Updated Rule',
            'value' => 15
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('commission_rules', [
            'name' => 'Updated Rule',
            'value' => 15
        ]);

        // Delete commission rule
        $response = $this->actingAs($admin)->delete("/commission-rules/{$rule->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('commission_rules', ['id' => $rule->id]);
    }

    public function test_sales_cannot_manage_commission_rules(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $rule = CommissionRule::factory()->create();

        // Attempt to create rule
        $response = $this->actingAs($sales)->post('/commission-rules', [
            'name' => 'Test Rule',
            'type' => 'percentage',
            'value' => 10
        ]);
        $response->assertForbidden();

        // Attempt to update rule
        $response = $this->actingAs($sales)->put("/commission-rules/{$rule->id}", [
            'value' => 15
        ]);
        $response->assertForbidden();

        // Attempt to delete rule
        $response = $this->actingAs($sales)->delete("/commission-rules/{$rule->id}");
        $response->assertForbidden();
    }

    public function test_admin_can_view_commission_reports(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/commissions/reports');

        $response->assertStatus(200);
        $response->assertSee('Commission Summary');
        $response->assertSee('Total Commissions');
        $response->assertSee('Commission by Role');
        $response->assertSee('Top Earners');
    }

    public function test_sales_cannot_view_commission_reports(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $response = $this->actingAs($sales)->get('/commissions/reports');

        $response->assertForbidden();
    }

    public function test_admin_can_export_commission_data(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/commissions/export');

        $response->assertDownload('commissions.csv');
    }

    public function test_sales_cannot_export_commission_data(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $response = $this->actingAs($sales)->get('/commissions/export');

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_commissions(): void
    {
        $response = $this->get('/commissions');

        $response->assertRedirect('/login');
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfitDistributionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_profit_distributions()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->get(route('profit-distributions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('profit-distributions.index');
    }

    public function test_non_admin_cannot_view_profit_distributions()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profit-distributions.index'));

        $response->assertStatus(403);
    }
}

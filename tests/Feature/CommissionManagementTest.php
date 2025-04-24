<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommissionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_commission_list()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->get(route('commissions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('commissions.index');
    }

    public function test_non_admin_cannot_view_commission_list()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('commissions.index'));

        $response->assertStatus(403);
    }
}

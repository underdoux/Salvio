<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_product_list()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('products.index');
    }

    public function test_non_admin_cannot_view_product_list()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('products.index'));

        $response->assertStatus(403);
    }
}

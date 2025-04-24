<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_order()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $orderData = [
            'tax' => 10,
            'status' => 'new',
            'total' => 100,
            'payment_type' => 'cash',
        ];

        $itemsData = [
            [
                'product_id' => 1,
                'original_price' => 50,
                'adjusted_price' => 45,
                'adjustment_reason' => 'Discount',
                'quantity' => 2,
            ],
        ];

        $response = $this->actingAs($admin)->post(route('orders.store'), [
            'order' => $orderData,
            'items' => $itemsData,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('orders', ['total' => 100]);
    }

    public function test_non_admin_cannot_create_order()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('orders.store'), [
            'order' => [],
            'items' => [],
        ]);

        $response->assertStatus(403);
    }
}

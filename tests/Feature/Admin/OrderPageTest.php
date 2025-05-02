<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Order;
use Tests\TestCase;
use Tests\Traits\WithRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderPageTest extends TestCase
{
    use RefreshDatabase, WithRoles;

    public function test_admin_can_view_all_orders(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        /** @var User $regularUser */
        $regularUser = User::factory()->create();
        $regularUser->assignRole('user');

        $regularUserOrder = Order::factory()->create([
            'user_id' => $regularUser->id
        ]);

        $response = $this->actingAs($admin)->get('/orders');

        $response->assertStatus(200);
        $response->assertSee($regularUserOrder->id);
    }

    public function test_admin_can_filter_orders_by_user(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        /** @var User $user1 */
        $user1 = User::factory()->create();
        $user1->assignRole('user');

        /** @var User $user2 */
        $user2 = User::factory()->create();
        $user2->assignRole('user');

        $user1Order = Order::factory()->create([
            'user_id' => $user1->id
        ]);
        $user2Order = Order::factory()->create([
            'user_id' => $user2->id
        ]);

        $response = $this->actingAs($admin)
            ->get('/orders?user=' . $user1->id);

        $response->assertStatus(200);
        $response->assertSee($user1Order->id);
        $response->assertDontSee($user2Order->id);
    }

    public function test_admin_can_see_user_details_in_orders(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        Order::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($admin)->get('/orders');

        $response->assertStatus(200);
        $response->assertSee('Customer');
        $response->assertSee($user->name);
    }

    public function test_admin_can_update_any_order(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($admin)
            ->patch("/orders/{$order->id}", [
                'status' => 'processing'
            ]);

        $response->assertRedirect();
        $this->assertEquals('processing', $order->fresh()->status);
    }

    public function test_admin_can_delete_any_order(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $order = Order::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($admin)
            ->delete("/orders/{$order->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_admin_can_export_orders(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->get('/orders/export');

        $response->assertDownload('orders.csv');
    }

    public function test_non_admin_cannot_access_admin_features(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        /** @var User $otherUser */
        $otherUser = User::factory()->create();
        $otherUser->assignRole('user');

        $otherUserOrder = Order::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($user)->get('/orders');

        $response->assertStatus(200);
        $response->assertDontSee($otherUserOrder->id);
        $response->assertDontSee('Customer');
        $response->assertDontSee('Export');
    }
}

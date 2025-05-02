<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use Tests\TestCase;
use Illuminate\Http\Request;

class OrderPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_orders_page_requires_authentication(): void
    {
        $response = $this->get('/orders');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_orders_page(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/orders');

        $response->assertStatus(200);
        $response->assertViewIs('orders.index');
    }

    public function test_orders_page_displays_user_orders(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $orders = Order::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get('/orders');

        $response->assertStatus(200);
        foreach ($orders as $order) {
            $response->assertSee($order->id);
            $response->assertSee(number_format($order->total, 2));
            $response->assertSee(ucfirst($order->status));
        }
    }

    public function test_orders_page_has_required_elements(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/orders');

        $response->assertStatus(200);
        $response->assertSee('Orders');
        $response->assertSee('Order ID');
        $response->assertSee('Date');
        $response->assertSee('Total');
        $response->assertSee('Status');
        $response->assertSee('Actions');
    }

    public function test_orders_page_has_pagination(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Order::factory()->count(15)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get('/orders');

        $response->assertStatus(200);
        $response->assertSee('Next');
    }

    public function test_orders_can_be_filtered_by_status(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $pendingOrder = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);
        $completedOrder = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($user)
            ->get('/orders?status=pending');

        $response->assertStatus(200);
        $response->assertSee($pendingOrder->id);
        $response->assertDontSee($completedOrder->id);
    }

    public function test_orders_can_be_sorted(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $oldOrder = Order::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(5)
        ]);
        $newOrder = Order::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()
        ]);

        $response = $this->actingAs($user)
            ->get('/orders?sort=created_at&direction=desc');

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $newOrder->id,
            $oldOrder->id
        ]);
    }

    public function test_orders_can_be_searched(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $orderWithSearchTerm = Order::factory()->create([
            'user_id' => $user->id,
            'id' => 'ORD-123-ABC'
        ]);
        $orderWithoutSearchTerm = Order::factory()->create([
            'user_id' => $user->id,
            'id' => 'ORD-456-XYZ'
        ]);

        $response = $this->actingAs($user)
            ->get('/orders?search=123');

        $response->assertStatus(200);
        $response->assertSee($orderWithSearchTerm->id);
        $response->assertDontSee($orderWithoutSearchTerm->id);
    }

    public function test_user_can_only_see_their_own_orders(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        /** @var User $user2 */
        $user2 = User::factory()->create();

        $user1Order = Order::factory()->create([
            'user_id' => $user1->id
        ]);
        $user2Order = Order::factory()->create([
            'user_id' => $user2->id
        ]);

        $response = $this->actingAs($user1)->get('/orders');

        $response->assertStatus(200);
        $response->assertSee($user1Order->id);
        $response->assertDontSee($user2Order->id);
    }

    public function test_orders_page_shows_empty_state(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/orders');

        $response->assertStatus(200);
        $response->assertSee('No orders found');
    }
}

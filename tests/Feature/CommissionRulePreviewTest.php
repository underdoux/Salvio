<?php

namespace Tests\Feature;

use App\Models\CommissionRule;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommissionRulePreviewTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Authenticatable $admin;
    protected CommissionRule $rule;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);

        // Create commission rule
        $this->rule = CommissionRule::factory()->create([
            'name' => 'Test Rule',
            'type' => 'percentage',
            'value' => 10,
            'active' => true,
            'conditions' => [
                'minimum_order_amount' => 100,
                'product_categories' => ['electronics'],
            ],
        ]);

        // Create test order
        $this->order = Order::factory()->create([
            'total_amount' => 200,
            'category' => 'electronics',
        ]);
    }

    /** @test */
    public function it_shows_preview_page()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.preview.show', $this->rule));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.preview')
            ->assertViewHas('rule')
            ->assertSee($this->rule->name);
    }

    /** @test */
    public function it_simulates_rule_with_matching_conditions()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.preview.simulate', $this->rule), [
                'order_amount' => 200,
                'product_category' => 'electronics',
                'customer_group' => 'regular',
                'payment_method' => 'credit_card',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'applicable' => true,
                'commission_amount' => 20, // 10% of 200
            ]);
    }

    /** @test */
    public function it_simulates_rule_with_non_matching_conditions()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.preview.simulate', $this->rule), [
                'order_amount' => 50, // Below minimum
                'product_category' => 'electronics',
                'customer_group' => 'regular',
                'payment_method' => 'credit_card',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'applicable' => false,
                'reason' => 'Order amount below minimum requirement',
            ]);
    }

    /** @test */
    public function it_simulates_rule_with_fixed_amount()
    {
        $fixedRule = CommissionRule::factory()->create([
            'type' => 'fixed',
            'value' => 25,
            'active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.preview.simulate', $fixedRule), [
                'order_amount' => 200,
                'product_category' => 'electronics',
                'customer_group' => 'regular',
                'payment_method' => 'credit_card',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'applicable' => true,
                'commission_amount' => 25,
            ]);
    }

    /** @test */
    public function it_simulates_rule_with_date_restrictions()
    {
        $dateRestrictedRule = CommissionRule::factory()->create([
            'type' => 'percentage',
            'value' => 10,
            'active' => true,
            'effective_from' => now()->addDay(),
            'effective_until' => now()->addDays(30),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.preview.simulate', $dateRestrictedRule), [
                'order_amount' => 200,
                'product_category' => 'electronics',
                'customer_group' => 'regular',
                'payment_method' => 'credit_card',
                'order_date' => now()->toDateString(),
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'applicable' => false,
                'reason' => 'Rule not yet effective',
            ]);
    }

    /** @test */
    public function it_simulates_rule_with_customer_group_restrictions()
    {
        $groupRestrictedRule = CommissionRule::factory()->create([
            'type' => 'percentage',
            'value' => 10,
            'active' => true,
            'conditions' => [
                'customer_groups' => ['vip'],
            ],
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.preview.simulate', $groupRestrictedRule), [
                'order_amount' => 200,
                'product_category' => 'electronics',
                'customer_group' => 'regular',
                'payment_method' => 'credit_card',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'applicable' => false,
                'reason' => 'Customer group not eligible',
            ]);
    }

    /** @test */
    public function it_simulates_rule_with_payment_method_restrictions()
    {
        $paymentRestrictedRule = CommissionRule::factory()->create([
            'type' => 'percentage',
            'value' => 10,
            'active' => true,
            'conditions' => [
                'payment_methods' => ['credit_card'],
            ],
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.preview.simulate', $paymentRestrictedRule), [
                'order_amount' => 200,
                'product_category' => 'electronics',
                'customer_group' => 'regular',
                'payment_method' => 'cash',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'applicable' => false,
                'reason' => 'Payment method not eligible',
            ]);
    }
}

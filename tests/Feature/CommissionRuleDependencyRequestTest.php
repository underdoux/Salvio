<?php

namespace Tests\Feature;

use App\Models\CommissionRule;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionRuleDependencyRequestTest extends TestCase
{
    use RefreshDatabase;

    protected Authenticatable $admin;
    protected CommissionRule $ruleA;
    protected CommissionRule $ruleB;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);

        // Create commission rules
        $this->ruleA = CommissionRule::factory()->create([
            'name' => 'Rule A',
            'type' => 'percentage',
            'value' => 10,
            'active' => true,
        ]);

        $this->ruleB = CommissionRule::factory()->create([
            'name' => 'Rule B',
            'type' => 'percentage',
            'value' => 15,
            'active' => true,
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.dependencies.store', $this->ruleA), []);

        $response->assertRedirect()
            ->assertSessionHasErrors(['depends_on_rule_id', 'dependency_type', 'reason']);
    }

    /** @test */
    public function it_validates_dependency_type()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.dependencies.store', $this->ruleA), [
                'depends_on_rule_id' => $this->ruleB->id,
                'dependency_type' => 'invalid',
                'reason' => 'Testing invalid dependency type',
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('dependency_type');
    }

    /** @test */
    public function it_prevents_self_dependency()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.dependencies.store', $this->ruleA), [
                'depends_on_rule_id' => $this->ruleA->id,
                'dependency_type' => 'requires',
                'reason' => 'Testing self dependency',
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('depends_on_rule_id');
    }

    /** @test */
    public function it_prevents_circular_dependency()
    {
        // Create initial dependency B -> A
        $this->actingAs($this->admin)
            ->post(route('commission-rules.dependencies.store', $this->ruleB), [
                'depends_on_rule_id' => $this->ruleA->id,
                'dependency_type' => 'requires',
                'reason' => 'Initial dependency',
            ]);

        // Attempt to create circular dependency A -> B
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.dependencies.store', $this->ruleA), [
                'depends_on_rule_id' => $this->ruleB->id,
                'dependency_type' => 'requires',
                'reason' => 'Testing circular dependency',
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('depends_on_rule_id');
    }

    /** @test */
    public function it_validates_reason_length()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.dependencies.store', $this->ruleA), [
                'depends_on_rule_id' => $this->ruleB->id,
                'dependency_type' => 'requires',
                'reason' => str_repeat('a', 256), // Exceeds max length
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('reason');
    }

    /** @test */
    public function it_allows_valid_dependency()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.dependencies.store', $this->ruleA), [
                'depends_on_rule_id' => $this->ruleB->id,
                'dependency_type' => 'requires',
                'reason' => 'Valid dependency test',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('commission_rule_dependencies', [
            'commission_rule_id' => $this->ruleA->id,
            'depends_on_rule_id' => $this->ruleB->id,
            'dependency_type' => 'requires',
            'reason' => 'Valid dependency test',
        ]);
    }
}

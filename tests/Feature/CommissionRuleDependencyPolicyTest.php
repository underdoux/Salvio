<?php

namespace Tests\Feature;

use App\Models\CommissionRule;
use App\Models\CommissionRuleDependency;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionRuleDependencyPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected Authenticatable $admin;
    protected Authenticatable $user;
    protected CommissionRule $ruleA;
    protected CommissionRule $ruleB;
    protected CommissionRuleDependency $dependency;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);

        $this->user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'role' => 'user'
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

        // Create dependency
        $this->dependency = CommissionRuleDependency::factory()->create([
            'commission_rule_id' => $this->ruleA->id,
            'depends_on_rule_id' => $this->ruleB->id,
            'dependency_type' => 'requires',
            'reason' => 'Test dependency',
        ]);
    }

    /** @test */
    public function admin_can_view_dependencies()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.dependencies.index', $this->ruleA));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.dependencies.index')
            ->assertViewHas('rule')
            ->assertSee($this->ruleB->name);
    }

    /** @test */
    public function regular_user_cannot_view_dependencies()
    {
        $response = $this->actingAs($this->user)
            ->get(route('commission-rules.dependencies.index', $this->ruleA));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_dependency()
    {
        $ruleC = CommissionRule::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.dependencies.store', $this->ruleA), [
                'depends_on_rule_id' => $ruleC->id,
                'dependency_type' => 'requires',
                'reason' => 'New test dependency',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('commission_rule_dependencies', [
            'commission_rule_id' => $this->ruleA->id,
            'depends_on_rule_id' => $ruleC->id,
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_dependency()
    {
        $ruleC = CommissionRule::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('commission-rules.dependencies.store', $this->ruleA), [
                'depends_on_rule_id' => $ruleC->id,
                'dependency_type' => 'requires',
                'reason' => 'New test dependency',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('commission_rule_dependencies', [
            'commission_rule_id' => $this->ruleA->id,
            'depends_on_rule_id' => $ruleC->id,
        ]);
    }

    /** @test */
    public function admin_can_delete_dependency()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('commission-rules.dependencies.destroy', [
                'commissionRule' => $this->ruleA,
                'dependency' => $this->dependency,
            ]));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('commission_rule_dependencies', [
            'id' => $this->dependency->id,
        ]);
    }

    /** @test */
    public function regular_user_cannot_delete_dependency()
    {
        $response = $this->actingAs($this->user)
            ->delete(route('commission-rules.dependencies.destroy', [
                'commissionRule' => $this->ruleA,
                'dependency' => $this->dependency,
            ]));

        $response->assertStatus(403);

        $this->assertDatabaseHas('commission_rule_dependencies', [
            'id' => $this->dependency->id,
        ]);
    }

    /** @test */
    public function admin_can_view_dependency_graph()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.dependencies.graph', $this->ruleA));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.dependencies.graph')
            ->assertViewHas(['rule', 'nodes', 'edges']);
    }

    /** @test */
    public function regular_user_cannot_view_dependency_graph()
    {
        $response = $this->actingAs($this->user)
            ->get(route('commission-rules.dependencies.graph', $this->ruleA));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_analyze_dependencies()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.dependencies.analyze', $this->ruleA));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.dependencies.analyze')
            ->assertViewHas(['rule', 'analysis']);
    }

    /** @test */
    public function regular_user_cannot_analyze_dependencies()
    {
        $response = $this->actingAs($this->user)
            ->get(route('commission-rules.dependencies.analyze', $this->ruleA));

        $response->assertStatus(403);
    }
}

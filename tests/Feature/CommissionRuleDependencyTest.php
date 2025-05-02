<?php

namespace Tests\Feature;

use App\Models\CommissionRule;
use App\Models\CommissionRuleDependency;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommissionRuleDependencyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Authenticatable $admin;
    protected CommissionRule $ruleA;
    protected CommissionRule $ruleB;
    protected CommissionRuleDependency $dependency;

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

        // Create dependency
        $this->dependency = CommissionRuleDependency::factory()->create([
            'commission_rule_id' => $this->ruleA->id,
            'depends_on_rule_id' => $this->ruleB->id,
            'dependency_type' => 'requires',
            'reason' => $this->faker->sentence(),
        ]);
    }

    /** @test */
    public function it_lists_dependencies()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.dependencies.index', $this->ruleA));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.dependencies.index')
            ->assertViewHas('rule')
            ->assertViewHas('dependencies')
            ->assertViewHas('dependents')
            ->assertSee($this->ruleB->name);
    }

    /** @test */
    public function it_shows_dependency_graph()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.dependencies.graph', $this->ruleA));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.dependencies.graph')
            ->assertViewHas('rule')
            ->assertViewHas('nodes')
            ->assertViewHas('edges')
            ->assertSee($this->ruleA->name)
            ->assertSee($this->ruleB->name);
    }

    /** @test */
    public function it_shows_dependency_analysis()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.dependencies.analyze', $this->ruleA));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.dependencies.analyze')
            ->assertViewHas('rule')
            ->assertViewHas('analysis')
            ->assertSee($this->ruleA->name);
    }

    /** @test */
    public function it_adds_dependency()
    {
        $ruleC = CommissionRule::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.dependencies.store', $this->ruleA), [
                'depends_on_rule_id' => $ruleC->id,
                'dependency_type' => 'requires',
                'reason' => $this->faker->sentence(),
            ]);

        $response->assertRedirect(route('commission-rules.dependencies.index', $this->ruleA))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('commission_rule_dependencies', [
            'commission_rule_id' => $this->ruleA->id,
            'depends_on_rule_id' => $ruleC->id,
            'dependency_type' => 'requires',
        ]);
    }

    /** @test */
    public function it_prevents_circular_dependencies()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.dependencies.store', $this->ruleB), [
                'depends_on_rule_id' => $this->ruleA->id,
                'dependency_type' => 'requires',
                'reason' => $this->faker->sentence(),
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors();

        $this->assertDatabaseMissing('commission_rule_dependencies', [
            'commission_rule_id' => $this->ruleB->id,
            'depends_on_rule_id' => $this->ruleA->id,
        ]);
    }

    /** @test */
    public function it_removes_dependency()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('commission-rules.dependencies.destroy', [
                'commissionRule' => $this->ruleA,
                'dependency' => $this->dependency,
            ]));

        $response->assertRedirect(route('commission-rules.dependencies.index', $this->ruleA))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('commission_rule_dependencies', [
            'id' => $this->dependency->id,
        ]);
    }

    /** @test */
    public function it_validates_dependency_status()
    {
        $this->ruleB->update(['active' => false]);

        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.dependencies.validate', $this->ruleA));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->ruleA->refresh();
        $this->assertFalse($this->ruleA->active);
    }
}

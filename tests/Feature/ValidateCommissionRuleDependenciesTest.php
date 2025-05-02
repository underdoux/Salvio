<?php

namespace Tests\Feature;

use App\Models\CommissionRule;
use App\Models\CommissionRuleConflict;
use App\Models\CommissionRuleDependency;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidateCommissionRuleDependenciesTest extends TestCase
{
    use RefreshDatabase;

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
        ]);
    }

    /** @test */
    public function it_validates_dependencies_on_rule_update()
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('commission-rules.update', $this->ruleA), [
                'name' => 'Updated Rule A',
                'type' => 'percentage',
                'value' => 20,
                'active' => true,
                'change_reason' => 'Testing dependency validation',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->ruleA->refresh();
        $this->assertEquals('Updated Rule A', $this->ruleA->name);
        $this->assertEquals(20, $this->ruleA->value);
    }

    /** @test */
    public function it_deactivates_dependent_rule_when_required_rule_is_deactivated()
    {
        $this->ruleB->update(['active' => false]);

        $this->ruleA->refresh();
        $this->assertFalse($this->ruleA->active);
    }

    /** @test */
    public function it_prevents_activation_when_required_rule_is_inactive()
    {
        $this->ruleB->update(['active' => false]);
        $this->ruleA->update(['active' => false]);

        $response = $this->actingAs($this->admin)
            ->patch(route('commission-rules.update', $this->ruleA), [
                'name' => 'Rule A',
                'type' => 'percentage',
                'value' => 10,
                'active' => true,
                'change_reason' => 'Attempting to activate with inactive dependency',
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors();

        $this->ruleA->refresh();
        $this->assertFalse($this->ruleA->active);
    }

    /** @test */
    public function it_validates_circular_dependencies()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.dependencies.store', $this->ruleB), [
                'depends_on_rule_id' => $this->ruleA->id,
                'dependency_type' => 'requires',
                'reason' => 'Testing circular dependency prevention',
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors();

        $this->assertDatabaseMissing('commission_rule_dependencies', [
            'commission_rule_id' => $this->ruleB->id,
            'depends_on_rule_id' => $this->ruleA->id,
        ]);
    }

    /** @test */
    public function it_validates_date_overlaps()
    {
        $this->ruleA->update([
            'effective_from' => now(),
            'effective_until' => now()->addDays(30),
        ]);

        $this->ruleB->update([
            'effective_from' => now()->addDays(15),
            'effective_until' => now()->addDays(45),
        ]);

        $conflict = CommissionRuleConflict::factory()->create([
            'rule_a_id' => $this->ruleA->id,
            'rule_b_id' => $this->ruleB->id,
            'conflict_type' => 'date_overlap',
            'details' => [
                'overlap_start' => now()->addDays(15)->toDateTimeString(),
                'overlap_end' => now()->addDays(30)->toDateTimeString(),
            ],
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('commission-rules.update', $this->ruleA), [
                'name' => 'Rule A',
                'type' => 'percentage',
                'value' => 10,
                'active' => true,
                'effective_from' => now()->toDateString(),
                'effective_until' => now()->addDays(30)->toDateString(),
                'change_reason' => 'Testing date overlap validation',
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors();
    }
}

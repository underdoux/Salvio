<?php

namespace Tests\Feature;

use App\Models\CommissionRule;
use App\Models\CommissionRuleConflict;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionRuleConflictTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected CommissionRule $ruleA;
    protected CommissionRule $ruleB;
    protected CommissionRuleConflict $conflict;

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

        // Create conflict
        $this->conflict = CommissionRuleConflict::factory()->create([
            'rule_a_id' => $this->ruleA->id,
            'rule_b_id' => $this->ruleB->id,
            'conflict_type' => 'value_conflict',
            'details' => [
                'condition_overlap' => [],
                'date_overlap' => [
                    'start' => now()->toDateTimeString(),
                    'end' => now()->addDays(30)->toDateTimeString(),
                ],
                'value_difference' => 5,
            ],
        ]);
    }

    /** @test */
    public function it_lists_conflicts()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.conflicts.index'));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.conflicts.index')
            ->assertViewHas('conflicts')
            ->assertSee($this->ruleA->name)
            ->assertSee($this->ruleB->name);
    }

    /** @test */
    public function it_shows_conflict_details()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.conflicts.show', $this->conflict));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.conflicts.show')
            ->assertViewHas('conflict')
            ->assertViewHas('suggestions')
            ->assertSee($this->ruleA->name)
            ->assertSee($this->ruleB->name);
    }

    /** @test */
    public function it_resolves_conflict_with_condition_adjustment()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.conflicts.resolve', $this->conflict), [
                'resolution_type' => 'adjust_conditions',
                'resolution_data' => [
                    'rule_a_conditions' => ['minimum_order_amount' => 100],
                    'rule_b_conditions' => ['minimum_order_amount' => 200],
                ],
                'notes' => 'Adjusted conditions to avoid overlap',
            ]);

        $response->assertRedirect(route('commission-rules.conflicts.index'))
            ->assertSessionHas('success');

        $this->conflict->refresh();
        $this->assertTrue($this->conflict->resolved);
        $this->assertNotNull($this->conflict->resolved_at);
        $this->assertEquals($this->admin->id, $this->conflict->resolved_by);
    }

    /** @test */
    public function it_resolves_conflict_with_value_adjustment()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.conflicts.resolve', $this->conflict), [
                'resolution_type' => 'adjust_values',
                'resolution_data' => [
                    'rule_a_value' => 10,
                    'rule_b_value' => 10,
                ],
                'notes' => 'Adjusted values to be consistent',
            ]);

        $response->assertRedirect(route('commission-rules.conflicts.index'))
            ->assertSessionHas('success');

        $this->conflict->refresh();
        $this->assertTrue($this->conflict->resolved);
        $this->assertNotNull($this->conflict->resolved_at);
        $this->assertEquals($this->admin->id, $this->conflict->resolved_by);
    }

    /** @test */
    public function it_resolves_conflict_with_date_adjustment()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.conflicts.resolve', $this->conflict), [
                'resolution_type' => 'adjust_dates',
                'resolution_data' => [
                    'rule_a_effective_from' => now()->toDateString(),
                    'rule_a_effective_until' => now()->addDays(15)->toDateString(),
                    'rule_b_effective_from' => now()->addDays(16)->toDateString(),
                    'rule_b_effective_until' => now()->addDays(30)->toDateString(),
                ],
                'notes' => 'Adjusted dates to avoid overlap',
            ]);

        $response->assertRedirect(route('commission-rules.conflicts.index'))
            ->assertSessionHas('success');

        $this->conflict->refresh();
        $this->assertTrue($this->conflict->resolved);
        $this->assertNotNull($this->conflict->resolved_at);
        $this->assertEquals($this->admin->id, $this->conflict->resolved_by);
    }

    /** @test */
    public function it_detects_conflicts_for_all_rules()
    {
        CommissionRuleConflict::query()->delete();

        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.conflicts.detect-all'));

        $response->assertRedirect(route('commission-rules.conflicts.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('commission_rule_conflicts', [
            'rule_a_id' => $this->ruleA->id,
            'rule_b_id' => $this->ruleB->id,
        ]);
    }

    /** @test */
    public function it_detects_conflicts_for_specific_rule()
    {
        CommissionRuleConflict::query()->delete();

        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.conflicts.detect', $this->ruleA));

        $response->assertRedirect(route('commission-rules.conflicts.index', ['rule' => $this->ruleA->id]))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('commission_rule_conflicts', [
            'rule_a_id' => $this->ruleA->id,
            'rule_b_id' => $this->ruleB->id,
        ]);
    }
}

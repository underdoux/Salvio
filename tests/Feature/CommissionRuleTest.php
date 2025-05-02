<?php

namespace Tests\Feature;

use App\Models\CommissionRule;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommissionRuleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Authenticatable $admin;
    protected CommissionRule $rule;

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
        ]);
    }

    /** @test */
    public function it_lists_commission_rules()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.index'));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.index')
            ->assertViewHas('rules')
            ->assertSee($this->rule->name);
    }

    /** @test */
    public function it_shows_create_form()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.create'));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.create')
            ->assertViewHas('templates');
    }

    /** @test */
    public function it_creates_commission_rule()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.store'), [
                'name' => 'New Rule',
                'type' => 'percentage',
                'value' => 15,
                'active' => true,
                'conditions' => [
                    'minimum_order_amount' => 100,
                ],
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('commission_rules', [
            'name' => 'New Rule',
            'type' => 'percentage',
            'value' => 15,
            'active' => true,
        ]);
    }

    /** @test */
    public function it_shows_edit_form()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.edit', $this->rule));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.edit')
            ->assertViewHas('commissionRule')
            ->assertSee($this->rule->name);
    }

    /** @test */
    public function it_updates_commission_rule()
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('commission-rules.update', $this->rule), [
                'name' => 'Updated Rule',
                'type' => 'percentage',
                'value' => 20,
                'active' => true,
                'change_reason' => 'Testing update',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->rule->refresh();
        $this->assertEquals('Updated Rule', $this->rule->name);
        $this->assertEquals(20, $this->rule->value);
    }

    /** @test */
    public function it_deletes_commission_rule()
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('commission-rules.destroy', $this->rule));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('commission_rules', [
            'id' => $this->rule->id,
        ]);
    }

    /** @test */
    public function it_duplicates_commission_rule()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.duplicate', $this->rule));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('commission_rules', [
            'name' => $this->rule->name . ' (Copy)',
            'type' => $this->rule->type,
            'value' => $this->rule->value,
            'active' => false,
        ]);
    }

    /** @test */
    public function it_saves_rule_as_template()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.save-as-template', $this->rule), [
                'template_name' => 'Template Rule',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('commission_rules', [
            'name' => 'Template Rule',
            'type' => $this->rule->type,
            'value' => $this->rule->value,
            'is_template' => true,
            'active' => false,
        ]);
    }

    /** @test */
    public function it_creates_rule_from_template()
    {
        $template = CommissionRule::factory()->create([
            'name' => 'Template Rule',
            'type' => 'percentage',
            'value' => 10,
            'is_template' => true,
            'active' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.create-from-template', $template));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('commission_rules', [
            'name' => 'Template Rule (From Template)',
            'type' => $template->type,
            'value' => $template->value,
            'is_template' => false,
            'active' => false,
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.store'), []);

        $response->assertRedirect()
            ->assertSessionHasErrors(['name', 'type', 'value']);
    }

    /** @test */
    public function it_validates_commission_type()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.store'), [
                'name' => 'Invalid Rule',
                'type' => 'invalid',
                'value' => 10,
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('type');
    }

    /** @test */
    public function it_validates_commission_value()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.store'), [
                'name' => 'Invalid Rule',
                'type' => 'percentage',
                'value' => -10,
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('value');
    }

    /** @test */
    public function it_validates_conditions_format()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.store'), [
                'name' => 'Invalid Rule',
                'type' => 'percentage',
                'value' => 10,
                'conditions' => 'invalid',
            ]);

        $response->assertRedirect()
            ->assertSessionHasErrors('conditions');
    }
}

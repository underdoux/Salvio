<?php

namespace Tests\Feature;

use App\Models\CommissionRule;
use App\Models\CommissionRuleVersion;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionRuleVersioningTest extends TestCase
{
    use RefreshDatabase;

    protected Authenticatable $admin;
    protected CommissionRule $rule;
    protected CommissionRuleVersion $version;

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

        // Create initial version
        $this->version = $this->rule->createVersion('Initial version');
    }

    /** @test */
    public function it_lists_versions()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.versions.index', $this->rule));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.versions.index')
            ->assertViewHas('rule')
            ->assertViewHas('versions')
            ->assertSee('Initial version');
    }

    /** @test */
    public function it_shows_version_details()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.versions.show', [
                'commissionRule' => $this->rule,
                'version' => $this->version,
            ]));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.versions.show')
            ->assertViewHas('rule')
            ->assertViewHas('version')
            ->assertSee($this->rule->name)
            ->assertSee('Initial version');
    }

    /** @test */
    public function it_creates_new_version_on_update()
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('commission-rules.update', $this->rule), [
                'name' => 'Updated Rule',
                'type' => 'percentage',
                'value' => 15,
                'active' => true,
                'change_reason' => 'Testing version creation',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->rule->refresh();
        $this->assertEquals('Updated Rule', $this->rule->name);
        $this->assertEquals(15, $this->rule->value);

        $this->assertDatabaseCount('commission_rule_versions', 2);
        $this->assertDatabaseHas('commission_rule_versions', [
            'commission_rule_id' => $this->rule->id,
            'version_number' => 2,
            'change_reason' => 'Testing version creation',
        ]);
    }

    /** @test */
    public function it_compares_versions()
    {
        // Create a second version
        $this->rule->update([
            'name' => 'Updated Rule',
            'value' => 15,
        ]);
        $newVersion = $this->rule->createVersion('Updated version');

        $response = $this->actingAs($this->admin)
            ->get(route('commission-rules.versions.compare', [
                'commissionRule' => $this->rule,
                'versionA' => $this->version,
                'versionB' => $newVersion,
            ]));

        $response->assertStatus(200)
            ->assertViewIs('commission-rules.versions.compare')
            ->assertViewHas('rule')
            ->assertViewHas('versionA')
            ->assertViewHas('versionB')
            ->assertViewHas('differences')
            ->assertSee('Test Rule')
            ->assertSee('Updated Rule');
    }

    /** @test */
    public function it_restores_previous_version()
    {
        // Create a second version with changes
        $this->rule->update([
            'name' => 'Updated Rule',
            'value' => 15,
        ]);
        $this->rule->createVersion('Updated version');

        // Attempt to restore the original version
        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.versions.restore', [
                'commissionRule' => $this->rule,
                'version' => $this->version,
            ]), [
                'change_reason' => 'Restoring original version',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->rule->refresh();
        $this->assertEquals('Test Rule', $this->rule->name);
        $this->assertEquals(10, $this->rule->value);

        // Verify a new version was created for the restoration
        $this->assertDatabaseCount('commission_rule_versions', 3);
        $this->assertDatabaseHas('commission_rule_versions', [
            'commission_rule_id' => $this->rule->id,
            'version_number' => 3,
            'change_reason' => 'Restoring original version',
        ]);
    }

    /** @test */
    public function it_prevents_restoring_version_from_different_rule()
    {
        $otherRule = CommissionRule::factory()->create();
        $otherVersion = $otherRule->createVersion('Other rule version');

        $response = $this->actingAs($this->admin)
            ->post(route('commission-rules.versions.restore', [
                'commissionRule' => $this->rule,
                'version' => $otherVersion,
            ]), [
                'change_reason' => 'Attempting invalid restore',
            ]);

        $response->assertStatus(404);

        $this->rule->refresh();
        $this->assertEquals('Test Rule', $this->rule->name);
    }
}

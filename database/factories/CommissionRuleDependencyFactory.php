<?php

namespace Database\Factories;

use App\Models\CommissionRule;
use App\Models\CommissionRuleDependency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommissionRuleDependencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CommissionRuleDependency::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'commission_rule_id' => CommissionRule::factory(),
            'depends_on_rule_id' => CommissionRule::factory(),
            'dependency_type' => $this->faker->randomElement(['requires', 'conflicts', 'overrides']),
            'reason' => $this->faker->sentence(),
        ];
    }

    /**
     * Indicate that the dependency is a requirement.
     */
    public function requires(): static
    {
        return $this->state(fn (array $attributes) => [
            'dependency_type' => 'requires',
            'reason' => 'Rule requires the conditions of the dependent rule to be met',
        ]);
    }

    /**
     * Indicate that the dependency is a conflict.
     */
    public function conflicts(): static
    {
        return $this->state(fn (array $attributes) => [
            'dependency_type' => 'conflicts',
            'reason' => 'Rule conflicts with the dependent rule and cannot be active simultaneously',
        ]);
    }

    /**
     * Indicate that the dependency is an override.
     */
    public function overrides(): static
    {
        return $this->state(fn (array $attributes) => [
            'dependency_type' => 'overrides',
            'reason' => 'Rule overrides the dependent rule when conditions overlap',
        ]);
    }

    /**
     * Set specific rules for the dependency.
     */
    public function betweenRules(CommissionRule $rule, CommissionRule $dependsOn): static
    {
        return $this->state(fn (array $attributes) => [
            'commission_rule_id' => $rule->id,
            'depends_on_rule_id' => $dependsOn->id,
        ]);
    }

    /**
     * Set a specific reason for the dependency.
     */
    public function withReason(string $reason): static
    {
        return $this->state(fn (array $attributes) => [
            'reason' => $reason,
        ]);
    }

    /**
     * Create a chain of dependencies.
     *
     * @param int $count Number of rules in the chain
     * @param string $type Type of dependency
     * @return array<CommissionRule> The rules in the chain
     */
    public function createDependencyChain(int $count, string $type = 'requires'): array
    {
        $rules = CommissionRule::factory()->count($count)->create();
        $chain = [];

        for ($i = 0; $i < $count - 1; $i++) {
            $this->create([
                'commission_rule_id' => $rules[$i]->id,
                'depends_on_rule_id' => $rules[$i + 1]->id,
                'dependency_type' => $type,
                'reason' => "Part of dependency chain at position {$i}",
            ]);
            $chain[] = $rules[$i];
        }
        $chain[] = $rules[$count - 1];

        return $chain;
    }

    /**
     * Create a circular dependency between rules.
     *
     * @param int $count Number of rules in the circle
     * @param string $type Type of dependency
     * @return array<CommissionRule> The rules in the circle
     */
    public function createCircularDependency(int $count, string $type = 'requires'): array
    {
        $rules = CommissionRule::factory()->count($count)->create();
        $circle = [];

        for ($i = 0; $i < $count; $i++) {
            $nextIndex = ($i + 1) % $count;
            $this->create([
                'commission_rule_id' => $rules[$i]->id,
                'depends_on_rule_id' => $rules[$nextIndex]->id,
                'dependency_type' => $type,
                'reason' => "Part of circular dependency at position {$i}",
            ]);
            $circle[] = $rules[$i];
        }

        return $circle;
    }

    /**
     * Create a complex dependency graph.
     *
     * @param int $count Number of rules in the graph
     * @return array<CommissionRule> The rules in the graph
     */
    public function createDependencyGraph(int $count): array
    {
        $rules = CommissionRule::factory()->count($count)->create();
        $graph = [];

        // Create a mix of dependencies
        for ($i = 0; $i < $count; $i++) {
            for ($j = 0; $j < $count; $j++) {
                if ($i !== $j && $this->faker->boolean(30)) {
                    $this->create([
                        'commission_rule_id' => $rules[$i]->id,
                        'depends_on_rule_id' => $rules[$j]->id,
                        'dependency_type' => $this->faker->randomElement(['requires', 'conflicts', 'overrides']),
                        'reason' => "Part of dependency graph between rule {$i} and {$j}",
                    ]);
                }
            }
            $graph[] = $rules[$i];
        }

        return $graph;
    }
}

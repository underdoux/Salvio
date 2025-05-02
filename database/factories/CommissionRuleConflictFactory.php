<?php

namespace Database\Factories;

use App\Models\CommissionRule;
use App\Models\CommissionRuleConflict;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommissionRuleConflict>
 */
class CommissionRuleConflictFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CommissionRuleConflict::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rule_a_id' => CommissionRule::factory(),
            'rule_b_id' => CommissionRule::factory(),
            'conflict_type' => $this->faker->randomElement([
                'type_mismatch',
                'value_conflict',
                'date_overlap',
                'condition_overlap'
            ]),
            'details' => function (array $attributes) {
                return $this->generateDetails($attributes['conflict_type']);
            },
            'resolved' => false,
            'resolved_at' => null,
            'resolved_by' => null,
            'resolution_type' => null,
            'resolution_data' => null,
            'resolution_notes' => null,
        ];
    }

    /**
     * Generate conflict details based on conflict type.
     */
    protected function generateDetails(string $conflictType): array
    {
        return match ($conflictType) {
            'type_mismatch' => [
                'type_mismatch' => [
                    'rule_a_type' => 'percentage',
                    'rule_b_type' => 'fixed',
                ],
            ],
            'value_conflict' => [
                'value_conflict' => [
                    'value_difference' => $this->faker->randomFloat(2, 1, 20),
                ],
            ],
            'date_overlap' => [
                'date_overlap' => [
                    'start' => now()->toDateTimeString(),
                    'end' => now()->addDays(30)->toDateTimeString(),
                ],
            ],
            'condition_overlap' => [
                'condition_overlap' => [
                    'minimum_order_amount' => $this->faker->randomFloat(2, 100, 1000),
                    'product_categories' => ['electronics', 'accessories'],
                ],
            ],
            default => [],
        };
    }

    /**
     * Indicate that the conflict is resolved.
     */
    public function resolved(?User $resolvedBy = null, ?string $resolutionType = null): static
    {
        return $this->state(function (array $attributes) use ($resolvedBy, $resolutionType) {
            $type = $resolutionType ?? $this->faker->randomElement([
                'adjust_conditions',
                'adjust_values',
                'adjust_dates'
            ]);

            return [
                'resolved' => true,
                'resolved_at' => now(),
                'resolved_by' => $resolvedBy?->id ?? User::factory(),
                'resolution_type' => $type,
                'resolution_data' => $this->generateResolutionData($type),
                'resolution_notes' => $this->faker->sentence(),
            ];
        });
    }

    /**
     * Generate resolution data based on resolution type.
     */
    protected function generateResolutionData(string $resolutionType): array
    {
        return match ($resolutionType) {
            'adjust_conditions' => [
                'rule_a_conditions' => [
                    'minimum_order_amount' => $this->faker->randomFloat(2, 100, 500),
                ],
                'rule_b_conditions' => [
                    'minimum_order_amount' => $this->faker->randomFloat(2, 501, 1000),
                ],
            ],
            'adjust_values' => [
                'rule_a_value' => $this->faker->randomFloat(2, 5, 15),
                'rule_b_value' => $this->faker->randomFloat(2, 5, 15),
            ],
            'adjust_dates' => [
                'rule_a_effective_from' => now()->toDateString(),
                'rule_a_effective_until' => now()->addDays(15)->toDateString(),
                'rule_b_effective_from' => now()->addDays(16)->toDateString(),
                'rule_b_effective_until' => now()->addDays(30)->toDateString(),
            ],
            default => [],
        };
    }

    /**
     * Set specific rules for the conflict.
     */
    public function betweenRules(CommissionRule $ruleA, CommissionRule $ruleB): static
    {
        return $this->state(fn (array $attributes) => [
            'rule_a_id' => $ruleA->id,
            'rule_b_id' => $ruleB->id,
        ]);
    }

    /**
     * Set the conflict type.
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'conflict_type' => $type,
            'details' => $this->generateDetails($type),
        ]);
    }

    /**
     * Set high severity conflict.
     */
    public function highSeverity(): static
    {
        return $this->state(fn (array $attributes) => [
            'conflict_type' => 'type_mismatch',
            'details' => $this->generateDetails('type_mismatch'),
        ]);
    }

    /**
     * Set medium severity conflict.
     */
    public function mediumSeverity(): static
    {
        return $this->state(fn (array $attributes) => [
            'conflict_type' => 'date_overlap',
            'details' => $this->generateDetails('date_overlap'),
        ]);
    }
}

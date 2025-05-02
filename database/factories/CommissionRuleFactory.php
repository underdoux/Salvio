<?php

namespace Database\Factories;

use App\Models\CommissionRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommissionRule>
 */
class CommissionRuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CommissionRule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['percentage', 'fixed']),
            'value' => $this->faker->randomFloat(2, 1, 100),
            'conditions' => json_encode([
                'min_order_value' => $this->faker->numberBetween(100, 1000),
                'max_order_value' => $this->faker->numberBetween(1001, 5000),
            ]),
            'active' => $this->faker->boolean(80), // 80% chance of being active
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => false,
            ];
        });
    }

    /**
     * Configure the model factory for percentage type.
     *
     * @return $this
     */
    public function percentage()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'percentage',
                'value' => $this->faker->randomFloat(2, 1, 50), // Reasonable percentage range
            ];
        });
    }

    /**
     * Configure the model factory for fixed type.
     *
     * @return $this
     */
    public function fixed()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'fixed',
                'value' => $this->faker->randomFloat(2, 10, 1000), // Reasonable fixed amount range
            ];
        });
    }
}

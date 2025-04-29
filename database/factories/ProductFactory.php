<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'category_id' => Category::inRandomOrder()->first()->id ?? 1,
            'bpom_registration_number' => Str::upper(Str::random(10)),
            'stock' => $this->faker->numberBetween(0, 100),
            'is_by_order' => $this->faker->boolean(),
            'price' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}

<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_creation()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100,
            'stock' => 50,
            'is_by_order' => false,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 100,
            'stock' => 50,
            'is_by_order' => false,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Tests\TestCase;
use Tests\Traits\WithRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductManagementAccessTest extends TestCase
{
    use RefreshDatabase, WithRoles;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_view_products_list(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $products = Product::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/products');

        $response->assertStatus(200);
        foreach ($products as $product) {
            $response->assertSee($product->name);
            $response->assertSee($product->sku);
        }
        $response->assertSee('Add Product');
        $response->assertSee('Edit');
        $response->assertSee('Delete');
    }

    public function test_sales_can_view_products_list(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $products = Product::factory()->count(3)->create();

        $response = $this->actingAs($sales)->get('/products');

        $response->assertStatus(200);
        foreach ($products as $product) {
            $response->assertSee($product->name);
            $response->assertSee($product->sku);
        }
        $response->assertDontSee('Add Product');
        $response->assertDontSee('Edit');
        $response->assertDontSee('Delete');
    }

    public function test_cashier_can_view_products_list(): void
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $products = Product::factory()->count(3)->create();

        $response = $this->actingAs($cashier)->get('/products');

        $response->assertStatus(200);
        foreach ($products as $product) {
            $response->assertSee($product->name);
            $response->assertSee($product->sku);
        }
        $response->assertDontSee('Add Product');
        $response->assertDontSee('Edit');
        $response->assertDontSee('Delete');
    }

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/products', [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'description' => 'Test description',
            'price' => 99.99,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('product.jpg'),
            'stock' => 100
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku' => 'TEST-001'
        ]);
    }

    public function test_sales_cannot_create_product(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $category = Category::factory()->create();

        $response = $this->actingAs($sales)->post('/products', [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'description' => 'Test description',
            'price' => 99.99,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('product.jpg'),
            'stock' => 100
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_update_product(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->put("/products/{$product->id}", [
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'price' => 149.99
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product'
        ]);
    }

    public function test_sales_cannot_update_product(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $product = Product::factory()->create();

        $response = $this->actingAs($sales)->put("/products/{$product->id}", [
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'price' => 149.99
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_product(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->delete("/products/{$product->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }

    public function test_sales_cannot_delete_product(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $product = Product::factory()->create();

        $response = $this->actingAs($sales)->delete("/products/{$product->id}");

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_products(): void
    {
        $response = $this->get('/products');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_manage_categories(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Create category
        $response = $this->actingAs($admin)->post('/categories', [
            'name' => 'Test Category',
            'description' => 'Test description'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);

        $category = Category::where('name', 'Test Category')->first();

        // Update category
        $response = $this->actingAs($admin)->put("/categories/{$category->id}", [
            'name' => 'Updated Category'
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Updated Category']);

        // Delete category
        $response = $this->actingAs($admin)->delete("/categories/{$category->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_sales_cannot_manage_categories(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole('sales');

        $category = Category::factory()->create();

        // Attempt to create category
        $response = $this->actingAs($sales)->post('/categories', [
            'name' => 'Test Category'
        ]);
        $response->assertForbidden();

        // Attempt to update category
        $response = $this->actingAs($sales)->put("/categories/{$category->id}", [
            'name' => 'Updated Category'
        ]);
        $response->assertForbidden();

        // Attempt to delete category
        $response = $this->actingAs($sales)->delete("/categories/{$category->id}");
        $response->assertForbidden();
    }
}

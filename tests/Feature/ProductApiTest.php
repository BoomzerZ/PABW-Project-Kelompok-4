<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all products.
     */
    public function test_can_list_all_products(): void
    {
        $category = Category::create(['name' => 'Keyboard']);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Test Keyboard',
            'description' => 'A test keyboard',
            'price' => 100000,
            'stock' => 10,
            'image_url' => 'https://example.com/test.jpg'
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Test Keyboard']);
    }

    /**
     * Test filtering products by category.
     */
    public function test_can_filter_products_by_category(): void
    {
        $cat1 = Category::create(['name' => 'Keyboard']);
        $cat2 = Category::create(['name' => 'Mouse']);

        Product::create([
            'category_id' => $cat1->id,
            'name' => 'Keyboard 1',
            'description' => 'Desc',
            'price' => 100,
            'stock' => 5,
            'image_url' => 'test.jpg'
        ]);

        Product::create([
            'category_id' => $cat2->id,
            'name' => 'Mouse 1',
            'description' => 'Desc',
            'price' => 200,
            'stock' => 5,
            'image_url' => 'test.jpg'
        ]);

        $response = $this->getJson('/api/products?category_id=' . $cat1->id);

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Keyboard 1'])
            ->assertJsonMissing(['name' => 'Mouse 1']);
    }

    /**
     * Test filtering products by max price.
     */
    public function test_can_filter_products_by_max_price(): void
    {
        $category = Category::create(['name' => 'Gear']);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Cheap Gear',
            'description' => 'Desc',
            'price' => 50000,
            'stock' => 5,
            'image_url' => 'test.jpg'
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Expensive Gear',
            'description' => 'Desc',
            'price' => 150000,
            'stock' => 5,
            'image_url' => 'test.jpg'
        ]);

        $response = $this->getJson('/api/products?max_price=100000');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Cheap Gear'])
            ->assertJsonMissing(['name' => 'Expensive Gear']);
    }

    /**
     * Test searching products by name.
     */
    public function test_can_search_products_by_name(): void
    {
        $category = Category::create(['name' => 'Gear']);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Logitech Mouse',
            'description' => 'Desc',
            'price' => 50000,
            'stock' => 5,
            'image_url' => 'test.jpg'
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Razer Keyboard',
            'description' => 'Desc',
            'price' => 150000,
            'stock' => 5,
            'image_url' => 'test.jpg'
        ]);

        $response = $this->getJson('/api/products?search=Logitech');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Logitech Mouse'])
            ->assertJsonMissing(['name' => 'Razer Keyboard']);
    }

    /**
     * Test getting a single product.
     */
    public function test_can_get_single_product(): void
    {
        $category = Category::create(['name' => 'Gear']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'description' => 'Desc',
            'price' => 50000,
            'stock' => 5,
            'image_url' => 'test.jpg'
        ]);

        $response = $this->getJson('/api/products/' . $product->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Test Product']);
    }

    /**
     * Test getting a non-existent product.
     */
    public function test_returns_404_for_non_existent_product(): void
    {
        $response = $this->getJson('/api/products/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Product not found']);
    }
}

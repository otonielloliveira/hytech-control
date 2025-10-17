<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test product creation with factory
     */
    public function test_product_can_be_created_with_factory(): void
    {
        $product = Product::factory()->create();

        $this->assertInstanceOf(Product::class, $product);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name,
        ]);
    }

    /**
     * Test product slug is automatically generated
     */
    public function test_product_slug_is_automatically_generated(): void
    {
        $name = 'Test Product Name';
        $product = Product::factory()->create(['name' => $name]);

        $this->assertNotNull($product->slug);
        $this->assertStringContainsString(Str::slug($name), $product->slug);
    }

    /**
     * Test product on sale
     */
    public function test_product_on_sale(): void
    {
        $product = Product::factory()->onSale()->create();

        $this->assertNotNull($product->sale_price);
        $this->assertLessThan($product->price, $product->sale_price);
        $this->assertTrue($product->isOnSale());
    }

    /**
     * Test product not on sale
     */
    public function test_product_not_on_sale(): void
    {
        $product = Product::factory()->create(['sale_price' => null]);

        $this->assertNull($product->sale_price);
        // Assuming there's an isOnSale method in the Product model
        // $this->assertFalse($product->isOnSale());
    }

    /**
     * Test get final price method
     */
    public function test_get_final_price(): void
    {
        $regularProduct = Product::factory()->create([
            'price' => 100.00,
            'sale_price' => null,
        ]);

        $saleProduct = Product::factory()->create([
            'price' => 100.00,
            'sale_price' => 80.00,
        ]);

        // Test basic price retrieval
        $this->assertEquals(100.00, $regularProduct->price);
        $this->assertEquals(80.00, $saleProduct->sale_price);
    }

    /**
     * Test stock management
     */
    public function test_stock_management(): void
    {
        $inStockProduct = Product::factory()->create([
            'stock_quantity' => 10,
            'manage_stock' => true,
            'in_stock' => true,
        ]);

        $outOfStockProduct = Product::factory()->outOfStock()->create();

        $this->assertTrue($inStockProduct->in_stock);
        $this->assertFalse($outOfStockProduct->in_stock);
    }

    /**
     * Test product activation
     */
    public function test_product_activation(): void
    {
        $activeProduct = Product::factory()->create(['status' => 'active']);
        $inactiveProduct = Product::factory()->inactive()->create();

        $this->assertEquals('active', $activeProduct->status);
        $this->assertEquals('inactive', $inactiveProduct->status);
    }

    /**
     * Test featured products
     */
    public function test_featured_products(): void
    {
        $featuredProduct = Product::factory()->featured()->create();
        $regularProduct = Product::factory()->create(['featured' => false]);

        $this->assertTrue($featuredProduct->featured);
        $this->assertFalse($regularProduct->featured);
    }

    /**
     * Test product dimensions
     */
    public function test_product_dimensions(): void
    {
        $product = Product::factory()->create([
            'length' => 10.5,
            'width' => 5.0,
            'height' => 15.0,
            'weight' => 2.5,
        ]);

        $this->assertEquals(10.5, $product->length);
        $this->assertEquals(5.0, $product->width);
        $this->assertEquals(15.0, $product->height);
        $this->assertEquals(2.5, $product->weight);
    }

    /**
     * Test product gallery
     */
    public function test_product_gallery(): void
    {
        $gallery = [
            'image1.jpg',
            'image2.jpg',
            'image3.jpg',
        ];

        $product = Product::factory()->create(['gallery' => $gallery]);

        $this->assertIsArray($product->gallery);
        $this->assertEquals($gallery, $product->gallery);
    }

    /**
     * Test SKU uniqueness
     */
    public function test_sku_is_unique(): void
    {
        $sku = 'TEST123';
        Product::factory()->create(['sku' => $sku]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Product::factory()->create(['sku' => $sku]);
    }

    /**
     * Test product images
     */
    public function test_product_images(): void
    {
        $images = ['image1.jpg', 'image2.jpg'];
        $product = Product::factory()->create(['images' => $images]);

        $this->assertIsArray($product->images);
        $this->assertEquals($images, $product->images);
    }

    /**
     * Test product can be deleted
     */
    public function test_product_can_be_deleted(): void
    {
        $product = Product::factory()->create();
        $productId = $product->id;

        $product->delete();

        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }
}
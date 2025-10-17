<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->randomNumber(4),
            'description' => $this->faker->paragraph(),
            'short_description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'sale_price' => null,
            'sku' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'manage_stock' => true,
            'in_stock' => true,
            'weight' => $this->faker->randomFloat(2, 0.1, 10),
            'length' => $this->faker->randomFloat(2, 1, 50),
            'width' => $this->faker->randomFloat(2, 1, 50),
            'height' => $this->faker->randomFloat(2, 1, 50),
            'images' => [
                $this->faker->imageUrl(600, 600, 'products'),
            ],
            'gallery' => [
                $this->faker->imageUrl(600, 600, 'products'),
                $this->faker->imageUrl(600, 600, 'products'),
            ],
            'featured' => $this->faker->boolean(30),
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the product is on sale.
     */
    public function onSale(): static
    {
        return $this->state(function (array $attributes) {
            $originalPrice = $attributes['price'];
            $salePrice = $originalPrice * $this->faker->randomFloat(2, 0.5, 0.9);
            
            return [
                'sale_price' => $salePrice,
            ];
        });
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
            'in_stock' => false,
        ]);
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
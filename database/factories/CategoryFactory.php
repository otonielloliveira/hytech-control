<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->randomNumber(3),
            'description' => $this->faker->paragraph(),
            'color' => $this->faker->hexColor(),
            'image' => $this->faker->imageUrl(300, 200, 'categories'),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
            'meta_title' => $name,
            'meta_description' => $this->faker->sentence(),
            'meta_keywords' => $this->faker->words(5),
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
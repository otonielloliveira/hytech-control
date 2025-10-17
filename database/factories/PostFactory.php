<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(6, true);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->randomNumber(4),
            'excerpt' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(5, true),
            'featured_image' => $this->faker->imageUrl(800, 600, 'blog'),
            'video_type' => 'none',
            'video_url' => null,
            'video_embed_code' => null,
            'show_video_in_content' => false,
            'status' => $this->faker->randomElement(['draft', 'published']),
            'destination' => $this->faker->randomElement([
                'artigos', 'peticoes', 'ultimas_noticias', 'noticias_mundiais',
                'noticias_nacionais', 'noticias_regionais', 'politica', 'economia'
            ]),
            'petition_videos' => [],
            'whatsapp_groups' => [],
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'meta_title' => $title,
            'meta_description' => $this->faker->sentence(),
            'meta_keywords' => $this->faker->words(5),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'views_count' => $this->faker->numberBetween(0, 1000),
            'is_featured' => $this->faker->boolean(20), // 20% chance
            'tags' => $this->faker->words(3),
            'reading_time' => $this->faker->numberBetween(1, 15),
        ];
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the post is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the post is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Create a petition post.
     */
    public function petition(): static
    {
        return $this->state(fn (array $attributes) => [
            'destination' => 'peticoes',
            'petition_videos' => [
                $this->faker->url(),
                $this->faker->url(),
            ],
        ]);
    }

    /**
     * Create a post with video.
     */
    public function withVideo(): static
    {
        return $this->state(fn (array $attributes) => [
            'video_type' => 'youtube',
            'video_url' => 'https://www.youtube.com/watch?v=' . $this->faker->regexify('[A-Za-z0-9]{11}'),
            'show_video_in_content' => true,
        ]);
    }
}
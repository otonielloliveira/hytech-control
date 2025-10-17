<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test post creation with factory
     */
    public function test_post_can_be_created_with_factory(): void
    {
        $post = Post::factory()->create();

        $this->assertInstanceOf(Post::class, $post);
        $this->assertDatabaseHas('blog_posts', [
            'id' => $post->id,
            'title' => $post->title,
        ]);
    }

    /**
     * Test post slug is automatically generated
     */
    public function test_post_slug_is_automatically_generated(): void
    {
        $title = 'Test Blog Post Title';
        $post = Post::factory()->create(['title' => $title, 'slug' => '']);

        $expectedSlug = Str::slug($title);
        $this->assertStringContainsString($expectedSlug, $post->slug);
    }

    /**
     * Test post reading time is calculated automatically
     */
    public function test_reading_time_is_calculated_automatically(): void
    {
        $content = str_repeat('word ', 400); // 400 words
        $post = Post::factory()->create(['content' => $content]);

        // 400 words / 200 words per minute = 2 minutes
        $this->assertEquals(2, $post->reading_time);
    }

    /**
     * Test post belongs to user
     */
    public function test_post_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $post->user);
        $this->assertEquals($user->id, $post->user->id);
    }

    /**
     * Test post belongs to category
     */
    public function test_post_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $post = Post::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $post->category);
        $this->assertEquals($category->id, $post->category->id);
    }

    /**
     * Test published scope
     */
    public function test_published_scope(): void
    {
        $publishedPost = Post::factory()->published()->create();
        $draftPost = Post::factory()->draft()->create();

        $publishedPosts = Post::published()->get();

        $this->assertTrue($publishedPosts->contains($publishedPost));
        $this->assertFalse($publishedPosts->contains($draftPost));
    }

    /**
     * Test featured scope
     */
    public function test_featured_scope(): void
    {
        $featuredPost = Post::factory()->featured()->create();
        $regularPost = Post::factory()->create(['is_featured' => false]);

        $featuredPosts = Post::featured()->get();

        $this->assertTrue($featuredPosts->contains($featuredPost));
        $this->assertFalse($featuredPosts->contains($regularPost));
    }

    /**
     * Test post is published method
     */
    public function test_is_published_method(): void
    {
        $publishedPost = Post::factory()->published()->create();
        $draftPost = Post::factory()->draft()->create();
        $futurePost = Post::factory()->create([
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $this->assertTrue($publishedPost->isPublished());
        $this->assertFalse($draftPost->isPublished());
        $this->assertFalse($futurePost->isPublished());
    }

    /**
     * Test increment views method
     */
    public function test_increment_views(): void
    {
        $post = Post::factory()->create(['views_count' => 5]);

        $post->incrementViews();

        $this->assertEquals(6, $post->fresh()->views_count);
    }

    /**
     * Test get URL method
     */
    public function test_get_url_method(): void
    {
        $post = Post::factory()->create(['slug' => 'test-post']);

        $expectedUrl = route('blog.post.show', 'test-post');
        $this->assertEquals($expectedUrl, $post->getUrl());
    }

    /**
     * Test destination options
     */
    public function test_destination_options(): void
    {
        $options = Post::getDestinationOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('artigos', $options);
        $this->assertArrayHasKey('peticoes', $options);
        $this->assertArrayHasKey('politica', $options);
    }

    /**
     * Test petition methods
     */
    public function test_petition_methods(): void
    {
        $petitionPost = Post::factory()->petition()->create();
        $regularPost = Post::factory()->create(['destination' => 'artigos']);

        $this->assertTrue($petitionPost->isPetition());
        $this->assertFalse($regularPost->isPetition());
    }

    /**
     * Test video embed functionality
     */
    public function test_video_embed_functionality(): void
    {
        $youtubePost = Post::factory()->withVideo()->create([
            'video_type' => 'youtube',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $embed = $youtubePost->getVideoEmbedAttribute();
        
        $this->assertNotNull($embed);
        $this->assertStringContainsString('iframe', $embed);
        $this->assertStringContainsString('youtube.com/embed', $embed);
    }

    /**
     * Test excerpt generation
     */
    public function test_excerpt_generation(): void
    {
        $content = 'This is a very long content that should be truncated when generating an excerpt. ' . str_repeat('More content. ', 50);
        $post = Post::factory()->create([
            'content' => $content,
            'excerpt' => null,
        ]);

        $excerpt = $post->getExcerptAttribute(null);
        
        $this->assertNotNull($excerpt);
        $this->assertLessThanOrEqual(160, strlen($excerpt));
    }

    /**
     * Test tags casting
     */
    public function test_tags_are_cast_to_array(): void
    {
        $tags = ['laravel', 'php', 'testing'];
        $post = Post::factory()->create(['tags' => $tags]);

        $this->assertIsArray($post->tags);
        $this->assertEquals($tags, $post->tags);
    }

    /**
     * Test soft deletes
     */
    public function test_post_can_be_soft_deleted(): void
    {
        $post = Post::factory()->create();
        $postId = $post->id;

        $post->delete();

        $this->assertSoftDeleted('blog_posts', ['id' => $postId]);
        $this->assertNotNull($post->fresh()->deleted_at);
    }
}
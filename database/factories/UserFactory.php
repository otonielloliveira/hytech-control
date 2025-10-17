<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_admin' => false,
            'can_create_posts' => false,
            'can_edit_posts' => false,
            'can_delete_posts' => false,
            'can_manage_users' => false,
            'can_manage_categories' => false,
            'can_manage_settings' => false,
            'can_manage_store' => false,
            'can_manage_courses' => false,
            'can_manage_donations' => false,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create admin user
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
            'can_create_posts' => true,
            'can_edit_posts' => true,
            'can_delete_posts' => true,
            'can_manage_users' => true,
            'can_manage_categories' => true,
            'can_manage_settings' => true,
            'can_manage_store' => true,
            'can_manage_courses' => true,
            'can_manage_donations' => true,
        ]);
    }

    /**
     * Create user with post permissions only
     */
    public function postManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'can_create_posts' => true,
            'can_edit_posts' => true,
            'can_delete_posts' => true,
        ]);
    }

    /**
     * Create user with store permissions only
     */
    public function storeManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'can_manage_store' => true,
        ]);
    }
}

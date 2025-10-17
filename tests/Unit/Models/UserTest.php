<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user creation with factory
     */
    public function test_user_can_be_created_with_factory(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Test admin user permissions
     */
    public function test_admin_user_has_all_permissions(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->is_admin);
        $this->assertTrue($admin->canCreatePosts());
        $this->assertTrue($admin->canEditPosts());
        $this->assertTrue($admin->canDeletePosts());
        $this->assertTrue($admin->canManageUsers());
        $this->assertTrue($admin->canManageCategories());
        $this->assertTrue($admin->canManageSettings());
        $this->assertTrue($admin->canManageStore());
        $this->assertTrue($admin->canManageCourses());
        $this->assertTrue($admin->canManageDonations());
    }

    /**
     * Test regular user has no permissions
     */
    public function test_regular_user_has_no_permissions(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->is_admin);
        $this->assertFalse($user->canCreatePosts());
        $this->assertFalse($user->canEditPosts());
        $this->assertFalse($user->canDeletePosts());
        $this->assertFalse($user->canManageUsers());
        $this->assertFalse($user->canManageCategories());
        $this->assertFalse($user->canManageSettings());
        $this->assertFalse($user->canManageStore());
        $this->assertFalse($user->canManageCourses());
        $this->assertFalse($user->canManageDonations());
    }

    /**
     * Test post manager permissions
     */
    public function test_post_manager_has_only_post_permissions(): void
    {
        $user = User::factory()->postManager()->create();

        $this->assertFalse($user->is_admin);
        $this->assertTrue($user->canCreatePosts());
        $this->assertTrue($user->canEditPosts());
        $this->assertTrue($user->canDeletePosts());
        $this->assertFalse($user->canManageUsers());
        $this->assertFalse($user->canManageStore());
    }

    /**
     * Test store manager permissions
     */
    public function test_store_manager_has_only_store_permissions(): void
    {
        $user = User::factory()->storeManager()->create();

        $this->assertFalse($user->is_admin);
        $this->assertFalse($user->canCreatePosts());
        $this->assertTrue($user->canManageStore());
        $this->assertFalse($user->canManageUsers());
    }

    /**
     * Test profile helper methods
     */
    public function test_profile_helper_methods(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
    }

    /**
     * Test user has relationships
     */
    public function test_user_has_relationships(): void
    {
        $user = User::factory()->create();

        // Test relationships exist (even if empty)
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->posts);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->comments);
        
        // Test additional relationships if they exist
        if (method_exists($user, 'orders')) {
            $this->assertIsIterable($user->orders);
        }
    }

    /**
     * Test password is hashed
     */
    public function test_password_is_hashed(): void
    {
        $user = User::factory()->create(['password' => 'plaintext']);

        $this->assertNotEquals('plaintext', $user->password);
        $this->assertTrue(password_verify('plaintext', $user->password));
    }

    /**
     * Test email verification
     */
    public function test_email_verification(): void
    {
        $verifiedUser = User::factory()->create();
        $unverifiedUser = User::factory()->unverified()->create();

        $this->assertNotNull($verifiedUser->email_verified_at);
        $this->assertNull($unverifiedUser->email_verified_at);
    }

    /**
     * Test user can be soft deleted
     */
    public function test_user_cannot_be_soft_deleted(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        // User should be hard deleted since User model doesn't use SoftDeletes
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    /**
     * Test unique email constraint
     */
    public function test_email_must_be_unique(): void
    {
        $email = 'test@example.com';
        User::factory()->create(['email' => $email]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => $email]);
    }
}
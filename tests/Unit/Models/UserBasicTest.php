<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;

class UserBasicTest extends TestCase
{
    /**
     * Test user creation with factory
     */
    public function test_user_can_be_created_with_factory(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->id);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->name);
    }

    /**
     * Test user has permission methods
     */
    public function test_user_has_permission_methods(): void
    {
        $user = User::factory()->create();

        // Verificar se os métodos existem
        $this->assertTrue(method_exists($user, 'canCreatePosts'));
        $this->assertTrue(method_exists($user, 'canEditPosts'));
        $this->assertTrue(method_exists($user, 'canManageUsers'));
        
        // Verificar valores padrão (false para usuário regular)
        $this->assertFalse($user->canCreatePosts());
        $this->assertFalse($user->canManageUsers());
    }

    /**
     * Test admin user permissions
     */
    public function test_admin_user_permissions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->assertTrue($admin->is_admin);
        
        // Admin pode fazer tudo
        $this->assertTrue($admin->canCreatePosts());
        $this->assertTrue($admin->canManageUsers());
    }
}
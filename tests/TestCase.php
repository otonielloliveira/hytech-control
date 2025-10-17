<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // FORÇAR configuração de teste ANTES de qualquer operação
        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');
        $this->app['config']->set('app.env', 'testing');
        
        // Desabilitar Telescope para testes
        $this->app['config']->set('telescope.enabled', false);
        
        // Configurar cache e session para arrays
        $this->app['config']->set('cache.default', 'array');
        $this->app['config']->set('session.driver', 'array');
        
        // Configurar log para testing
        $this->app['config']->set('logging.default', 'testing');
        
        // Executar migrations manualmente
        $this->runMigrations();
    }
    
    protected function runMigrations(): void
    {
        $this->artisan('migrate:fresh', [
            '--database' => 'sqlite',
            '--force' => true
        ]);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Criar um usuário admin para testes
     */
    protected function createAdminUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
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
        ], $attributes));
    }

    /**
     * Criar um usuário com permissões específicas
     */
    protected function createUserWithPermissions(array $permissions): User
    {
        return User::factory()->create($permissions);
    }

    /**
     * Criar um usuário regular (sem permissões especiais)
     */
    protected function createRegularUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
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
        ], $attributes));
    }

    /**
     * Autenticar como admin
     */
    protected function actingAsAdmin(?User $admin = null): static
    {
        $admin = $admin ?: $this->createAdminUser();
        return $this->actingAs($admin);
    }

    /**
     * Autenticar como usuário regular
     */
    protected function actingAsUser(?User $user = null): static
    {
        $user = $user ?: $this->createRegularUser();
        return $this->actingAs($user);
    }

    /**
     * Configurar banco de dados SQLite em memória para testes
     */
    protected function defineDatabaseTransactions(): array
    {
        return [];
    }
}

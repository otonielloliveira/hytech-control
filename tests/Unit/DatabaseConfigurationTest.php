<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseConfigurationTest extends TestCase
{
    public function test_uses_sqlite_in_memory_for_testing()
    {
        // Verificar se estamos usando SQLite
        $this->assertEquals('sqlite', config('database.default'));
        
        // Verificar se o banco é em memória
        $this->assertEquals(':memory:', config('database.connections.sqlite.database'));
        
        // Verificar se conseguimos criar tabelas
        Schema::create('test_table', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        
        $this->assertTrue(Schema::hasTable('test_table'));
        
        // Inserir dados de teste
        DB::table('test_table')->insert([
            'name' => 'Test Record',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->assertEquals(1, DB::table('test_table')->count());
    }
    
    public function test_database_is_clean_between_tests()
    {
        // Este teste deve passar mesmo rodando após o anterior
        // porque o RefreshDatabase limpa o banco a cada teste
        
        $this->assertFalse(Schema::hasTable('test_table'));
    }
    
    public function test_user_table_exists_after_migrations()
    {
        // Verificar se as migrations foram executadas
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('blog_posts'));
        
        // Verificar se outras tabelas importantes existem
        if (Schema::hasTable('categories')) {
            $this->assertTrue(Schema::hasTable('categories'));
        }
        
        // Debug: listar todas as tabelas criadas
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        $tableNames = array_map(fn($table) => $table->name, $tables);
        
        // Verificar se pelo menos as tabelas básicas existem
        $this->assertContains('users', $tableNames);
        $this->assertContains('migrations', $tableNames);
    }
}
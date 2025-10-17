<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Permissões para Posts
            $table->boolean('can_create_posts')->default(false)->after('is_author');
            $table->boolean('can_edit_posts')->default(false)->after('can_create_posts');
            $table->boolean('can_delete_posts')->default(false)->after('can_edit_posts');
            $table->boolean('can_publish_posts')->default(false)->after('can_delete_posts');
            $table->boolean('can_edit_own_posts_only')->default(true)->after('can_publish_posts');
            
            // Permissões para Comentários
            $table->boolean('can_moderate_comments')->default(false)->after('can_edit_own_posts_only');
            
            // Permissões para Categorias
            $table->boolean('can_manage_categories')->default(false)->after('can_moderate_comments');
            
            // Permissões para Tags
            $table->boolean('can_manage_tags')->default(false)->after('can_manage_categories');
            
            // Permissões para Usuários
            $table->boolean('can_manage_users')->default(false)->after('can_manage_tags');
            
            // Permissões para Configurações
            $table->boolean('can_manage_settings')->default(false)->after('can_manage_users');
            
            // Permissões para Loja
            $table->boolean('can_manage_store')->default(false)->after('can_manage_settings');
            
            // Permissões para Doações
            $table->boolean('can_manage_donations')->default(false)->after('can_manage_store');
            
            // Permissões para Cursos
            $table->boolean('can_manage_courses')->default(false)->after('can_manage_donations');
            
            // Status do usuário
            $table->boolean('is_active')->default(true)->after('can_manage_courses');
            
            // Data de último acesso
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'can_create_posts',
                'can_edit_posts', 
                'can_delete_posts',
                'can_publish_posts',
                'can_edit_own_posts_only',
                'can_moderate_comments',
                'can_manage_categories',
                'can_manage_tags',
                'can_manage_users',
                'can_manage_settings',
                'can_manage_store',
                'can_manage_donations',
                'can_manage_courses',
                'is_active',
                'last_login_at'
            ]);
        });
    }
};

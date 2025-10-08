<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Models\BlogConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário admin apenas se não existir
        $admin = User::firstOrCreate(
            ['email' => 'admin@hytech.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'is_author' => true,
                'bio' => 'Administrador do sistema',
            ]
        );

        // Criar algumas categorias de exemplo
        $categories = [
            [
                'name' => 'Tecnologia',
                'slug' => 'tecnologia',
                'description' => 'Posts sobre tecnologia e inovação',
                'color' => '#3B82F6',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'description' => 'Artigos sobre Laravel e PHP',
                'color' => '#EF4444',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Filament',
                'slug' => 'filament',
                'description' => 'Tutoriais e dicas sobre Filament',
                'color' => '#F59E0B',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );

            // Criar posts de exemplo para cada categoria apenas se não existir
            $postSlug = 'introducao-' . $category->slug;
            if (!Post::where('slug', $postSlug)->exists()) {
                Post::create([
                    'title' => 'Introdução ao ' . $category->name,
                    'slug' => $postSlug,
                    'excerpt' => 'Um artigo introdutório sobre ' . $category->name,
                    'content' => '<p>Este é um post de exemplo sobre <strong>' . $category->name . '</strong>.</p><p>Aqui você encontrará informações valiosas e atualizadas sobre este tema.</p>',
                    'status' => 'published',
                    'published_at' => now(),
                    'user_id' => $admin->id,
                    'category_id' => $category->id,
                    'is_featured' => $category->slug === 'tecnologia',
                    'tags' => [$category->slug, 'tutorial', 'guia'],
                    'reading_time' => 5,
                ]);
            }
        }

        // Criar configuração inicial do blog apenas se não existir
        if (BlogConfig::count() === 0) {
            BlogConfig::create([
                'site_name' => 'HyTech Control Blog',
                'site_description' => 'Blog oficial da HyTech Control - Tecnologia e Inovação',
                'meta_title' => 'HyTech Control Blog - Tecnologia e Inovação',
                'meta_description' => 'Descubra as últimas tendências em tecnologia, desenvolvimento e inovação no blog oficial da HyTech Control.',
                'meta_keywords' => ['tecnologia', 'desenvolvimento', 'inovação', 'laravel', 'filament', 'php'],
                'footer_text' => '<p>© ' . date('Y') . ' <strong>HyTech Control</strong>. Todos os direitos reservados.</p>',
                'social_links' => [
                    'facebook' => 'https://facebook.com/hytechcontrol',
                    'twitter' => 'https://twitter.com/hytechcontrol',
                    'instagram' => 'https://instagram.com/hytechcontrol',
                    'linkedin' => 'https://linkedin.com/company/hytechcontrol',
                    'youtube' => 'https://youtube.com/@hytechcontrol',
                ],
                'contact_email' => 'contato@hytech.com',
                'contact_phone' => '(11) 99999-9999',
                'address' => 'São Paulo, SP - Brasil',
            ]);
        }
    }
}

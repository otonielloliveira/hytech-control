<?php

namespace Database\Seeders;

use App\Models\SectionConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'section_key' => 'featured_posts',
                'section_name' => 'Conteúdos Principais',
                'section_icon' => 'fas fa-star',
                'section_description' => 'Descubra nossos artigos mais importantes e relevantes',
                'sort_order' => 1,
            ],
            [
                'section_key' => 'articles',
                'section_name' => 'Análises e Reflexões',
                'section_icon' => 'fas fa-newspaper',
                'section_description' => 'Conteúdos aprofundados sobre diversos temas',
                'sort_order' => 2,
            ],
            [
                'section_key' => 'news_mundial',
                'section_name' => 'Notícias Mundiais',
                'section_icon' => 'fas fa-globe',
                'section_description' => 'Principais acontecimentos do mundo',
                'sort_order' => 3,
            ],
            [
                'section_key' => 'news_nacional',
                'section_name' => 'Notícias Nacionais',
                'section_icon' => 'fas fa-flag',
                'section_description' => 'Notícias do Brasil',
                'sort_order' => 4,
            ],
            [
                'section_key' => 'news_regional',
                'section_name' => 'Notícias Regionais',
                'section_icon' => 'fas fa-map-marker-alt',
                'section_description' => 'Acontecimentos da sua região',
                'sort_order' => 5,
            ],
            [
                'section_key' => 'politics',
                'section_name' => 'Política',
                'section_icon' => 'fas fa-landmark',
                'section_description' => 'Análises políticas e governamentais',
                'sort_order' => 6,
            ],
            [
                'section_key' => 'economy',
                'section_name' => 'Economia',
                'section_icon' => 'fas fa-dollar-sign',
                'section_description' => 'Mercado financeiro e economia',
                'sort_order' => 7,
            ],
            [
                'section_key' => 'petitions',
                'section_name' => 'Petições',
                'section_icon' => 'fas fa-hand-fist',
                'section_description' => 'Participe das nossas campanhas',
                'sort_order' => 8,
            ],
            [
                'section_key' => 'latest_news',
                'section_name' => 'Últimas Notícias',
                'section_icon' => 'fas fa-bolt',
                'section_description' => 'Acontecimentos mais recentes',
                'sort_order' => 9,
            ],
            [
                'section_key' => 'friends_supporters',
                'section_name' => 'Amigos e Apoiadores',
                'section_icon' => 'fas fa-handshake',
                'section_description' => 'Conheça nossos parceiros',
                'sort_order' => 10,
            ],
        ];

        foreach ($sections as $section) {
            SectionConfig::updateOrCreate(
                ['section_key' => $section['section_key']],
                $section
            );
        }
    }
}

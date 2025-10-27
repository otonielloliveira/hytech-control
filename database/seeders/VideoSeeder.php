<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $videos = [
            // Categoria: Tutoriais
            [
                'title' => 'Como Começar com Laravel 11 - Tutorial Completo',
                'description' => 'Aprenda a criar seu primeiro projeto Laravel do zero. Neste tutorial completo, vamos abordar instalação, configuração e criação de suas primeiras rotas e controllers.',
                'video_url' => 'https://www.youtube.com/watch?v=MFh0Fd7BsjE',
                'category' => 'tutoriais',
                'tags' => ['laravel', 'php', 'desenvolvimento web', 'tutorial', 'programação'],
                'duration' => '45:30',
                'published_date' => now()->subDays(5),
                'is_active' => true,
                'priority' => 10,
                'views_count' => 1250,
            ],
            [
                'title' => 'Filament 3: Criando um Admin Panel Profissional',
                'description' => 'Descubra como criar um painel administrativo moderno e funcional usando Filament 3. Aprenda sobre recursos, formulários e tabelas personalizadas.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'category' => 'tutoriais',
                'tags' => ['filament', 'laravel', 'admin panel', 'php', 'tutorial'],
                'duration' => '32:15',
                'published_date' => now()->subDays(12),
                'is_active' => true,
                'priority' => 9,
                'views_count' => 890,
            ],
            [
                'title' => 'API RESTful com Laravel: Guia Completo',
                'description' => 'Aprenda a construir APIs RESTful profissionais com Laravel. Cobrimos autenticação, validação, recursos e muito mais.',
                'video_url' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw',
                'category' => 'tutoriais',
                'tags' => ['api', 'laravel', 'rest', 'desenvolvimento web', 'backend'],
                'duration' => '28:45',
                'published_date' => now()->subDays(8),
                'is_active' => true,
                'priority' => 8,
                'views_count' => 2100,
            ],

            // Categoria: Design
            [
                'title' => 'Tailwind CSS: Do Básico ao Avançado',
                'description' => 'Domine o Tailwind CSS e crie interfaces modernas e responsivas. Aprenda as melhores práticas e técnicas avançadas.',
                'video_url' => 'https://www.youtube.com/watch?v=UBOj6rqRUME',
                'category' => 'design',
                'tags' => ['tailwind', 'css', 'design', 'frontend', 'ui'],
                'duration' => '41:20',
                'published_date' => now()->subDays(3),
                'is_active' => true,
                'priority' => 7,
                'views_count' => 1680,
            ],
            [
                'title' => 'Design System: Criando Componentes Reutilizáveis',
                'description' => 'Aprenda a criar um design system completo com componentes reutilizáveis e consistentes para seus projetos.',
                'video_url' => 'https://www.youtube.com/watch?v=3c-iBn73dDE',
                'category' => 'design',
                'tags' => ['design system', 'componentes', 'ui', 'frontend', 'css'],
                'duration' => '36:50',
                'published_date' => now()->subDays(15),
                'is_active' => true,
                'priority' => 6,
                'views_count' => 945,
            ],

            // Categoria: DevOps
            [
                'title' => 'Deploy Laravel na AWS: Passo a Passo',
                'description' => 'Aprenda a fazer deploy de sua aplicação Laravel na AWS de forma profissional e segura. Cobrimos EC2, RDS e S3.',
                'video_url' => 'https://www.youtube.com/watch?v=J---aiyznGQ',
                'category' => 'devops',
                'tags' => ['aws', 'deploy', 'laravel', 'cloud', 'devops'],
                'duration' => '52:30',
                'published_date' => now()->subDays(7),
                'is_active' => true,
                'priority' => 9,
                'views_count' => 3200,
            ],
            [
                'title' => 'Docker para Desenvolvedores Laravel',
                'description' => 'Configure seu ambiente de desenvolvimento Laravel com Docker. Aprenda sobre containers, volumes e docker-compose.',
                'video_url' => 'https://www.youtube.com/watch?v=Kzcz-EVKBEQ',
                'category' => 'devops',
                'tags' => ['docker', 'laravel', 'containers', 'desenvolvimento', 'devops'],
                'duration' => '38:15',
                'published_date' => now()->subDays(10),
                'is_active' => true,
                'priority' => 8,
                'views_count' => 2750,
            ],

            // Categoria: Banco de Dados
            [
                'title' => 'Eloquent ORM: Relacionamentos Avançados',
                'description' => 'Domine os relacionamentos do Eloquent ORM. Aprenda sobre relacionamentos polimórficos, many-to-many e muito mais.',
                'video_url' => 'https://www.youtube.com/watch?v=gvtDlg8H31Y',
                'category' => 'banco de dados',
                'tags' => ['eloquent', 'laravel', 'orm', 'banco de dados', 'mysql'],
                'duration' => '44:20',
                'published_date' => now()->subDays(6),
                'is_active' => true,
                'priority' => 7,
                'views_count' => 1890,
            ],
            [
                'title' => 'Otimização de Queries no Laravel',
                'description' => 'Aprenda técnicas para otimizar suas queries e melhorar a performance da sua aplicação Laravel.',
                'video_url' => 'https://www.youtube.com/watch?v=Bb08tukzrO4',
                'category' => 'banco de dados',
                'tags' => ['performance', 'laravel', 'queries', 'otimização', 'banco de dados'],
                'duration' => '29:40',
                'published_date' => now()->subDays(14),
                'is_active' => true,
                'priority' => 6,
                'views_count' => 1450,
            ],

            // Categoria: Segurança
            [
                'title' => 'Segurança em Laravel: Melhores Práticas',
                'description' => 'Aprenda as melhores práticas de segurança para proteger sua aplicação Laravel contra ataques comuns.',
                'video_url' => 'https://www.youtube.com/watch?v=PXsWYwxAor4',
                'category' => 'segurança',
                'tags' => ['segurança', 'laravel', 'proteção', 'csrf', 'xss'],
                'duration' => '35:25',
                'published_date' => now()->subDays(4),
                'is_active' => true,
                'priority' => 10,
                'views_count' => 2340,
            ],

            // Categoria: JavaScript
            [
                'title' => 'Vue.js 3 com Laravel: Integração Completa',
                'description' => 'Integre Vue.js 3 com Laravel para criar aplicações SPA modernas e reativas.',
                'video_url' => 'https://www.youtube.com/watch?v=Wy9q22isx3U',
                'category' => 'javascript',
                'tags' => ['vue', 'javascript', 'laravel', 'spa', 'frontend'],
                'duration' => '48:10',
                'published_date' => now()->subDays(9),
                'is_active' => true,
                'priority' => 8,
                'views_count' => 1920,
            ],
            [
                'title' => 'Livewire 3: Componentes Reativos sem JavaScript',
                'description' => 'Crie componentes dinâmicos e reativos usando apenas PHP com Laravel Livewire 3.',
                'video_url' => 'https://www.youtube.com/watch?v=Mz9537kAlvA',
                'category' => 'javascript',
                'tags' => ['livewire', 'laravel', 'componentes', 'php', 'frontend'],
                'duration' => '39:55',
                'published_date' => now()->subDays(11),
                'is_active' => true,
                'priority' => 9,
                'views_count' => 2580,
            ],

            // Categoria: Testes
            [
                'title' => 'Testes Automatizados com PHPUnit no Laravel',
                'description' => 'Aprenda a escrever testes unitários e de feature para garantir a qualidade do seu código Laravel.',
                'video_url' => 'https://www.youtube.com/watch?v=86Z36Qo8fAA',
                'category' => 'testes',
                'tags' => ['testes', 'phpunit', 'laravel', 'qualidade', 'tdd'],
                'duration' => '42:30',
                'published_date' => now()->subDays(13),
                'is_active' => true,
                'priority' => 7,
                'views_count' => 1230,
            ],

            // Categoria: Performance
            [
                'title' => 'Cache e Performance no Laravel',
                'description' => 'Técnicas avançadas de cache e otimização para melhorar drasticamente a performance da sua aplicação.',
                'video_url' => 'https://www.youtube.com/watch?v=lhvjzDvp3i0',
                'category' => 'performance',
                'tags' => ['cache', 'performance', 'laravel', 'otimização', 'redis'],
                'duration' => '31:45',
                'published_date' => now()->subDays(2),
                'is_active' => true,
                'priority' => 8,
                'views_count' => 2890,
            ],

            // Categoria: Pagamentos
            [
                'title' => 'Integração com Gateway de Pagamento',
                'description' => 'Aprenda a integrar diferentes gateways de pagamento (PIX, cartão de crédito) na sua aplicação Laravel.',
                'video_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'category' => 'pagamentos',
                'tags' => ['pagamentos', 'pix', 'gateway', 'laravel', 'ecommerce'],
                'duration' => '55:20',
                'published_date' => now()->subDays(1),
                'is_active' => true,
                'priority' => 10,
                'views_count' => 4150,
            ],
        ];

        foreach ($videos as $videoData) {
            Video::create($videoData);
        }

        $this->command->info('✅ Videos criados com sucesso!');
    }
}

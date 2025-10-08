<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Banner;
use App\Models\BlogConfig;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário admin se não existir
        $admin = User::firstOrCreate([
            'email' => 'admin@hytech.com'
        ], [
            'name' => 'Administrador',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Configuração do Blog
        BlogConfig::updateOrCreate([], [
            'site_name' => 'HyTech Blog',
            'site_description' => 'Um blog moderno sobre tecnologia e inovação',
            'site_logo' => null,
            'site_favicon' => null,
            'contact_email' => 'contato@hytech.com',
            'contact_phone' => '(11) 99999-9999',
            'address' => 'São Paulo, SP - Brasil',
            'social_links' => [
                'facebook' => 'https://facebook.com/hytech',
                'instagram' => 'https://instagram.com/hytech',
                'twitter' => 'https://twitter.com/hytech',
                'youtube' => 'https://youtube.com/hytech',
                'linkedin' => 'https://linkedin.com/company/hytech',
            ],
            'footer_text' => 'Todos os direitos reservados.',
            'meta_title' => 'HyTech Blog - Tecnologia e Inovação',
            'meta_description' => 'Fique por dentro das últimas novidades em tecnologia, programação e inovação.',
            'meta_keywords' => ['tecnologia', 'programação', 'inovação', 'blog', 'desenvolvimento'],
        ]);

        // Categorias
        $categories = [
            [
                'name' => 'Tecnologia',
                'slug' => 'tecnologia',
                'description' => 'Últimas novidades em tecnologia e inovação',
                'color' => '#007bff',
                'is_active' => true,
            ],
            [
                'name' => 'Programação',
                'slug' => 'programacao',
                'description' => 'Dicas e tutoriais de programação',
                'color' => '#28a745',
                'is_active' => true,
            ],
            [
                'name' => 'IA & Machine Learning',
                'slug' => 'ia-machine-learning',
                'description' => 'Inteligência Artificial e aprendizado de máquina',
                'color' => '#dc3545',
                'is_active' => true,
            ],
            [
                'name' => 'Design',
                'slug' => 'design',
                'description' => 'Design UI/UX e tendências visuais',
                'color' => '#fd7e14',
                'is_active' => true,
            ],
            [
                'name' => 'Negócios',
                'slug' => 'negocios',
                'description' => 'Estratégias e insights de negócios',
                'color' => '#6f42c1',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Tags
        $tagNames = [
            'Laravel', 'PHP', 'JavaScript', 'React', 'Vue.js', 'Node.js',
            'Python', 'AI', 'Machine Learning', 'DevOps', 'Docker',
            'AWS', 'Cloud Computing', 'Mobile', 'iOS', 'Android',
            'UI/UX', 'Design Systems', 'Figma', 'Startup', 'Empreendedorismo'
        ];

        foreach ($tagNames as $tagName) {
            Tag::updateOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
        }

        // Banners
        $banners = [
            [
                'title' => 'Bem-vindo ao HyTech Blog',
                'subtitle' => 'Tecnologia e Inovação',
                'description' => 'Descubra as últimas tendências em tecnologia, programação e inovação. Fique por dentro das novidades que estão transformando o mundo digital.',
                'image' => '', // Placeholder por enquanto
                'button_text' => 'Explorar Posts',
                'link_type' => 'url',
                'link_url' => '#posts',
                'target_blank' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Aprenda Programação',
                'subtitle' => 'Do Zero ao Profissional',
                'description' => 'Tutoriais completos, dicas práticas e projetos reais para você se tornar um desenvolvedor de sucesso.',
                'image' => '', // Placeholder por enquanto
                'button_text' => 'Ver Tutoriais',
                'link_type' => 'url',
                'link_url' => '/blog/categoria/programacao',
                'target_blank' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Inteligência Artificial',
                'subtitle' => 'O Futuro é Agora',
                'description' => 'Explore o fascinante mundo da IA e Machine Learning. Descubra como essas tecnologias estão revolucionando indústrias inteiras.',
                'image' => '', // Placeholder por enquanto
                'button_text' => 'Descobrir IA',
                'link_type' => 'url',
                'link_url' => '/blog/categoria/ia-machine-learning',
                'target_blank' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($banners as $bannerData) {
            Banner::updateOrCreate(
                ['title' => $bannerData['title']],
                $bannerData
            );
        }

        // Posts
        $posts = [
            [
                'title' => 'Como Começar com Laravel em 2024',
                'slug' => 'como-comecar-com-laravel-2024',
                'excerpt' => 'Guia completo para iniciantes que querem aprender Laravel, o framework PHP mais popular do mundo.',
                'content' => '<p>Laravel é um dos frameworks PHP mais populares e poderosos disponíveis hoje. Neste guia completo, vamos explorar tudo que você precisa saber para começar sua jornada com Laravel.</p><h2>Por que escolher Laravel?</h2><p>Laravel oferece uma sintaxe elegante e expressiva, além de ferramentas poderosas para desenvolvimento web moderno. Com recursos como Eloquent ORM, Blade templating engine e Artisan CLI, o Laravel torna o desenvolvimento web mais eficiente e prazeroso.</p><h2>Requisitos do Sistema</h2><p>Antes de começar, certifique-se de ter instalado:</p><ul><li>PHP 8.1 ou superior</li><li>Composer</li><li>Node.js e NPM</li><li>Um servidor web local (XAMPP, WAMP, ou Laravel Sail)</li></ul><h2>Instalação</h2><p>Para criar um novo projeto Laravel, use o comando:</p><pre><code>composer create-project laravel/laravel meu-projeto</code></pre><p>Após a instalação, você pode iniciar o servidor de desenvolvimento com:</p><pre><code>php artisan serve</code></pre><h2>Primeiros Passos</h2><p>Agora que você tem o Laravel instalado, vamos criar sua primeira rota e view. Isso vai te dar uma base sólida para começar a construir aplicações incríveis!</p>',
                'featured_image' => null,
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'is_featured' => true,
                'reading_time' => 8,
                'views_count' => 245,
                'category_id' => Category::where('slug', 'programacao')->first()->id,
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Tendências de Tecnologia para 2024',
                'slug' => 'tendencias-tecnologia-2024',
                'excerpt' => 'Descubra as principais tecnologias que vão dominar o mercado em 2024 e como se preparar para essas mudanças.',
                'content' => '<p>O ano de 2024 promete ser revolucionário para o mundo da tecnologia. Com avanços significativos em diversas áreas, é fundamental estar preparado para as mudanças que estão por vir.</p><h2>Inteligência Artificial Generativa</h2><p>A IA generativa continua evoluindo rapidamente. Ferramentas como ChatGPT, Midjourney e GitHub Copilot estão transformando a forma como trabalhamos e criamos conteúdo.</p><h2>Computação Quântica</h2><p>Embora ainda em estágios iniciais para uso comercial, a computação quântica está avançando e promete resolver problemas complexos que são impossíveis para computadores tradicionais.</p><h2>Edge Computing</h2><p>Com o crescimento da IoT, o processamento de dados na borda da rede se torna cada vez mais importante para reduzir latência e melhorar a eficiência.</p><h2>Sustentabilidade Digital</h2><p>A preocupação com o meio ambiente está levando ao desenvolvimento de tecnologias mais sustentáveis e eficientes energeticamente.</p>',
                'featured_image' => null,
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'is_featured' => true,
                'reading_time' => 6,
                'views_count' => 189,
                'category_id' => Category::where('slug', 'tecnologia')->first()->id,
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Introdução ao Machine Learning com Python',
                'slug' => 'introducao-machine-learning-python',
                'excerpt' => 'Aprenda os conceitos básicos de Machine Learning e como implementar seus primeiros algoritmos usando Python.',
                'content' => '<p>Machine Learning está revolucionando a forma como resolvemos problemas complexos. Neste tutorial, vamos começar do zero e construir nosso primeiro modelo de ML.</p><h2>O que é Machine Learning?</h2><p>Machine Learning é uma subárea da inteligência artificial que permite aos computadores aprender e fazer previsões sem serem explicitamente programados para cada tarefa específica.</p><h2>Tipos de Machine Learning</h2><h3>Aprendizado Supervisionado</h3><p>Usa dados rotulados para treinar modelos que podem fazer previsões em novos dados.</p><h3>Aprendizado Não Supervisionado</h3><p>Encontra padrões em dados sem rótulos, como clustering e redução de dimensionalidade.</p><h3>Aprendizado por Reforço</h3><p>O modelo aprende através de tentativa e erro, recebendo recompensas ou penalidades.</p><h2>Bibliotecas Essenciais</h2><ul><li>NumPy - Para computação numérica</li><li>Pandas - Para manipulação de dados</li><li>Scikit-learn - Para algoritmos de ML</li><li>Matplotlib - Para visualização</li></ul>',
                'featured_image' => null,
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'is_featured' => true,
                'reading_time' => 12,
                'views_count' => 301,
                'category_id' => Category::where('slug', 'ia-machine-learning')->first()->id,
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Princípios de Design UI/UX Moderno',
                'slug' => 'principios-design-ui-ux-moderno',
                'excerpt' => 'Entenda os fundamentos do design de interface e experiência do usuário para criar produtos digitais excepcionais.',
                'content' => '<p>O design UI/UX é fundamental para o sucesso de qualquer produto digital. Vamos explorar os princípios que tornam uma interface verdadeiramente excepcional.</p><h2>UI vs UX: Qual a diferença?</h2><p>UI (User Interface) foca na aparência visual, enquanto UX (User Experience) concentra-se na experiência completa do usuário.</p><h2>Princípios Fundamentais</h2><h3>1. Simplicidade</h3><p>Interfaces simples são mais fáceis de usar e entender. Remova elementos desnecessários e foque no essencial.</p><h3>2. Consistência</h3><p>Mantenha padrões visuais e comportamentais consistentes em todo o produto.</p><h3>3. Hierarquia Visual</h3><p>Use tamanho, cor e espaçamento para guiar o olhar do usuário pelos elementos mais importantes.</p><h3>4. Feedback</h3><p>Forneça feedback claro para todas as ações do usuário.</p><h2>Ferramentas Modernas</h2><ul><li>Figma - Design colaborativo</li><li>Adobe XD - Prototipagem</li><li>Sketch - Design de interface</li><li>Principle - Animações</li></ul>',
                'featured_image' => null,
                'status' => 'published',
                'published_at' => now()->subDays(4),
                'is_featured' => false,
                'reading_time' => 7,
                'views_count' => 156,
                'category_id' => Category::where('slug', 'design')->first()->id,
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Como Estruturar uma Startup de Tecnologia',
                'slug' => 'como-estruturar-startup-tecnologia',
                'excerpt' => 'Guia prático para empreendedores que querem criar e estruturar uma startup de tecnologia do zero.',
                'content' => '<p>Criar uma startup de tecnologia é desafiador, mas com a estrutura certa, você pode aumentar significativamente suas chances de sucesso.</p><h2>Validação da Ideia</h2><p>Antes de investir tempo e recursos, valide sua ideia com potenciais clientes. Use MVPs (Minimum Viable Products) para testar hipóteses.</p><h2>Equipe Fundadora</h2><p>Monte uma equipe complementar com habilidades em:</p><ul><li>Tecnologia (CTO)</li><li>Negócios (CEO)</li><li>Marketing/Vendas</li><li>Design/UX</li></ul><h2>Modelo de Negócios</h2><p>Defina claramente como sua startup vai gerar receita:</p><ul><li>SaaS (Software as a Service)</li><li>Marketplace</li><li>Freemium</li><li>Publicidade</li></ul><h2>Tecnologia e Desenvolvimento</h2><p>Escolha tecnologias que permitam:</p><ul><li>Escalabilidade</li><li>Manutenibilidade</li><li>Rapidez no desenvolvimento</li><li>Custo-benefício</li></ul><h2>Funding e Investimento</h2><p>Conheça as opções de financiamento:</p><ul><li>Bootstrapping</li><li>Angel Investors</li><li>Venture Capital</li><li>Crowdfunding</li></ul>',
                'featured_image' => null,
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'is_featured' => false,
                'reading_time' => 10,
                'views_count' => 203,
                'category_id' => Category::where('slug', 'negocios')->first()->id,
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Docker para Desenvolvedores: Guia Prático',
                'slug' => 'docker-desenvolvedores-guia-pratico',
                'excerpt' => 'Aprenda Docker do básico ao avançado e transforme sua forma de desenvolver e deployar aplicações.',
                'content' => '<p>Docker revolucionou a forma como desenvolvemos e deployamos aplicações. Neste guia, você vai aprender tudo que precisa para dominar essa tecnologia.</p><h2>O que é Docker?</h2><p>Docker é uma plataforma de containerização que permite empacotar aplicações e suas dependências em containers leves e portáveis.</p><h2>Vantagens do Docker</h2><ul><li>Consistência entre ambientes</li><li>Isolamento de aplicações</li><li>Facilidade de deploy</li><li>Escalabilidade</li><li>Eficiência de recursos</li></ul><h2>Conceitos Básicos</h2><h3>Images</h3><p>Templates read-only usados para criar containers.</p><h3>Containers</h3><p>Instâncias executáveis de images.</p><h3>Dockerfile</h3><p>Arquivo com instruções para construir uma image.</p><h3>Docker Compose</h3><p>Ferramenta para definir e executar aplicações multi-container.</p><h2>Comandos Essenciais</h2><pre><code># Baixar uma image\ndocker pull nginx\n\n# Executar um container\ndocker run -p 80:80 nginx\n\n# Listar containers\ndocker ps\n\n# Construir uma image\ndocker build -t minha-app .</code></pre>',
                'featured_image' => null,
                'status' => 'published',
                'published_at' => now()->subDays(6),
                'is_featured' => false,
                'reading_time' => 15,
                'views_count' => 278,
                'category_id' => Category::where('slug', 'programacao')->first()->id,
                'user_id' => $admin->id,
            ],
        ];

        foreach ($posts as $postData) {
            $post = Post::updateOrCreate(
                ['slug' => $postData['slug']],
                $postData
            );

            // Adicionar tags aos posts
            $randomTags = Tag::inRandomOrder()->take(rand(2, 5))->get();
            $post->tags()->sync($randomTags->pluck('id'));
        }

        $this->command->info('Blog seeder executado com sucesso!');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Database\Seeder;

class AlbumSeeder extends Seeder
{
    public function run(): void
    {
        $albums = [
            [
                'title' => 'Conferência Internacional de Tecnologia 2025',
                'description' => 'Momentos marcantes da maior conferência de tecnologia do ano, com palestrantes renomados e workshops inovadores.',
                'event_date' => now()->subDays(15),
                'location' => 'Centro de Convenções - São Paulo, SP',
                'is_active' => true,
                'priority' => 10,
                'photos' => [
                    ['title' => 'Abertura da Conferência', 'description' => 'Momento de abertura com mais de 2000 participantes'],
                    ['title' => 'Palestra Keynote', 'description' => 'Apresentação sobre o futuro da inteligência artificial'],
                    ['title' => 'Workshop de Laravel', 'description' => 'Sessão prática de desenvolvimento web moderno'],
                    ['title' => 'Networking Coffee Break', 'description' => 'Momento de networking entre os participantes'],
                    ['title' => 'Painel sobre Cloud Computing', 'description' => 'Debate sobre tendências em computação em nuvem'],
                    ['title' => 'Hackathon', 'description' => 'Competição de programação com equipes do mundo todo'],
                    ['title' => 'Premiação', 'description' => 'Entrega de prêmios aos melhores projetos'],
                    ['title' => 'Encerramento', 'description' => 'Encerramento com apresentação musical'],
                ],
            ],
            [
                'title' => 'Semana de Inovação e Empreendedorismo',
                'description' => 'Evento dedicado a startups e inovação, com pitches, mentorias e networking.',
                'event_date' => now()->subDays(30),
                'location' => 'Hub de Inovação - Rio de Janeiro, RJ',
                'is_active' => true,
                'priority' => 9,
                'photos' => [
                    ['title' => 'Pitch de Startups', 'description' => '15 startups apresentando suas soluções inovadoras'],
                    ['title' => 'Sessão de Mentoria', 'description' => 'Empreendedores recebendo orientação de experts'],
                    ['title' => 'Painel de Investidores', 'description' => 'Discussão sobre investimentos em tecnologia'],
                    ['title' => 'Área de Networking', 'description' => 'Espaço colaborativo para conexões'],
                    ['title' => 'Exposição de Produtos', 'description' => 'Demonstração de produtos inovadores'],
                    ['title' => 'Workshop de Design Thinking', 'description' => 'Atividade prática de metodologias ágeis'],
                ],
            ],
            [
                'title' => 'Encontro de Desenvolvedores PHP Brasil',
                'description' => 'Reunião da comunidade PHP com palestras técnicas, cases de sucesso e muito código.',
                'event_date' => now()->subDays(45),
                'location' => 'Auditório Tech Center - Belo Horizonte, MG',
                'is_active' => true,
                'priority' => 8,
                'photos' => [
                    ['title' => 'Credenciamento', 'description' => 'Recepção calorosa dos participantes'],
                    ['title' => 'Palestra sobre Laravel 11', 'description' => 'Novidades e melhores práticas'],
                    ['title' => 'Live Coding', 'description' => 'Sessão ao vivo de desenvolvimento'],
                    ['title' => 'Mesa Redonda', 'description' => 'Discussão sobre o futuro do PHP'],
                    ['title' => 'Área de Exposição', 'description' => 'Stands de empresas e comunidades'],
                    ['title' => 'Happy Hour', 'description' => 'Confraternização ao final do evento'],
                    ['title' => 'Sorteio de Brindes', 'description' => 'Momento de premiação dos participantes'],
                ],
            ],
            [
                'title' => 'Summit de Transformação Digital',
                'description' => 'Evento corporativo focado em estratégias de transformação digital para empresas.',
                'event_date' => now()->subDays(60),
                'location' => 'Hotel Executive - Brasília, DF',
                'is_active' => true,
                'priority' => 7,
                'photos' => [
                    ['title' => 'Palestra de Abertura', 'description' => 'CEOs discutindo transformação digital'],
                    ['title' => 'Casos de Sucesso', 'description' => 'Apresentação de empresas que se digitalizaram'],
                    ['title' => 'Workshop de IA', 'description' => 'Aplicações práticas de inteligência artificial'],
                    ['title' => 'Área VIP', 'description' => 'Espaço exclusivo para executivos'],
                    ['title' => 'Demonstração de Soluções', 'description' => 'Tecnologias em ação'],
                ],
            ],
            [
                'title' => 'Maratona de Programação Universitária',
                'description' => 'Competição entre universidades com desafios de algoritmos e programação.',
                'event_date' => now()->subDays(20),
                'location' => 'Campus Universitário - Porto Alegre, RS',
                'is_active' => true,
                'priority' => 6,
                'photos' => [
                    ['title' => 'Abertura da Competição', 'description' => 'Times se preparando para os desafios'],
                    ['title' => 'Sala de Competição', 'description' => 'Ambiente focado e competitivo'],
                    ['title' => 'Equipes Trabalhando', 'description' => 'Colaboração intensa entre membros'],
                    ['title' => 'Jurados Avaliando', 'description' => 'Comissão técnica analisando soluções'],
                    ['title' => 'Pódio dos Vencedores', 'description' => 'Celebração das equipes campeãs'],
                    ['title' => 'Confraternização', 'description' => 'Momento de descontração pós-competição'],
                ],
            ],
            [
                'title' => 'DevOps Day - Cultura e Ferramentas',
                'description' => 'Dia inteiro dedicado a práticas DevOps, CI/CD e automação.',
                'event_date' => now()->subDays(10),
                'location' => 'Tech Hub - Curitiba, PR',
                'is_active' => true,
                'priority' => 8,
                'photos' => [
                    ['title' => 'Palestra sobre CI/CD', 'description' => 'Pipelines automatizados na prática'],
                    ['title' => 'Workshop de Docker', 'description' => 'Containerização de aplicações'],
                    ['title' => 'Demo de Kubernetes', 'description' => 'Orquestração de containers em produção'],
                    ['title' => 'Monitoramento e Logs', 'description' => 'Ferramentas para observabilidade'],
                    ['title' => 'Coffee & Code', 'description' => 'Networking informal entre participantes'],
                ],
            ],
            [
                'title' => 'Mobile Dev Summit 2025',
                'description' => 'Conferência focada em desenvolvimento mobile com Flutter, React Native e tecnologias nativas.',
                'event_date' => now()->subDays(25),
                'location' => 'Centro de Eventos - Recife, PE',
                'is_active' => true,
                'priority' => 7,
                'photos' => [
                    ['title' => 'Keynote Mobile First', 'description' => 'Tendências do desenvolvimento mobile'],
                    ['title' => 'Flutter Workshop', 'description' => 'Criando apps multiplataforma'],
                    ['title' => 'UI/UX para Mobile', 'description' => 'Design de experiências móveis'],
                    ['title' => 'Performance Mobile', 'description' => 'Otimização de aplicativos'],
                    ['title' => 'Showcase de Apps', 'description' => 'Demonstração de aplicativos inovadores'],
                    ['title' => 'Networking Lounge', 'description' => 'Conexões entre desenvolvedores'],
                ],
            ],
            [
                'title' => 'Data Science & AI Conference',
                'description' => 'Evento sobre ciência de dados, machine learning e inteligência artificial.',
                'event_date' => now()->subDays(35),
                'location' => 'Convention Center - Florianópolis, SC',
                'is_active' => true,
                'priority' => 9,
                'photos' => [
                    ['title' => 'Machine Learning na Prática', 'description' => 'Implementação de modelos preditivos'],
                    ['title' => 'Deep Learning Workshop', 'description' => 'Redes neurais profundas'],
                    ['title' => 'Big Data Analytics', 'description' => 'Processamento de grandes volumes de dados'],
                    ['title' => 'Ética em IA', 'description' => 'Discussão sobre uso responsável de IA'],
                    ['title' => 'Casos Práticos', 'description' => 'Aplicações reais em empresas'],
                    ['title' => 'Hackathon de IA', 'description' => 'Competição de projetos com IA'],
                    ['title' => 'Premiação', 'description' => 'Reconhecimento dos melhores projetos'],
                ],
            ],
        ];

        foreach ($albums as $albumData) {
            $photosData = $albumData['photos'];
            unset($albumData['photos']);

            $album = Album::create($albumData);

            // URLs de exemplo do Unsplash para fotos realistas
            $unsplashCategories = ['tech', 'conference', 'people', 'business', 'workspace'];
            
            foreach ($photosData as $index => $photoData) {
                $category = $unsplashCategories[array_rand($unsplashCategories)];
                $randomId = rand(1000, 9999);
                
                Photo::create([
                    'album_id' => $album->id,
                    'title' => $photoData['title'],
                    'description' => $photoData['description'],
                    'alt_text' => $photoData['title'],
                    'image_path' => "https://source.unsplash.com/800x600/?{$category},{$randomId}",
                    'thumbnail_path' => "https://source.unsplash.com/400x300/?{$category},{$randomId}",
                    'order' => $index + 1,
                    'is_featured' => $index === 0, // Primeira foto é destaque
                    'file_size' => rand(500000, 3000000), // 500KB - 3MB
                    'width' => 800,
                    'height' => 600,
                ]);
            }

            // Atualizar contagem de fotos
            $album->updatePhotoCount();
        }

        $this->command->info('✅ Álbuns e fotos criados com sucesso!');
        $this->command->info("📸 Total: " . Album::count() . " álbuns com " . Photo::count() . " fotos");
    }
}

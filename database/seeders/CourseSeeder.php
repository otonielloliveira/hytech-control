<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CertificateType;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;
use App\Models\LessonMaterial;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar tipos de certificado
        $certificateTypes = [
            [
                'name' => 'Certificado de Participação',
                'slug' => 'certificado-de-participacao',
                'description' => 'Certificado para cursos básicos de participação',
                'min_completion_percentage' => 80,
                'requires_exam' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Certificado Profissional',
                'slug' => 'certificado-profissional',
                'description' => 'Certificado para cursos profissionalizantes',
                'min_completion_percentage' => 100,
                'requires_exam' => true,
                'min_exam_score' => 70.0,
                'is_active' => true,
            ],
            [
                'name' => 'Certificado de Especialização',
                'slug' => 'certificado-de-especializacao',
                'description' => 'Certificado para cursos avançados',
                'min_completion_percentage' => 100,
                'requires_exam' => true,
                'min_exam_score' => 85.0,
                'is_active' => true,
            ],
        ];

        foreach ($certificateTypes as $type) {
            CertificateType::updateOrCreate(
                ['slug' => $type['slug']], 
                $type
            );
        }

        // Criar cursos de exemplo
        $courses = [
            [
                'title' => 'Introdução ao Laravel',
                'slug' => 'introducao-ao-laravel',
                'description' => 'Aprenda os fundamentos do framework Laravel para desenvolvimento web moderno. Este curso aborda desde a instalação até conceitos avançados do Laravel, incluindo Eloquent ORM, Blade Templates, e muito mais.',
                'short_description' => 'Aprenda os fundamentos do framework Laravel para desenvolvimento web moderno.',
                'what_you_will_learn' => json_encode([
                    'Instalar e configurar o Laravel',
                    'Criar rotas e controllers',
                    'Trabalhar com Eloquent ORM',
                    'Implementar autenticação'
                ]),
                'requirements' => json_encode([
                    'Conhecimento básico de PHP',
                    'Familiaridade com HTML/CSS',
                    'Noções de bancos de dados'
                ]),
                'target_audience' => json_encode([
                    'Desenvolvedores PHP iniciantes',
                    'Estudantes de programação',
                    'Profissionais querendo aprender Laravel'
                ]),
                'level' => 'intermediate',
                'estimated_hours' => 20,
                'price' => 199.90,
                'promotional_price' => 149.90,
                'max_enrollments' => 100,
                'certificate_type_id' => 2,
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'JavaScript Moderno (ES6+)',
                'slug' => 'javascript-moderno-es6',
                'description' => 'Domine as funcionalidades mais recentes do JavaScript e torne-se um desenvolvedor front-end completo. Explore as funcionalidades do ES6+ incluindo arrow functions, destructuring, promises, async/await e muito mais.',
                'short_description' => 'Domine as funcionalidades mais recentes do JavaScript e torne-se um desenvolvedor front-end completo.',
                'what_you_will_learn' => json_encode([
                    'Dominar sintaxe ES6+',
                    'Trabalhar com Promises e Async/Await',
                    'Utilizar módulos ES6',
                    'Implementar programação funcional'
                ]),
                'requirements' => json_encode([
                    'Conhecimento básico de JavaScript',
                    'Familiaridade com HTML/CSS'
                ]),
                'target_audience' => json_encode([
                    'Desenvolvedores JavaScript',
                    'Front-end developers',
                    'Estudantes de programação web'
                ]),
                'level' => 'intermediate',
                'estimated_hours' => 15,
                'price' => 149.90,
                'max_enrollments' => 150,
                'certificate_type_id' => 2,
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'HTML e CSS para Iniciantes',
                'slug' => 'html-css-iniciantes',
                'description' => 'Curso completo de HTML5 e CSS3 para quem está começando no desenvolvimento web. Aprenda desde o básico até técnicas avançadas de HTML5 e CSS3, incluindo Flexbox e Grid Layout.',
                'short_description' => 'Curso completo de HTML5 e CSS3 para quem está começando no desenvolvimento web.',
                'what_you_will_learn' => json_encode([
                    'Estruturar páginas com HTML5',
                    'Estilizar com CSS3',
                    'Criar layouts responsivos',
                    'Usar Flexbox e Grid'
                ]),
                'requirements' => json_encode([
                    'Nenhum conhecimento prévio necessário',
                    'Computador com acesso à internet'
                ]),
                'target_audience' => json_encode([
                    'Iniciantes em programação',
                    'Estudantes',
                    'Profissionais de outras áreas'
                ]),
                'level' => 'beginner',
                'estimated_hours' => 12,
                'price' => 0, // Curso gratuito
                'max_enrollments' => 500,
                'certificate_type_id' => 1,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now(),
            ],
            [
                'title' => 'React.js Avançado',
                'slug' => 'reactjs-avancado',
                'description' => 'Aprofunde-se no React.js com hooks, context API, performance e testing. Curso avançado para desenvolvedores que já conhecem React e querem se especializar em técnicas avançadas.',
                'short_description' => 'Aprofunde-se no React.js com hooks, context API, performance e testing.',
                'what_you_will_learn' => json_encode([
                    'Dominar React Hooks',
                    'Gerenciar estado com Context API',
                    'Otimizar performance',
                    'Implementar testes'
                ]),
                'requirements' => json_encode([
                    'Conhecimento sólido de JavaScript',
                    'Experiência básica com React',
                    'Familiaridade com ES6+'
                ]),
                'target_audience' => json_encode([
                    'Desenvolvedores React intermediários',
                    'Front-end developers avançados',
                    'Arquitetos de software'
                ]),
                'level' => 'advanced',
                'estimated_hours' => 30,
                'price' => 299.90,
                'promotional_price' => 199.90,
                'max_enrollments' => 80,
                'certificate_type_id' => 3,
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now(),
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::updateOrCreate(
                ['slug' => $courseData['slug']], 
                $courseData
            );

            // Criar módulos para cada curso
            $this->createModulesForCourse($course);
        }
    }

    private function createModulesForCourse(Course $course)
    {
        $modules = [];

        switch ($course->slug) {
            case 'introducao-ao-laravel':
                $modules = [
                    [
                        'title' => 'Introdução e Instalação',
                        'description' => 'Configuração do ambiente e primeiros passos',
                        'lessons' => [
                            ['title' => 'O que é Laravel?', 'duration' => 15],
                            ['title' => 'Instalando o Laravel', 'duration' => 20],
                            ['title' => 'Estrutura de pastas', 'duration' => 25],
                        ]
                    ],
                    [
                        'title' => 'Rotas e Controllers',
                        'description' => 'Criando rotas e organizando com controllers',
                        'lessons' => [
                            ['title' => 'Criando rotas básicas', 'duration' => 30],
                            ['title' => 'Trabalhando com controllers', 'duration' => 35],
                            ['title' => 'Route parameters', 'duration' => 25],
                        ]
                    ],
                    [
                        'title' => 'Eloquent ORM',
                        'description' => 'Trabalhando com banco de dados',
                        'lessons' => [
                            ['title' => 'Criando Models', 'duration' => 30],
                            ['title' => 'Migrations e Schema', 'duration' => 40],
                            ['title' => 'Relacionamentos', 'duration' => 45],
                        ]
                    ],
                ];
                break;

            case 'javascript-moderno-es6':
                $modules = [
                    [
                        'title' => 'Sintaxe ES6+',
                        'description' => 'Novas funcionalidades do JavaScript moderno',
                        'lessons' => [
                            ['title' => 'Arrow Functions', 'duration' => 20],
                            ['title' => 'Destructuring', 'duration' => 25],
                            ['title' => 'Template Literals', 'duration' => 15],
                        ]
                    ],
                    [
                        'title' => 'Programação Assíncrona',
                        'description' => 'Promises, async/await e fetch API',
                        'lessons' => [
                            ['title' => 'Entendendo Promises', 'duration' => 30],
                            ['title' => 'Async/Await', 'duration' => 25],
                            ['title' => 'Fetch API', 'duration' => 35],
                        ]
                    ],
                ];
                break;

            case 'html-css-iniciantes':
                $modules = [
                    [
                        'title' => 'HTML Básico',
                        'description' => 'Estruturando páginas web com HTML5',
                        'lessons' => [
                            ['title' => 'Estrutura básica HTML', 'duration' => 20],
                            ['title' => 'Tags e elementos', 'duration' => 30],
                            ['title' => 'Formulários', 'duration' => 25],
                        ]
                    ],
                    [
                        'title' => 'CSS Fundamental',
                        'description' => 'Estilizando páginas com CSS3',
                        'lessons' => [
                            ['title' => 'Seletores CSS', 'duration' => 25],
                            ['title' => 'Box Model', 'duration' => 30],
                            ['title' => 'Layout com Flexbox', 'duration' => 40],
                        ]
                    ],
                ];
                break;

            case 'reactjs-avancado':
                $modules = [
                    [
                        'title' => 'React Hooks Avançados',
                        'description' => 'Dominando hooks customizados e avançados',
                        'lessons' => [
                            ['title' => 'useEffect avançado', 'duration' => 40],
                            ['title' => 'Custom Hooks', 'duration' => 45],
                            ['title' => 'useCallback e useMemo', 'duration' => 35],
                        ]
                    ],
                    [
                        'title' => 'Gerenciamento de Estado',
                        'description' => 'Context API e padrões avançados',
                        'lessons' => [
                            ['title' => 'Context API', 'duration' => 50],
                            ['title' => 'useReducer', 'duration' => 40],
                            ['title' => 'Padrões de estado', 'duration' => 45],
                        ]
                    ],
                ];
                break;
        }

        foreach ($modules as $index => $moduleData) {
            $module = CourseModule::updateOrCreate([
                'course_id' => $course->id,
                'slug' => Str::slug($moduleData['title']),
            ], [
                'title' => $moduleData['title'],
                'description' => $moduleData['description'],
                'sort_order' => $index + 1,
                'is_published' => true,
            ]);

            // Criar aulas para cada módulo
            foreach ($moduleData['lessons'] as $lessonIndex => $lessonData) {
                $lesson = CourseLesson::updateOrCreate([
                    'course_module_id' => $module->id,
                    'slug' => Str::slug($lessonData['title']),
                ], [
                    'title' => $lessonData['title'],
                    'description' => 'Descrição detalhada da aula: ' . $lessonData['title'],
                    'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Video exemplo
                    'video_duration' => $lessonData['duration'] * 60, // Converter minutos para segundos
                    'sort_order' => $lessonIndex + 1,
                    'is_published' => true,
                    'is_free' => $lessonIndex === 0, // Primeira aula como preview/free
                ]);

                // Adicionar material de apoio para algumas aulas
                if ($lessonIndex === 0) {
                    LessonMaterial::updateOrCreate([
                        'course_lesson_id' => $lesson->id,
                        'title' => 'Material de apoio - ' . $lessonData['title'],
                    ], [
                        'description' => 'Material complementar para esta aula',
                        'type' => 'pdf',
                        'file_path' => 'materials/sample.pdf',
                        'file_size' => 1024000,
                        'is_downloadable' => true,
                        'sort_order' => 1,
                    ]);
                }
            }
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ReplicateFilamentCustomizations extends Command
{
    protected $signature = 'filament:replicate-customizations';
    protected $description = 'Replica personalizações do LectureResource para todos os outros recursos';

    private array $resourceMappings = [
        'BannerResource' => ['singular' => 'Banner', 'plural' => 'Banners'],
        'BookResource' => ['singular' => 'Livro', 'plural' => 'Livros'],
        'BlogConfigResource' => ['singular' => 'Configuração do Blog', 'plural' => 'Configurações do Blog'],
        'CategoryResource' => ['singular' => 'Categoria', 'plural' => 'Categorias'],
        'CommentResource' => ['singular' => 'Comentário', 'plural' => 'Comentários'],
        'DownloadResource' => ['singular' => 'Download', 'plural' => 'Downloads'],
        'HangoutResource' => ['singular' => 'Hangout', 'plural' => 'Hangouts'],
        'NewsletterResource' => ['singular' => 'Newsletter', 'plural' => 'Newsletters'],
        'NoticeResource' => ['singular' => 'Aviso', 'plural' => 'Avisos'],
        'PollResource' => ['singular' => 'Enquete', 'plural' => 'Enquetes'],
        'PostResource' => ['singular' => 'Post', 'plural' => 'Posts'],
        'SeoMetaResource' => ['singular' => 'SEO Meta', 'plural' => 'SEO Metas'],
        'SidebarConfigResource' => ['singular' => 'Configuração da Sidebar', 'plural' => 'Configurações da Sidebar'],
    ];

    public function handle()
    {
        $this->info('🚀 Iniciando replicação das personalizações...');
        
        foreach ($this->resourceMappings as $resource => $labels) {
            $this->info("📝 Processando {$resource}...");
            
            $this->createViewPage($resource, $labels);
            $this->updateResourceClass($resource, $labels);
            $this->updateCreatePage($resource, $labels);
            $this->updateEditPage($resource, $labels);
        }
        
        $this->info('✅ Replicação concluída com sucesso!');
        return 0;
    }

    private function createViewPage($resource, $labels)
    {
        $resourceName = str_replace('Resource', '', $resource);
        $viewPagePath = app_path("Filament/Resources/{$resource}/Pages/View{$resourceName}.php");
        
        if (File::exists($viewPagePath)) {
            $this->line("   ⚠️  ViewPage já existe para {$resource}");
            return;
        }

        $namespace = "App\\Filament\\Resources\\{$resource}\\Pages";
        $className = "View{$resourceName}";
        
        $content = "<?php

namespace {$namespace};

use App\\Filament\\Resources\\{$resource};
use Filament\\Actions;
use Filament\\Resources\\Pages\\ViewRecord;

class {$className} extends ViewRecord
{
    protected static string \$resource = {$resource}::class;
    
    protected static ?string \$title = 'Visualizar {$labels['singular']}';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\\EditAction::make()
                ->label('Editar {$labels['singular']}')
                ->icon('heroicon-o-pencil'),
        ];
    }
}
";

        File::put($viewPagePath, $content);
        $this->line("   ✅ ViewPage criada para {$resource}");
    }

    private function updateResourceClass($resource, $labels)
    {
        $resourcePath = app_path("Filament/Resources/{$resource}.php");
        
        if (!File::exists($resourcePath)) {
            $this->line("   ⚠️  Arquivo {$resource}.php não encontrado");
            return;
        }

        $content = File::get($resourcePath);
        
        // Adicionar import do Infolist se não existir
        if (!str_contains($content, 'use Filament\\Infolists;')) {
            $content = str_replace(
                'use Filament\\Forms\\Form;',
                'use Filament\\Forms\\Form;' . "\n" . 'use Filament\\Infolists;' . "\n" . 'use Filament\\Infolists\\Infolist;',
                $content
            );
        }

        // Adicionar ViewAction na tabela se não existir
        if (!str_contains($content, 'Tables\\Actions\\ViewAction::make()')) {
            $pattern = '/->actions\(\[\s*(.*?)\s*\]\)/s';
            $replacement = function($matches) {
                $existingActions = trim($matches[1]);
                if (empty($existingActions)) {
                    return "->actions([\n                Tables\\Actions\\ViewAction::make()\n                    ->label('Visualizar')\n                    ->icon('heroicon-o-eye')\n                    ->color('info'),\n            ])";
                } else {
                    return "->actions([\n                Tables\\Actions\\ViewAction::make()\n                    ->label('Visualizar')\n                    ->icon('heroicon-o-eye')\n                    ->color('info'),\n                {$existingActions}\n            ])";
                }
            };
            $content = preg_replace_callback($pattern, $replacement, $content);
        }

        // Adicionar view page no getPages se não existir
        $resourceName = str_replace('Resource', '', $resource);
        if (!str_contains($content, "'view' =>")) {
            $pattern = "/'create' => Pages\\\\Create{$resourceName}::route\('\/create'\),/";
            $replacement = "'create' => Pages\\\\Create{$resourceName}::route('/create'),\n            'view' => Pages\\\\View{$resourceName}::route('/{record}'),";
            $content = preg_replace($pattern, $replacement, $content);
        }

        File::put($resourcePath, $content);
        $this->line("   ✅ {$resource}.php atualizado");
    }

    private function updateCreatePage($resource, $labels)
    {
        $resourceName = str_replace('Resource', '', $resource);
        $createPagePath = app_path("Filament/Resources/{$resource}/Pages/Create{$resourceName}.php");
        
        if (!File::exists($createPagePath)) {
            $this->line("   ⚠️  CreatePage não encontrada para {$resource}");
            return;
        }

        $content = File::get($createPagePath);
        
        // Adicionar título se não existir
        if (!str_contains($content, 'protected static ?string $title')) {
            $pattern = '/protected static string \$resource = .*?;/';
            $replacement = "protected static string \$resource = {$resource}::class;\n\n    protected static ?string \$title = 'Criar {$labels['singular']}';";
            $content = preg_replace($pattern, $replacement, $content);
        }

        File::put($createPagePath, $content);
        $this->line("   ✅ CreatePage atualizada para {$resource}");
    }

    private function updateEditPage($resource, $labels)
    {
        $resourceName = str_replace('Resource', '', $resource);
        $editPagePath = app_path("Filament/Resources/{$resource}/Pages/Edit{$resourceName}.php");
        
        if (!File::exists($editPagePath)) {
            $this->line("   ⚠️  EditPage não encontrada para {$resource}");
            return;
        }

        $content = File::get($editPagePath);
        
        // Adicionar título se não existir
        if (!str_contains($content, 'protected static ?string $title')) {
            $pattern = '/protected static string \$resource = .*?;/';
            $replacement = "protected static string \$resource = {$resource}::class;\n\n    protected static ?string \$title = 'Editar {$labels['singular']}';";
            $content = preg_replace($pattern, $replacement, $content);
        }

        File::put($editPagePath, $content);
        $this->line("   ✅ EditPage atualizada para {$resource}");
    }
}

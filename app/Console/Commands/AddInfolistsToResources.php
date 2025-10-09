<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddInfolistsToResources extends Command
{
    protected $signature = 'filament:add-infolists';
    protected $description = 'Adiciona infolists básicos aos recursos principais do Filament';

    public function handle()
    {
        $this->info('🔧 Adicionando infolists aos recursos...');
        
        $this->addCategoryInfolist();
        $this->addBannerInfolist();
        $this->addNoticeInfolist();
        
        $this->info('✅ Infolists adicionados com sucesso!');
        return 0;
    }

    private function addCategoryInfolist()
    {
        $resourcePath = app_path('Filament/Resources/CategoryResource.php');
        
        if (!File::exists($resourcePath)) {
            return;
        }

        $content = File::get($resourcePath);
        
        $infolistMethod = '
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\\Components\\Section::make(\'Informações da Categoria\')
                    ->schema([
                        Infolists\\Components\\Grid::make(2)
                            ->schema([
                                Infolists\\Components\\TextEntry::make(\'name\')
                                    ->label(\'Nome\')
                                    ->size(Infolists\\Components\\TextEntry\\TextEntrySize::Large)
                                    ->weight(\'bold\'),
                                
                                Infolists\\Components\\TextEntry::make(\'slug\')
                                    ->label(\'Slug\')
                                    ->badge()
                                    ->color(\'gray\'),
                                
                                Infolists\\Components\\IconEntry::make(\'is_visible\')
                                    ->label(\'Visível\')
                                    ->boolean()
                                    ->trueIcon(\'heroicon-o-check-circle\')
                                    ->falseIcon(\'heroicon-o-x-circle\')
                                    ->trueColor(\'success\')
                                    ->falseColor(\'danger\'),
                                
                                Infolists\\Components\\TextEntry::make(\'sort\')
                                    ->label(\'Ordem\')
                                    ->numeric(),
                            ]),
                    ]),
                
                Infolists\\Components\\Section::make(\'Descrição\')
                    ->schema([
                        Infolists\\Components\\TextEntry::make(\'description\')
                            ->label(\'\')
                            ->placeholder(\'Nenhuma descrição fornecida\'),
                    ])
                    ->collapsible()
                    ->collapsed(),
                
                Infolists\\Components\\Section::make(\'Informações do Sistema\')
                    ->schema([
                        Infolists\\Components\\Grid::make(2)
                            ->schema([
                                Infolists\\Components\\TextEntry::make(\'created_at\')
                                    ->label(\'Criado em\')
                                    ->dateTime(\'d/m/Y H:i\'),
                                
                                Infolists\\Components\\TextEntry::make(\'updated_at\')
                                    ->label(\'Atualizado em\')
                                    ->dateTime(\'d/m/Y H:i\'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
';

        if (!str_contains($content, 'public static function infolist')) {
            $pattern = '/public static function getRelations\(\): array/';
            $replacement = $infolistMethod . "\n    public static function getRelations(): array";
            $content = preg_replace($pattern, $replacement, $content);
            
            File::put($resourcePath, $content);
            $this->line('   ✅ Infolist adicionado ao CategoryResource');
        }
    }

    private function addBannerInfolist()
    {
        $resourcePath = app_path('Filament/Resources/BannerResource.php');
        
        if (!File::exists($resourcePath)) {
            return;
        }

        $content = File::get($resourcePath);
        
        $infolistMethod = '
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\\Components\\Section::make(\'Informações do Banner\')
                    ->schema([
                        Infolists\\Components\\Split::make([
                            Infolists\\Components\\Grid::make(2)
                                ->schema([
                                    Infolists\\Components\\TextEntry::make(\'title\')
                                        ->label(\'Título\')
                                        ->size(Infolists\\Components\\TextEntry\\TextEntrySize::Large)
                                        ->weight(\'bold\')
                                        ->columnSpanFull(),
                                    
                                    Infolists\\Components\\TextEntry::make(\'type\')
                                        ->label(\'Tipo\')
                                        ->badge()
                                        ->color(\'primary\'),
                                    
                                    Infolists\\Components\\IconEntry::make(\'is_active\')
                                        ->label(\'Ativo\')
                                        ->boolean()
                                        ->trueIcon(\'heroicon-o-check-circle\')
                                        ->falseIcon(\'heroicon-o-x-circle\')
                                        ->trueColor(\'success\')
                                        ->falseColor(\'danger\'),
                                    
                                    Infolists\\Components\\TextEntry::make(\'priority\')
                                        ->label(\'Prioridade\')
                                        ->numeric()
                                        ->badge()
                                        ->color(fn ($state) => match (true) {
                                            $state >= 80 => \'success\',
                                            $state >= 50 => \'warning\',
                                            default => \'danger\',
                                        }),
                                ]),
                            
                            Infolists\\Components\\ImageEntry::make(\'image\')
                                ->label(\'Imagem\')
                                ->size(200)
                                ->grow(false),
                        ])->from(\'lg\'),
                    ]),
                
                Infolists\\Components\\Section::make(\'Conteúdo\')
                    ->schema([
                        Infolists\\Components\\TextEntry::make(\'content\')
                            ->label(\'\')
                            ->html()
                            ->placeholder(\'Nenhum conteúdo fornecido\'),
                    ])
                    ->collapsible(),
            ]);
    }
';

        if (!str_contains($content, 'public static function infolist')) {
            $pattern = '/public static function getRelations\(\): array/';
            $replacement = $infolistMethod . "\n    public static function getRelations(): array";
            $content = preg_replace($pattern, $replacement, $content);
            
            File::put($resourcePath, $content);
            $this->line('   ✅ Infolist adicionado ao BannerResource');
        }
    }

    private function addNoticeInfolist()
    {
        $resourcePath = app_path('Filament/Resources/NoticeResource.php');
        
        if (!File::exists($resourcePath)) {
            return;
        }

        $content = File::get($resourcePath);
        
        $infolistMethod = '
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\\Components\\Section::make(\'Informações do Aviso\')
                    ->schema([
                        Infolists\\Components\\Grid::make(2)
                            ->schema([
                                Infolists\\Components\\TextEntry::make(\'title\')
                                    ->label(\'Título\')
                                    ->size(Infolists\\Components\\TextEntry\\TextEntrySize::Large)
                                    ->weight(\'bold\')
                                    ->columnSpanFull(),
                                
                                Infolists\\Components\\TextEntry::make(\'type\')
                                    ->label(\'Tipo\')
                                    ->badge()
                                    ->color(fn ($state) => match($state) {
                                        \'info\' => \'info\',
                                        \'warning\' => \'warning\',
                                        \'danger\' => \'danger\',
                                        \'success\' => \'success\',
                                        default => \'gray\'
                                    }),
                                
                                Infolists\\Components\\IconEntry::make(\'is_active\')
                                    ->label(\'Ativo\')
                                    ->boolean()
                                    ->trueIcon(\'heroicon-o-check-circle\')
                                    ->falseIcon(\'heroicon-o-x-circle\')
                                    ->trueColor(\'success\')
                                    ->falseColor(\'danger\'),
                                
                                Infolists\\Components\\TextEntry::make(\'priority\')
                                    ->label(\'Prioridade\')
                                    ->numeric()
                                    ->badge()
                                    ->color(fn ($state) => match (true) {
                                        $state >= 80 => \'success\',
                                        $state >= 50 => \'warning\',
                                        default => \'danger\',
                                    }),
                            ]),
                    ]),
                
                Infolists\\Components\\Section::make(\'Conteúdo\')
                    ->schema([
                        Infolists\\Components\\TextEntry::make(\'content\')
                            ->label(\'\')
                            ->html()
                            ->placeholder(\'Nenhum conteúdo fornecido\'),
                    ])
                    ->collapsible(),
            ]);
    }
';

        if (!str_contains($content, 'public static function infolist')) {
            $pattern = '/public static function getRelations\(\): array/';
            $replacement = $infolistMethod . "\n    public static function getRelations(): array";
            $content = preg_replace($pattern, $replacement, $content);
            
            File::put($resourcePath, $content);
            $this->line('   ✅ Infolist adicionado ao NoticeResource');
        }
    }
}

<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Resources\VideoResource;
use App\Models\Video;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\DB;

class BulkCreateVideos extends Page
{
    use InteractsWithFormActions;

    protected static string $resource = VideoResource::class;

    protected static string $view = 'filament.resources.video-resource.pages.bulk-create-videos';

    protected static ?string $title = 'Upload em Lote de Vídeos';

    protected static ?string $navigationLabel = 'Upload em Lote';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'videos' => [
                ['video_url' => '', 'title' => '', 'category' => '', 'is_active' => true],
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Upload Múltiplos Vídeos')
                    ->description('Adicione vários vídeos de uma vez. Cole os links do YouTube, Vimeo ou outras plataformas.')
                    ->schema([
                        Forms\Components\Repeater::make('videos')
                            ->label('Vídeos')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('video_url')
                                            ->label('URL do Vídeo')
                                            ->required()
                                            ->url()
                                            ->placeholder('https://www.youtube.com/watch?v=...')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                if ($state) {
                                                    // Extrair dados automaticamente
                                                    $this->extractVideoData($state, $set, $get);
                                                }
                                            }),
                                            
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título')
                                            ->required()
                                            ->placeholder('Título do vídeo'),
                                    ]),
                                    
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('video_platform')
                                            ->label('Plataforma')
                                            ->options([
                                                'youtube' => 'YouTube',
                                                'vimeo' => 'Vimeo',
                                                'local' => 'Local/Outro',
                                            ])
                                            ->disabled(),
                                            
                                        Forms\Components\TextInput::make('category')
                                            ->label('Categoria')
                                            ->datalist([
                                                'entrevistas' => 'Entrevistas',
                                                'palestras' => 'Palestras',
                                                'eventos' => 'Eventos',
                                                'tutoriais' => 'Tutoriais',
                                                'debates' => 'Debates',
                                                'documentarios' => 'Documentários',
                                                'noticias' => 'Notícias',
                                                'educativo' => 'Educativo',
                                            ]),
                                            
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Ativo')
                                            ->default(true),
                                    ]),
                                    
                                Forms\Components\Textarea::make('description')
                                    ->label('Descrição (Opcional)')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                    
                                Forms\Components\Hidden::make('video_id'),
                                Forms\Components\Hidden::make('thumbnail_url'),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Novo Vídeo')
                            ->addActionLabel('Adicionar Outro Vídeo')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->cloneable()
                            ->minItems(1)
                            ->defaultItems(1),
                    ]),
                    
                Forms\Components\Section::make('Configurações Globais')
                    ->description('Estas configurações serão aplicadas a todos os vídeos que não tiverem valores específicos.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('default_category')
                                    ->label('Categoria Padrão')
                                    ->datalist([
                                        'entrevistas' => 'Entrevistas',
                                        'palestras' => 'Palestras',
                                        'eventos' => 'Eventos',
                                        'tutoriais' => 'Tutoriais',
                                        'debates' => 'Debates',
                                        'documentarios' => 'Documentários',
                                        'noticias' => 'Notícias',
                                        'educativo' => 'Educativo',
                                    ]),
                                    
                                Forms\Components\DatePicker::make('default_published_date')
                                    ->label('Data de Publicação Padrão')
                                    ->default(now()),
                                    
                                Forms\Components\TextInput::make('default_priority')
                                    ->label('Prioridade Padrão')
                                    ->numeric()
                                    ->default(0),
                            ]),
                            
                        Forms\Components\TagsInput::make('default_tags')
                            ->label('Tags Padrão')
                            ->separator(',')
                            ->placeholder('Digite tags separadas por vírgula'),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    protected function extractVideoData(string $url, callable $set, callable $get): void
    {
        // YouTube patterns
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $matches)) {
            $videoId = $matches[1];
            $set('video_id', $videoId);
            $set('video_platform', 'youtube');
            $set('thumbnail_url', "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg");
            
            // Se não tem título, tenta extrair do YouTube (básico)
            if (!$get('title')) {
                $set('title', 'YouTube Video - ' . $videoId);
            }
        } elseif (preg_match('/youtu\.be\/([^?]+)/', $url, $matches)) {
            $videoId = $matches[1];
            $set('video_id', $videoId);
            $set('video_platform', 'youtube');
            $set('thumbnail_url', "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg");
            
            if (!$get('title')) {
                $set('title', 'YouTube Video - ' . $videoId);
            }
        }
        // Vimeo patterns
        elseif (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            $videoId = $matches[1];
            $set('video_id', $videoId);
            $set('video_platform', 'vimeo');
            $set('thumbnail_url', "https://vumbnail.com/{$videoId}.jpg");
            
            if (!$get('title')) {
                $set('title', 'Vimeo Video - ' . $videoId);
            }
        }
    }

    public function create(): void
    {
        try {
            $data = $this->form->getState();
            
            DB::beginTransaction();
            
            $created = 0;
            $errors = [];
            
            foreach ($data['videos'] as $videoData) {
                try {
                    // Aplicar valores padrão se não especificados
                    if (empty($videoData['category']) && !empty($data['default_category'])) {
                        $videoData['category'] = $data['default_category'];
                    }
                    
                    if (empty($videoData['published_date']) && !empty($data['default_published_date'])) {
                        $videoData['published_date'] = $data['default_published_date'];
                    }
                    
                    if (empty($videoData['priority']) && !empty($data['default_priority'])) {
                        $videoData['priority'] = $data['default_priority'];
                    }
                    
                    if (empty($videoData['tags']) && !empty($data['default_tags'])) {
                        $videoData['tags'] = $data['default_tags'];
                    }
                    
                    // Limpar dados vazios
                    $videoData = array_filter($videoData, function ($value) {
                        return $value !== null && $value !== '';
                    });
                    
                    Video::create($videoData);
                    $created++;
                } catch (\Exception $e) {
                    $errors[] = "Erro no vídeo '{$videoData['title']}': " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            if ($created > 0) {
                $this->getSuccessNotification($created)->send();
            }
            
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    \Filament\Notifications\Notification::make()
                        ->title('Erro')
                        ->body($error)
                        ->danger()
                        ->send();
                }
            }
            
            if ($created > 0) {
                $this->redirect(VideoResource::getUrl('index'));
            }
            
        } catch (Halt $exception) {
            return;
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Filament\Notifications\Notification::make()
                ->title('Erro')
                ->body('Erro ao criar vídeos: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getSuccessNotification(int $count): \Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->title('Sucesso!')
            ->body("$count vídeo(s) criado(s) com sucesso!")
            ->success();
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Criar Todos os Vídeos')
                ->action('create')
                ->color('primary')
                ->icon('heroicon-o-plus'),
                
            Actions\Action::make('cancel')
                ->label('Cancelar')
                ->url(VideoResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
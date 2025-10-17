<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Filament\Resources\VideoResource\RelationManagers;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    
    protected static ?string $navigationLabel = 'Vídeos';
    
    protected static ?string $modelLabel = 'Vídeo';
    
    protected static ?string $pluralModelLabel = 'Vídeos';
    
    protected static ?string $navigationGroup = 'Galeria';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
                    
                Forms\Components\Section::make('Vídeo')
                    ->schema([
                        Forms\Components\TextInput::make('video_url')
                            ->label('URL do Vídeo')
                            ->required()
                            ->url()
                            ->helperText('Cole aqui a URL completa do YouTube, Vimeo ou outro serviço. O sistema extrairá automaticamente as informações.')
                            ->placeholder('Ex: https://www.youtube.com/watch?v=dQw4w9WgXcQ')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    // Extrair ID do YouTube
                                    if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $state, $matches)) {
                                        $set('video_id', $matches[1]);
                                        $set('video_platform', 'youtube');
                                        $set('thumbnail_url', "https://img.youtube.com/vi/{$matches[1]}/maxresdefault.jpg");
                                    } elseif (preg_match('/youtu\.be\/([^?]+)/', $state, $matches)) {
                                        $set('video_id', $matches[1]);
                                        $set('video_platform', 'youtube');
                                        $set('thumbnail_url', "https://img.youtube.com/vi/{$matches[1]}/maxresdefault.jpg");
                                    }
                                    // Extrair ID do Vimeo
                                    elseif (preg_match('/vimeo\.com\/(\d+)/', $state, $matches)) {
                                        $set('video_id', $matches[1]);
                                        $set('video_platform', 'vimeo');
                                        $set('thumbnail_url', "https://vumbnail.com/{$matches[1]}.jpg");
                                    }
                                }
                            })
                            ->columnSpanFull(),
                            
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('video_platform')
                                    ->label('Plataforma')
                                    ->options([
                                        'youtube' => 'YouTube',
                                        'vimeo' => 'Vimeo',
                                        'local' => 'Local/Outro',
                                    ])
                                    ->default('youtube')
                                    ->disabled(),
                                    
                                Forms\Components\TextInput::make('video_id')
                                    ->label('ID do Vídeo')
                                    ->disabled()
                                    ->helperText('Extraído automaticamente'),
                                    
                                Forms\Components\TextInput::make('duration')
                                    ->label('Duração')
                                    ->placeholder('Ex: 5:30')
                                    ->helperText('Formato: mm:ss ou hh:mm:ss'),
                            ]),
                    ]),
                    
                Forms\Components\Section::make('Miniatura e Categoria')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('thumbnail_url')
                                    ->label('URL da Miniatura')
                                    ->url()
                                    ->helperText('Preenchido automaticamente ou insira URL personalizada'),
                                    
                                Forms\Components\Placeholder::make('thumbnail_preview')
                                    ->label('Preview da Miniatura')
                                    ->content(function ($get) {
                                        $thumbnailUrl = $get('thumbnail_url');
                                        if ($thumbnailUrl) {
                                            return new \Illuminate\Support\HtmlString(
                                                "<img src='{$thumbnailUrl}' style='max-width: 200px; max-height: 120px; border-radius: 8px; object-fit: cover;' alt='Preview'>"
                                            );
                                        }
                                        return 'Nenhuma miniatura disponível';
                                    }),
                            ]),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
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
                                    ])
                                    ->helperText('Digite ou selecione uma categoria'),
                                    
                                Forms\Components\DatePicker::make('published_date')
                                    ->label('Data de Publicação')
                                    ->default(now()),
                            ]),
                    ]),
                    
                Forms\Components\Section::make('Tags e SEO')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Tags')
                            ->separator(',')
                            ->columnSpanFull(),
                    ]),
                    
                Forms\Components\Section::make('Configurações')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                            
                        Forms\Components\TextInput::make('priority')
                            ->label('Prioridade')
                            ->numeric()
                            ->default(0)
                            ->helperText('Maior número = maior prioridade'),
                            
                        Forms\Components\TextInput::make('views_count')
                            ->label('Contagem de Visualizações')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_url')
                    ->label('Miniatura')
                    ->size(80)
                    ->defaultImageUrl(url('/images/video-placeholder.jpg'))
                    ->extraAttributes(['style' => 'border-radius: 8px;']),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->description(fn (Video $record): string => $record->short_description)
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('video_platform')
                    ->label('Plataforma')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'youtube' => 'danger',
                        'vimeo' => 'info', 
                        'local' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'youtube' => 'heroicon-o-play-circle',
                        'vimeo' => 'heroicon-o-video-camera',
                        'local' => 'heroicon-o-film',
                        default => 'heroicon-o-video-camera',
                    }),
                    
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duração')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Visualizações')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(fn (int $state): string => number_format($state)),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('published_date')
                    ->label('Publicado em')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Apenas ativos')
                    ->falseLabel('Apenas inativos')
                    ->native(false),
                    
                Tables\Filters\SelectFilter::make('video_platform')
                    ->label('Plataforma')
                    ->options([
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo',
                        'local' => 'Local/Outro',
                    ])
                    ->native(false),
                    
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoria')
                    ->options(function () {
                        return Video::query()
                            ->whereNotNull('category')
                            ->distinct()
                            ->pluck('category', 'category')
                            ->toArray();
                    })
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Ver no Site')
                        ->icon('heroicon-o-eye')
                        ->url(fn (Video $record): string => route('videos.show', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('preview')
                        ->label('Preview do Vídeo')
                        ->icon('heroicon-o-play')
                        ->modalContent(fn (Video $record) => view('filament.video-preview', ['video' => $record]))
                        ->modalWidth('5xl'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                ])->label('Ações')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Ativar Selecionados')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => true])))
                        ->color('success')
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desativar Selecionados')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => false])))
                        ->color('warning')
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                ]),
            ])
            ->defaultSort('priority', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'bulk-create' => Pages\BulkCreateVideos::route('/bulk-create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
    
    public static function canAccess(): bool
    {
        return Auth::user()->canManageSettings() || Auth::user()->is_admin;
    }
}

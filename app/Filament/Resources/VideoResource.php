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
                            ->helperText('Cole aqui a URL do YouTube, Vimeo ou outro serviço')
                            ->columnSpanFull(),
                            
                        Forms\Components\Select::make('video_platform')
                            ->label('Plataforma')
                            ->options([
                                'youtube' => 'YouTube',
                                'vimeo' => 'Vimeo',
                                'local' => 'Local/Outro',
                            ])
                            ->default('youtube'),
                            
                        Forms\Components\TextInput::make('video_id')
                            ->label('ID do Vídeo')
                            ->helperText('Será extraído automaticamente da URL'),
                            
                        Forms\Components\TextInput::make('duration')
                            ->label('Duração')
                            ->placeholder('Ex: 5:30')
                            ->helperText('Formato: mm:ss ou hh:mm:ss'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Imagem e Categoria')
                    ->schema([
                        Forms\Components\TextInput::make('thumbnail_url')
                            ->label('URL da Miniatura')
                            ->url()
                            ->helperText('Deixe vazio para usar miniatura automática')
                            ->columnSpanFull(),
                            
                        Forms\Components\TextInput::make('category')
                            ->label('Categoria')
                            ->datalist([
                                'entrevistas',
                                'palestras',
                                'eventos',
                                'tutoriais',
                                'debates',
                                'documentarios',
                            ]),
                            
                        Forms\Components\DatePicker::make('published_date')
                            ->label('Data de Publicação'),
                    ])->columns(2),
                    
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
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Miniatura')
                    ->circular()
                    ->defaultImageUrl(url('/images/video-placeholder.jpg')),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                    
                Tables\Columns\TextColumn::make('video_platform')
                    ->label('Plataforma')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'youtube' => 'danger',
                        'vimeo' => 'info',
                        'local' => 'gray',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->searchable()
                    ->badge(),
                    
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duração'),
                    
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Visualizações')
                    ->numeric()
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->numeric()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('published_date')
                    ->label('Publicado em')
                    ->date('d/m/Y')
                    ->sortable(),
                    
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
                Tables\Actions\ViewAction::make()
                    ->url(fn (Video $record): string => route('videos.show', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}

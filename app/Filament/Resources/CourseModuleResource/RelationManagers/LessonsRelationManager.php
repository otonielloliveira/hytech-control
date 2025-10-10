<?php

namespace App\Filament\Resources\CourseModuleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';

    protected static ?string $title = 'Aulas do Módulo';

    protected static ?string $modelLabel = 'Aula';

    protected static ?string $pluralModelLabel = 'Aulas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Aula')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título da Aula')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => 
                                $context === 'create' ? $set('slug', Str::slug($state)) : null),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Conteúdo da Aula')
                    ->schema([
                        Forms\Components\Textarea::make('content')
                            ->label('Conteúdo Textual')
                            ->rows(5)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('video_url')
                            ->label('URL do Vídeo')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://youtube.com/watch?v=... ou https://vimeo.com/...')
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('video_duration')
                            ->label('Duração do Vídeo (segundos)')
                            ->numeric()
                            ->suffix('segundos')
                            ->placeholder('Ex: 900 (para 15 minutos)'),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagem da Aula')
                            ->image()
                            ->disk('public')
                            ->directory('course-lessons')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('450'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configurações')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem')
                            ->numeric()
                            ->required()
                            ->default(fn() => $this->getOwnerRecord()->lessons()->max('sort_order') + 1),
                        
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publicado')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('is_free')
                            ->label('Aula Gratuita')
                            ->helperText('Aulas gratuitas podem ser assistidas sem matrícula')
                            ->default(false),
                    ])
                    ->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->square()
                    ->size(50),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('video_duration')
                    ->label('Duração')
                    ->formatStateUsing(fn (int $state = null): string => 
                        $state ? gmdate('H:i:s', $state) : '-'
                    )
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publicado')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_free')
                    ->label('Gratuita')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publicado'),
                
                Tables\Filters\TernaryFilter::make('is_free')
                    ->label('Gratuita'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['course_module_id'] = $this->getOwnerRecord()->id;
                        
                        if (empty($data['slug'])) {
                            $data['slug'] = Str::slug($data['title']);
                        }
                        
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if (empty($data['slug'])) {
                            $data['slug'] = Str::slug($data['title']);
                        }
                        
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }
}

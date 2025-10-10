<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use App\Models\CertificateType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-play';
    
    protected static ?string $navigationLabel = 'Cursos';
    
    protected static ?string $modelLabel = 'Curso';
    
    protected static ?string $pluralModelLabel = 'Cursos';
    
    protected static ?string $navigationGroup = 'Gestão de Cursos';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => 
                                $context === 'create' ? $set('slug', Str::slug($state)) : null),
                            
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                            
                        Forms\Components\Textarea::make('short_description')
                            ->label('Descrição Curta')
                            ->rows(2)
                            ->maxLength(500),
                            
                        Forms\Components\RichEditor::make('description')
                            ->label('Descrição Completa')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Mídia')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagem de Capa')
                            ->image()
                            ->directory('courses')
                            ->maxSize(2048),
                            
                        Forms\Components\TextInput::make('trailer_video')
                            ->label('Vídeo de Apresentação')
                            ->url()
                            ->placeholder('https://youtube.com/watch?v=...'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Configurações')
                    ->schema([
                        Forms\Components\Select::make('certificate_type_id')
                            ->label('Tipo de Certificado')
                            ->relationship('certificateType', 'name')
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('level')
                            ->label('Nível')
                            ->options([
                                'beginner' => 'Iniciante',
                                'intermediate' => 'Intermediário',
                                'advanced' => 'Avançado',
                            ])
                            ->default('beginner')
                            ->required(),
                            
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Rascunho',
                                'published' => 'Publicado',
                                'archived' => 'Arquivado',
                            ])
                            ->default('draft')
                            ->required(),
                            
                        Forms\Components\TextInput::make('estimated_hours')
                            ->label('Horas Estimadas')
                            ->numeric()
                            ->suffix('horas'),
                            
                        Forms\Components\TextInput::make('max_enrollments')
                            ->label('Máximo de Matrículas')
                            ->numeric()
                            ->hint('Deixe vazio para ilimitado'),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Preços')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Preço')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0),
                            
                        Forms\Components\TextInput::make('promotional_price')
                            ->label('Preço Promocional')
                            ->numeric()
                            ->prefix('R$'),
                            
                        Forms\Components\DateTimePicker::make('promotion_ends_at')
                            ->label('Promoção Termina em')
                            ->displayFormat('d/m/Y H:i'),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Conteúdo do Curso')
                    ->schema([
                        Forms\Components\TagsInput::make('requirements')
                            ->label('Pré-requisitos')
                            ->placeholder('Digite um pré-requisito e pressione Enter'),
                            
                        Forms\Components\TagsInput::make('what_you_will_learn')
                            ->label('O que você vai aprender')
                            ->placeholder('Digite um tópico e pressione Enter'),
                            
                        Forms\Components\TagsInput::make('target_audience')
                            ->label('Público-alvo')
                            ->placeholder('Digite um tipo de público e pressione Enter'),
                    ]),
                    
                Forms\Components\Section::make('Configurações Adicionais')
                    ->schema([
                        Forms\Components\Textarea::make('instructor_notes')
                            ->label('Notas do Instrutor')
                            ->rows(3),
                            
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Curso em Destaque')
                            ->default(false),
                            
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem de Exibição')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->circular(),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('level')
                    ->label('Nível')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'beginner' => 'success',
                        'intermediate' => 'warning',
                        'advanced' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'beginner' => 'Iniciante',
                        'intermediate' => 'Intermediário',
                        'advanced' => 'Avançado',
                    }),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Rascunho',
                        'published' => 'Publicado',
                        'archived' => 'Arquivado',
                    }),
                    
                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->money('BRL')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('promotional_price')
                    ->label('Preço Promocional')
                    ->money('BRL')
                    ->placeholder('N/A'),
                    
                Tables\Columns\TextColumn::make('enrollments_count')
                    ->label('Matrículas')
                    ->counts('enrollments')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('modules_count')
                    ->label('Módulos')
                    ->counts('modules')
                    ->badge()
                    ->color('primary'),
                    
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destaque')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Rascunho',
                        'published' => 'Publicado',
                        'archived' => 'Arquivado',
                    ]),
                    
                Tables\Filters\SelectFilter::make('level')
                    ->label('Nível')
                    ->options([
                        'beginner' => 'Iniciante',
                        'intermediate' => 'Intermediário',
                        'advanced' => 'Avançado',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Em Destaque')
                    ->placeholder('Todos')
                    ->trueLabel('Em destaque')
                    ->falseLabel('Não destacados'),
                    
                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('Preço mínimo')
                            ->numeric()
                            ->prefix('R$'),
                        Forms\Components\TextInput::make('price_to')
                            ->label('Preço máximo')
                            ->numeric()
                            ->prefix('R$'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['price_from'], fn ($q, $price) => $q->where('price', '>=', $price))
                            ->when($data['price_to'], fn ($q, $price) => $q->where('price', '<=', $price));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('publish')
                    ->label('Publicar')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->visible(fn (Course $record) => $record->status === 'draft')
                    ->action(function (Course $record) {
                        $record->update([
                            'status' => 'published',
                            'published_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation(),
                    
                Tables\Actions\Action::make('archive')
                    ->label('Arquivar')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->visible(fn (Course $record) => $record->status === 'published')
                    ->action(function (Course $record) {
                        $record->update(['status' => 'archived']);
                    })
                    ->requiresConfirmation(),
                    
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplicar')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (Course $record) {
                        $newCourse = $record->replicate();
                        $newCourse->title = $record->title . ' (Cópia)';
                        $newCourse->slug = $record->slug . '-copy';
                        $newCourse->status = 'draft';
                        $newCourse->published_at = null;
                        $newCourse->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publicar Selecionados')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function (Course $record) {
                                if ($record->status === 'draft') {
                                    $record->update([
                                        'status' => 'published',
                                        'published_at' => now(),
                                    ]);
                                }
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\ModulesRelationManager::class,
            // RelationManagers\EnrollmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}

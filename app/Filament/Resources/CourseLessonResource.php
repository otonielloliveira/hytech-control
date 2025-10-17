<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseLessonResource\Pages;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CourseLessonResource extends Resource
{
    protected static ?string $model = CourseLesson::class;

    protected static ?string $navigationIcon = 'heroicon-o-play';

    protected static ?string $navigationLabel = 'Aulas do Curso';

    protected static ?string $modelLabel = 'Aula do Curso';

    protected static ?string $pluralModelLabel = 'Aulas do Curso';

    protected static ?string $navigationGroup = 'Gestão de Cursos (Avançado)';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('course_module_id')
                    ->label('Módulo')
                    ->relationship('module', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Forms\Components\TextInput::make('title')
                    ->label('Título da Aula')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(CourseLesson::class, 'slug', ignoreRecord: true),
                
                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->rows(3)
                    ->columnSpanFull(),
                
                Forms\Components\Textarea::make('content')
                    ->label('Conteúdo')
                    ->rows(5)
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('video_url')
                    ->label('URL do Vídeo')
                    ->url()
                    ->maxLength(500)
                    ->placeholder('https://youtube.com/watch?v=...'),
                
                Forms\Components\TextInput::make('video_duration')
                    ->label('Duração do Vídeo (minutos)')
                    ->numeric()
                    ->suffix('min'),
                
                Forms\Components\FileUpload::make('image')
                    ->label('Imagem da Aula')
                    ->image()
                    ->disk('public')
                    ->directory('course-lessons')
                    ->visibility('public'),
                
                Forms\Components\TextInput::make('sort_order')
                    ->label('Ordem')
                    ->numeric()
                    ->required()
                    ->default(1),
                
                Forms\Components\Toggle::make('is_published')
                    ->label('Publicado')
                    ->default(true),
                
                Forms\Components\Toggle::make('is_free')
                    ->label('Aula Gratuita')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('module.course.title')
                    ->label('Curso')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('module.title')
                    ->label('Módulo')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título da Aula')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->square(),
                
                Tables\Columns\TextColumn::make('video_duration')
                    ->label('Duração')
                    ->suffix(' min')
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
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('module')
                    ->relationship('module', 'title')
                    ->label('Módulo'),
                
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publicado'),
                
                Tables\Filters\TernaryFilter::make('is_free')
                    ->label('Gratuita'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
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
            'index' => Pages\ListCourseLessons::route('/'),
            'create' => Pages\CreateCourseLesson::route('/create'),
            'edit' => Pages\EditCourseLesson::route('/{record}/edit'),
        ];
    }
    
    public static function canAccess(): bool
    {
        return Auth::user()->canManageCourses() || Auth::user()->is_admin;
    }
}
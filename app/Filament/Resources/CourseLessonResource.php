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

class CourseLessonResource extends Resource
{
    protected static ?string $model = CourseLesson::class;

    protected static ?string $navigationIcon = 'heroicon-o-play';

    protected static ?string $navigationLabel = 'Aulas do Curso';

    protected static ?string $modelLabel = 'Aula do Curso';

    protected static ?string $pluralModelLabel = 'Aulas do Curso';

    protected static ?string $navigationGroup = 'Gestão de Cursos';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('course_id')
                    ->label('Curso')
                    ->options(Course::all()->pluck('title', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('course_module_id', null)),
                
                Forms\Components\Select::make('course_module_id')
                    ->label('Módulo')
                    ->options(function (callable $get) {
                        $courseId = $get('course_id');
                        if (!$courseId) return [];
                        
                        return CourseModule::where('course_id', $courseId)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive(),
                
                Forms\Components\TextInput::make('name')
                    ->label('Nome da Aula')
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
                
                Forms\Components\TextInput::make('order')
                    ->label('Ordem')
                    ->numeric()
                    ->required()
                    ->default(1),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo')
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
                Tables\Columns\TextColumn::make('course.title')
                    ->label('Curso')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('courseModule.name')
                    ->label('Módulo')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome da Aula')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->square(),
                
                Tables\Columns\TextColumn::make('video_duration')
                    ->label('Duração')
                    ->suffix(' min')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('order')
                    ->label('Ordem')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
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
                Tables\Filters\SelectFilter::make('course')
                    ->relationship('course', 'title')
                    ->label('Curso'),
                
                Tables\Filters\SelectFilter::make('courseModule')
                    ->relationship('courseModule', 'name')
                    ->label('Módulo'),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo'),
                
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
            ->defaultSort('order');
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
}
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Support\Enums\FontWeight;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Posts';
    
    protected static ?string $modelLabel = 'Post';
    
    protected static ?string $pluralModelLabel = 'Posts';
    
    protected static ?string $navigationGroup = 'Blog';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Post')
                    ->tabs([
                        Tabs\Tab::make('Conteúdo')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Informações Básicas')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (string $context, $state, callable $set) => $context === 'create' ? $set('slug', Str::slug($state)) : null)
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->unique(Post::class, 'slug', ignoreRecord: true)
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\Select::make('category_id')
                                            ->label('Categoria')
                                            ->relationship('category', 'name')
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nome')
                                                    ->required(),
                                                Forms\Components\TextInput::make('slug')
                                                    ->label('Slug')
                                                    ->required(),
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Descrição'),
                                                Forms\Components\ColorPicker::make('color')
                                                    ->label('Cor')
                                                    ->default('#3B82F6'),
                                            ])
                                            ->searchable()
                                            ->preload(),
                                        
                                        Forms\Components\Select::make('user_id')
                                            ->label('Autor')
                                            ->relationship('user', 'name')
                                            ->default(auth()->id())
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                    ])->columns(2),
                                
                                Section::make('Conteúdo do Post')
                                    ->schema([
                                        Forms\Components\Textarea::make('excerpt')
                                            ->label('Resumo')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->hint('Se deixar vazio, será gerado automaticamente'),
                                        
                                        Forms\Components\RichEditor::make('content')
                                            ->label('Conteúdo')
                                            ->required()
                                            ->columnSpanFull()
                                            ->toolbarButtons([
                                                'attachFiles',
                                                'blockquote',
                                                'bold',
                                                'bulletList',
                                                'codeBlock',
                                                'h2',
                                                'h3',
                                                'italic',
                                                'link',
                                                'orderedList',
                                                'redo',
                                                'strike',
                                                'underline',
                                                'undo',
                                            ]),
                                    ]),
                                
                                Section::make('Imagem e Tags')
                                    ->schema([
                                        Forms\Components\FileUpload::make('featured_image')
                                            ->label('Imagem Destacada')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('blog/images')
                                            ->visibility('public'),
                                        
                                        Forms\Components\TagsInput::make('tags')
                                            ->label('Tags')
                                            ->placeholder('Digite as tags...')
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),
                        
                        Tabs\Tab::make('Configurações')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('Status e Publicação')
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->label('Status')
                                            ->required()
                                            ->options([
                                                'draft' => 'Rascunho',
                                                'published' => 'Publicado',
                                                'scheduled' => 'Agendado',
                                                'archived' => 'Arquivado',
                                            ])
                                            ->default('draft')
                                            ->live(),
                                        
                                        Forms\Components\DateTimePicker::make('published_at')
                                            ->label('Data de Publicação')
                                            ->visible(fn (callable $get) => in_array($get('status'), ['published', 'scheduled']))
                                            ->default(now()),
                                        
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Post em Destaque')
                                            ->default(false),
                                        
                                        Forms\Components\TextInput::make('reading_time')
                                            ->label('Tempo de Leitura (minutos)')
                                            ->numeric()
                                            ->default(1)
                                            ->hint('Será calculado automaticamente se deixar vazio'),
                                    ])->columns(2),
                            ]),
                        
                        Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Section::make('Meta Tags')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title')
                                            ->label('Título SEO')
                                            ->hint('Deixe vazio para usar o título do post')
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\Textarea::make('meta_description')
                                            ->label('Descrição SEO')
                                            ->rows(3)
                                            ->hint('Deixe vazio para usar o resumo do post')
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\TagsInput::make('meta_keywords')
                                            ->label('Palavras-chave SEO')
                                            ->placeholder('Digite as palavras-chave...')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Imagem')
                    ->size(60)
                    ->defaultImageUrl(asset('images/placeholder-image.png')),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->color(fn ($record) => $record->category?->color ?? 'gray')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Autor')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'scheduled' => 'warning',
                        'archived' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Rascunho',
                        'published' => 'Publicado',
                        'scheduled' => 'Agendado',
                        'archived' => 'Arquivado',
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destaque')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Visualizações')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('—'),
                
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
                        'scheduled' => 'Agendado',
                        'archived' => 'Arquivado',
                    ]),
                
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->searchable(),
                
                Tables\Filters\Filter::make('is_featured')
                    ->label('Posts em Destaque')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),
                
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

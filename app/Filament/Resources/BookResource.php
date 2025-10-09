<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;
    
    protected static ?string $modelLabel = 'Livro';
    protected static ?string $pluralModelLabel = 'Livros';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Blog Sidebar';
    protected static ?int $navigationSort = 6;

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
                        
                        Forms\Components\TextInput::make('author')
                            ->label('Autor')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->maxLength(1000),
                        
                        FileUpload::make('cover_image')
                            ->label('Capa do Livro')
                            ->image()
                            ->directory('books/covers')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageCropAspectRatio('3:4'),
                    ])->columns(2),

                Forms\Components\Section::make('Detalhes do Livro')
                    ->schema([
                        Forms\Components\TextInput::make('isbn')
                            ->label('ISBN')
                            ->maxLength(20),
                        
                        Forms\Components\TextInput::make('publication_year')
                            ->label('Ano de Publicação')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y')),
                        
                        Forms\Components\Select::make('category')
                            ->label('Categoria')
                            ->options([
                                'programacao' => 'Programação',
                                'tecnologia' => 'Tecnologia',
                                'web-development' => 'Desenvolvimento Web',
                                'mobile' => 'Desenvolvimento Mobile',
                                'devops' => 'DevOps',
                                'data-science' => 'Data Science',
                                'ia' => 'Inteligência Artificial',
                                'design' => 'Design',
                                'business' => 'Business',
                                'outros' => 'Outros',
                            ])
                            ->searchable(),
                        
                        Forms\Components\TextInput::make('rating')
                            ->label('Avaliação (0-5)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.1),
                    ])->columns(2),

                Forms\Components\Section::make('Links e Recursos')
                    ->schema([
                        Forms\Components\TextInput::make('amazon_link')
                            ->label('Link Amazon')
                            ->url()
                            ->prefix('https://'),
                        
                        Forms\Components\TextInput::make('goodreads_link')
                            ->label('Link Goodreads')
                            ->url()
                            ->prefix('https://'),
                        
                        Forms\Components\TextInput::make('pdf_link')
                            ->label('Link PDF/Download')
                            ->url()
                            ->prefix('https://'),
                    ])->columns(1),

                Forms\Components\Section::make('Resenha')
                    ->schema([
                        Forms\Components\RichEditor::make('review')
                            ->label('Resenha/Comentários')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                            ]),
                    ]),

                Forms\Components\Section::make('Configurações')
                    ->schema([
                        Forms\Components\TextInput::make('priority')
                            ->label('Prioridade')
                            ->numeric()
                            ->default(0)
                            ->helperText('Menor número = maior prioridade'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Livro Destacado')
                            ->helperText('Aparecerá na seção de livros em destaque'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true)
                            ->helperText('Apenas livros ativos aparecerão no site'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Capa')
                    ->size(60)
                    ->defaultImageUrl(asset('images/default-book-cover.png')),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('author')
                    ->label('Autor')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'programacao' => 'primary',
                        'tecnologia' => 'info',
                        'web-development' => 'success',
                        'mobile' => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('rating')
                    ->label('Avaliação')
                    ->formatStateUsing(fn ($state) => $state ? $state . '/5' : 'N/A')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->sortable(),
                
                ToggleColumn::make('is_featured')
                    ->label('Destacado'),
                
                ToggleColumn::make('is_active')
                    ->label('Ativo'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoria')
                    ->options([
                        'programacao' => 'Programação',
                        'tecnologia' => 'Tecnologia',
                        'web-development' => 'Desenvolvimento Web',
                        'mobile' => 'Desenvolvimento Mobile',
                        'devops' => 'DevOps',
                        'data-science' => 'Data Science',
                        'ia' => 'Inteligência Artificial',
                        'design' => 'Design',
                        'business' => 'Business',
                        'outros' => 'Outros',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacado'),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo'),
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
            ->defaultSort('priority', 'asc');
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}

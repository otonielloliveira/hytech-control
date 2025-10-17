<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
                    ->defaultImageUrl(asset('images/default-no-image.png')),
                
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('title')
                        ->weight('semibold')
                        ->searchable()
                        ->sortable()
                        ->limit(35)
                        ->tooltip(function (Model $record): ?string {
                            return strlen($record->title) > 35 ? $record->title : null;
                        }),
                    
                    Tables\Columns\TextColumn::make('author')
                        ->color('gray')
                        ->size('sm')
                        ->limit(30)
                        ->tooltip(function (Model $record): ?string {
                            return strlen($record->author) > 30 ? $record->author : null;
                        }),
                ])->space(1),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->size('sm')
                    ->color(fn (string $state): string => match ($state) {
                        'programacao' => 'primary',
                        'tecnologia' => 'info',
                        'web-development' => 'success',
                        'mobile' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'programacao' => 'Programação',
                        'tecnologia' => 'Tecnologia',
                        'web-development' => 'Web Dev',
                        'mobile' => 'Mobile',
                        default => ucfirst($state),
                    })
                    ->width(120),
                
                Tables\Columns\TextColumn::make('rating')
                    ->label('Avaliação')
                    ->formatStateUsing(fn ($state) => $state ? '⭐ ' . $state . '/5' : 'N/A')
                    ->sortable()
                    ->color('warning')
                    ->size('sm')
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prior.')
                    ->badge()
                    ->color('info')
                    ->size('sm')
                    ->sortable()
                    ->alignCenter(),
                
                ToggleColumn::make('is_featured')
                    ->label('Destacado')
                    ->onIcon('heroicon-s-star')
                    ->offIcon('heroicon-o-star')
                    ->onColor('warning')
                    ->offColor('gray')
                    ->alignCenter(),
                
                ToggleColumn::make('is_active')
                    ->label('Ativo')
                    ->onIcon('heroicon-s-check-circle')
                    ->offIcon('heroicon-s-x-circle')
                    ->onColor('success')
                    ->offColor('danger')
                    ->alignCenter(),
                
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
            ->recordAction('edit')
            ->recordUrl(fn (Model $record): string => static::getUrl('edit', ['record' => $record]))
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                Tables\Actions\EditAction::make()->label('Editar')->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()->label('Excluir')->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('priority', 'asc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Livro')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('title')
                                        ->label('Título')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold')
                                        ->columnSpanFull(),
                                    
                                    Infolists\Components\TextEntry::make('author')
                                        ->label('Autor')
                                        ->icon('heroicon-o-user'),
                                    
                                    Infolists\Components\TextEntry::make('isbn')
                                        ->label('ISBN')
                                        ->placeholder('Não informado')
                                        ->copyable()
                                        ->icon('heroicon-o-identification'),
                                    
                                    Infolists\Components\TextEntry::make('publisher')
                                        ->label('Editora')
                                        ->placeholder('Não informada')
                                        ->icon('heroicon-o-building-library'),
                                    
                                    Infolists\Components\TextEntry::make('year')
                                        ->label('Ano de Publicação')
                                        ->numeric()
                                        ->placeholder('Não informado')
                                        ->icon('heroicon-o-calendar'),
                                    
                                    Infolists\Components\TextEntry::make('pages')
                                        ->label('Páginas')
                                        ->numeric()
                                        ->suffix(' páginas')
                                        ->placeholder('Não informado'),
                                    
                                    Infolists\Components\TextEntry::make('language')
                                        ->label('Idioma')
                                        ->placeholder('Não informado')
                                        ->badge()
                                        ->color('primary'),
                                    
                                    Infolists\Components\TextEntry::make('genre')
                                        ->label('Gênero')
                                        ->placeholder('Não informado')
                                        ->badge()
                                        ->color('success'),
                                    
                                    Infolists\Components\TextEntry::make('priority')
                                        ->label('Prioridade')
                                        ->numeric()
                                        ->badge()
                                        ->color(fn ($state) => match (true) {
                                            $state >= 80 => 'success',
                                            $state >= 50 => 'warning',
                                            default => 'danger',
                                        }),
                                    
                                    Infolists\Components\IconEntry::make('is_active')
                                        ->label('Ativo')
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-circle')
                                        ->falseIcon('heroicon-o-x-mark')
                                        ->trueColor('success')
                                        ->falseColor('danger'),
                                ]),
                            
                            Infolists\Components\ImageEntry::make('cover_image')
                                ->label('Capa')
                                ->size(200)
                                ->grow(false),
                        ])->from('lg'),
                    ]),
                
                Infolists\Components\Section::make('Links e Recursos')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('download_url')
                                    ->label('URL de Download')
                                    ->placeholder('Não disponível')
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-o-arrow-down-tray'),
                                
                                Infolists\Components\TextEntry::make('external_url')
                                    ->label('Link Externo')
                                    ->placeholder('Não disponível')
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-o-link'),
                            ]),
                    ])
                    ->collapsible(),
                
                Infolists\Components\Section::make('Descrição')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label('')
                            ->html()
                            ->placeholder('Nenhuma descrição fornecida'),
                    ])
                    ->collapsible(),
                
                Infolists\Components\Section::make('Informações do Sistema')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Criado em')
                                    ->dateTime('d/m/Y H:i'),
                                
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Atualizado em')
                                    ->dateTime('d/m/Y H:i'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
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
            'view' => Pages\ViewBook::route('/{record}'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}

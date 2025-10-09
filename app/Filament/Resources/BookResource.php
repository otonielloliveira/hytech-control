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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning'),
                    Tables\Actions\Action::make('duplicate')
                        ->label('Duplicar')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->action(function ($record) {
                            $newBook = $record->replicate();
                            $newBook->title = $record->title . ' (Cópia)';
                            $newBook->is_active = false;
                            $newBook->is_featured = false;
                            $newBook->save();
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Livro duplicado com sucesso!')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('toggle_featured')
                        ->label(fn ($record) => $record->is_featured ? 'Remover Destaque' : 'Destacar')
                        ->icon(fn ($record) => $record->is_featured ? 'heroicon-o-star' : 'heroicon-s-star')
                        ->color(fn ($record) => $record->is_featured ? 'gray' : 'warning')
                        ->action(function ($record) {
                            $record->is_featured = !$record->is_featured;
                            $record->save();
                            
                            \Filament\Notifications\Notification::make()
                                ->title($record->is_featured ? 'Livro destacado!' : 'Destaque removido!')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                ])
                ->tooltip('Ações')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button()
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

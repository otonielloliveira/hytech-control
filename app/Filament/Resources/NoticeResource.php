<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoticeResource\Pages;
use App\Filament\Resources\NoticeResource\RelationManagers;
use App\Models\Notice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class NoticeResource extends Resource
{
    protected static ?string $model = Notice::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    
    protected static ?string $navigationLabel = 'Recados';
    
    protected static ?string $modelLabel = 'Recado';
    
    protected static ?string $pluralModelLabel = 'Recados';
    
    protected static ?string $navigationGroup = 'Blog Sidebar';

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
                        
                        Forms\Components\RichEditor::make('content')
                            ->label('Conteúdo')
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagem')
                            ->image()
                            ->directory('notices')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Configurações de Link')
                    ->schema([
                        Forms\Components\Select::make('link_type')
                            ->label('Tipo de Link')
                            ->options([
                                'none' => 'Sem Link',
                                'internal' => 'Link Interno',
                                'external' => 'Link Externo',
                            ])
                            ->default('none')
                            ->reactive()
                            ->required(),
                        
                        Forms\Components\TextInput::make('link_url')
                            ->label('URL do Link')
                            ->url()
                            ->visible(fn (Forms\Get $get): bool => $get('link_type') === 'external'),
                        
                        Forms\Components\TextInput::make('link_url')
                            ->label('Slug/Rota Interna')
                            ->helperText('Ex: slug-do-post ou ID do registro')
                            ->visible(fn (Forms\Get $get): bool => $get('link_type') === 'internal'),
                        
                        Forms\Components\Select::make('internal_route')
                            ->label('Rota Interna')
                            ->options([
                                'blog.post.show' => 'Post do Blog',
                                'blog.category.show' => 'Categoria do Blog',
                                'blog.tag.show' => 'Tag do Blog',
                            ])
                            ->visible(fn (Forms\Get $get): bool => $get('link_type') === 'internal'),
                    ]),
                
                Forms\Components\Section::make('Configurações de Exibição')
                    ->schema([
                        Forms\Components\TextInput::make('priority')
                            ->label('Prioridade')
                            ->helperText('Maior número = maior prioridade')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true)
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Data de Início')
                            ->helperText('Deixe vazio para exibir imediatamente'),
                        
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Data de Fim')
                            ->helperText('Deixe vazio para exibir indefinidamente'),
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
                    ->size(50)
                    ->defaultImageUrl(asset('images/default-no-image.png')),
                
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('title')
                        ->weight('semibold')
                        ->searchable()
                        ->sortable()
                        ->limit(40)
                        ->tooltip(function (Model $record): ?string {
                            return strlen($record->title) > 40 ? $record->title : null;
                        }),
                    
                    Tables\Columns\TextColumn::make('description')
                        ->color('gray')
                        ->size('sm')
                        ->limit(60)
                        ->tooltip(function (Model $record): ?string {
                            return $record->description && strlen($record->description) > 60 ? $record->description : null;
                        }),
                ])->space(1),
                
                Tables\Columns\TextColumn::make('link_type')
                    ->label('Tipo')
                    ->badge()
                    ->size('sm')
                    ->color(fn (string $state): string => match ($state) {
                        'none' => 'gray',
                        'internal' => 'info',
                        'external' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'none' => 'Sem Link',
                        'internal' => 'Interno',
                        'external' => 'Externo',
                        default => $state,
                    })
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('warning')
                    ->size('sm'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-s-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('link_type')
                    ->label('Tipo de Link')
                    ->options([
                        'none' => 'Sem Link',
                        'internal' => 'Link Interno',
                        'external' => 'Link Externo',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status'),
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
            ->defaultSort('priority', 'desc');
    }

    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Recado')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Título')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->columnSpanFull(),
                                
                                Infolists\Components\TextEntry::make('type')
                                    ->label('Tipo')
                                    ->badge()
                                    ->color(fn ($state) => match($state) {
                                        'info' => 'info',
                                        'warning' => 'warning',
                                        'danger' => 'danger',
                                        'success' => 'success',
                                        default => 'gray'
                                    }),
                                
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Ativo')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-mark')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                                
                                Infolists\Components\TextEntry::make('priority')
                                    ->label('Prioridade')
                                    ->numeric()
                                    ->badge()
                                    ->color(fn ($state) => match (true) {
                                        $state >= 80 => 'success',
                                        $state >= 50 => 'warning',
                                        default => 'danger',
                                    }),
                            ]),
                    ]),
                
                Infolists\Components\Section::make('Conteúdo')
                    ->schema([
                        Infolists\Components\TextEntry::make('content')
                            ->label('')
                            ->html()
                            ->placeholder('Nenhum conteúdo fornecido'),
                    ])
                    ->collapsible(),
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
            'index' => Pages\ListNotices::route('/'),
            'create' => Pages\CreateNotice::route('/create'),
            'view' => Pages\ViewNotice::route('/{record}'),
            'edit' => Pages\EditNotice::route('/{record}/edit'),
        ];
    }
    
    public static function canAccess(): bool
    {
        return Auth::user()->canManageSettings() || Auth::user()->is_admin;
    }
}

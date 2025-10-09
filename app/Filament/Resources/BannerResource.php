<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Filament\Resources\BannerResource\RelationManagers;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationLabel = 'Banners';
    
    protected static ?string $modelLabel = 'Banner';
    
    protected static ?string $pluralModelLabel = 'Banners';
    
    protected static ?string $navigationGroup = 'Blog';
    
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Conteúdo do Banner')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('subtitle')
                            ->label('Subtítulo')
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagem do Banner')
                            ->image()
                            ->imageEditor()
                            ->directory('blog/banners')
                            ->visibility('public')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Link de Destino')
                    ->schema([
                        Forms\Components\Select::make('link_type')
                            ->label('Tipo de Link')
                            ->options([
                                'url' => 'URL Externa',
                                'post' => 'Post do Blog',
                            ])
                            ->default('url')
                            ->live()
                            ->required(),
                        
                        Forms\Components\TextInput::make('link_url')
                            ->label('URL')
                            ->url()
                            ->visible(fn (callable $get) => $get('link_type') === 'url')
                            ->required(fn (callable $get) => $get('link_type') === 'url'),
                        
                        Forms\Components\Select::make('post_id')
                            ->label('Post')
                            ->relationship('post', 'title')
                            ->searchable()
                            ->preload()
                            ->visible(fn (callable $get) => $get('link_type') === 'post')
                            ->required(fn (callable $get) => $get('link_type') === 'post'),
                        
                        Forms\Components\TextInput::make('button_text')
                            ->label('Texto do Botão')
                            ->default('Saiba Mais'),
                        
                        Forms\Components\Toggle::make('target_blank')
                            ->label('Abrir em Nova Aba')
                            ->default(false),
                    ])->columns(2),
                
                Forms\Components\Section::make('Configurações')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem de Exibição')
                            ->numeric()
                            ->default(0)
                            ->helperText('Menor número aparece primeiro'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->size(80),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('Subtítulo')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('link_type')
                    ->label('Tipo de Link')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'url' => 'success',
                        'post' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'url' => 'URL Externa',
                        'post' => 'Post do Blog',
                    }),
                
                Tables\Columns\TextColumn::make('post.title')
                    ->label('Post Vinculado')
                    ->limit(30)
                    ->placeholder('—'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('Apenas Ativos')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                
                Tables\Filters\SelectFilter::make('link_type')
                    ->label('Tipo de Link')
                    ->options([
                        'url' => 'URL Externa',
                        'post' => 'Post do Blog',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
    }

    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Banner')
                    ->schema([
                        Infolists\Components\Split::make([
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
                                        ->color('primary'),
                                    
                                    Infolists\Components\IconEntry::make('is_active')
                                        ->label('Ativo')
                                        ->boolean()
                                        ->trueIcon('heroicon-o-check-circle')
                                        ->falseIcon('heroicon-o-x-circle')
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
                            
                            Infolists\Components\ImageEntry::make('image')
                                ->label('Imagem')
                                ->size(200)
                                ->grow(false),
                        ])->from('lg'),
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'view' => Pages\ViewBanner::route('/{record}'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}

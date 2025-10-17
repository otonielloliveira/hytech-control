<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Enums\FontWeight;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationLabel = 'Produtos';
    
    protected static ?string $modelLabel = 'Produto';
    
    protected static ?string $pluralModelLabel = 'Produtos';
    
    protected static ?string $navigationGroup = 'Loja';
    
    public static function canAccess(): bool
    {
        return Auth::user()?->canManageStore() || Auth::user()?->is_admin;
    }
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informações Básicas')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome do Produto')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $context, $state, Forms\Set $set) {
                                        if ($context === 'create') {
                                            $set('sku', 'PRD-' . strtoupper(Str::random(8)));
                                        }
                                    }),
                                    
                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->unique(Product::class, 'sku', ignoreRecord: true)
                                    ->maxLength(255),
                                    
                                Forms\Components\Textarea::make('short_description')
                                    ->label('Descrição Curta')
                                    ->rows(3)
                                    ->maxLength(500),
                                    
                                Forms\Components\RichEditor::make('description')
                                    ->label('Descrição Completa')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                            
                        Forms\Components\Section::make('Preços e Estoque')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Preço')
                                    ->required()
                                    ->numeric()
                                    ->prefix('R$')
                                    ->minValue(0),
                                    
                                Forms\Components\TextInput::make('sale_price')
                                    ->label('Preço Promocional')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->minValue(0)
                                    ->hint('Deixe vazio se não houver promoção'),
                                    
                                Forms\Components\Toggle::make('manage_stock')
                                    ->label('Gerenciar Estoque')
                                    ->default(true)
                                    ->live(),
                                    
                                Forms\Components\TextInput::make('stock_quantity')
                                    ->label('Quantidade em Estoque')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->visible(fn (Forms\Get $get) => $get('manage_stock')),
                                    
                                Forms\Components\Toggle::make('in_stock')
                                    ->label('Em Estoque')
                                    ->default(true),
                            ])
                            ->columns(2),
                            
                        Forms\Components\Section::make('Dimensões e Peso')
                            ->schema([
                                Forms\Components\TextInput::make('weight')
                                    ->label('Peso (kg)')
                                    ->numeric()
                                    ->step(0.001)
                                    ->suffix('kg'),
                                    
                                Forms\Components\TextInput::make('length')
                                    ->label('Comprimento (cm)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('cm'),
                                    
                                Forms\Components\TextInput::make('width')
                                    ->label('Largura (cm)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('cm'),
                                    
                                Forms\Components\TextInput::make('height')
                                    ->label('Altura (cm)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('cm'),
                            ])
                            ->columns(4),
                    ])
                    ->columnSpan(['lg' => 2]),
                    
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status e Visibilidade')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'draft' => 'Rascunho',
                                        'active' => 'Ativo',
                                        'inactive' => 'Inativo',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                    
                                Forms\Components\Toggle::make('featured')
                                    ->label('Produto em Destaque')
                                    ->default(false),
                                    
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Ordem de Exibição')
                                    ->numeric()
                                    ->default(0),
                            ]),
                            
                        Forms\Components\Section::make('Imagens')
                            ->schema([
                                FileUpload::make('images')
                                    ->label('Imagens do Produto')
                                    ->multiple()
                                    ->image()
                                    ->maxFiles(10)
                                    ->reorderable()
                                    ->directory('products')
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->hint('A primeira imagem será a principal'),
                            ]),
                            
                        Forms\Components\Section::make('Galeria Adicional')
                            ->schema([
                                FileUpload::make('gallery')
                                    ->label('Galeria de Imagens')
                                    ->multiple()
                                    ->image()
                                    ->maxFiles(20)
                                    ->reorderable()
                                    ->directory('products/gallery')
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->hint('Imagens adicionais para a galeria'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->label('Imagem')
                    ->getStateUsing(fn (Product $record) => $record->getMainImage())
                    ->circular()
                    ->size(50),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                    
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->money('BRL')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Promoção')
                    ->money('BRL')
                    ->placeholder('Sem promoção'),
                    
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Estoque')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 10 => 'success',
                        $state > 0 => 'warning',
                        default => 'danger',
                    }),
                    
                IconColumn::make('featured')
                    ->label('Destaque')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'draft' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Ativo',
                        'inactive' => 'Inativo',
                        'draft' => 'Rascunho',
                    }),
                    
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
                        'active' => 'Ativo',
                        'inactive' => 'Inativo',
                        'draft' => 'Rascunho',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('featured')
                    ->label('Em Destaque'),
                    
                Tables\Filters\TernaryFilter::make('in_stock')
                    ->label('Em Estoque'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Ativar Selecionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'active']))
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desativar Selecionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'inactive']))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingRuleResource\Pages;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\ShippingRuleResource\RelationManagers;
use App\Models\ShippingRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\IconColumn;

class ShippingRuleResource extends Resource
{
    protected static ?string $model = ShippingRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationLabel = 'Regras de Frete';
    
    protected static ?string $modelLabel = 'Regra de Frete';
    
    protected static ?string $pluralModelLabel = 'Regras de Frete';
    
    protected static ?string $navigationGroup = 'Loja';
    
    public static function canAccess(): bool
    {
        return Auth::user()?->canManageStore() || Auth::user()?->is_admin;
    }
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome da Regra')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->maxLength(1000),
                            
                        Forms\Components\Select::make('type')
                            ->label('Tipo de Regra')
                            ->options(ShippingRule::getAvailableTypes())
                            ->required()
                            ->live(),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                            
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem de Prioridade')
                            ->numeric()
                            ->default(0)
                            ->hint('Regras com menor número têm prioridade'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Configurações de Custo')
                    ->schema([
                        Forms\Components\TextInput::make('base_cost')
                            ->label('Custo Base')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('R$')
                            ->default(0)
                            ->required(),
                            
                        Forms\Components\TextInput::make('cost_per_kg')
                            ->label('Custo por Kg')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('R$')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'weight_based'),
                            
                        Forms\Components\TextInput::make('min_weight')
                            ->label('Peso Mínimo (kg)')
                            ->numeric()
                            ->step(0.001)
                            ->suffix('kg')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'weight_based'),
                            
                        Forms\Components\TextInput::make('max_weight')
                            ->label('Peso Máximo (kg)')
                            ->numeric()
                            ->step(0.001)
                            ->suffix('kg')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'weight_based'),
                            
                        Forms\Components\TextInput::make('min_order_value')
                            ->label('Valor Mínimo do Pedido')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('R$')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'price_based'),
                            
                        Forms\Components\TextInput::make('max_order_value')
                            ->label('Valor Máximo do Pedido')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('R$')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'price_based'),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Configurações de Entrega')
                    ->schema([
                        Forms\Components\TextInput::make('estimated_days_min')
                            ->label('Dias Mínimos para Entrega')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->suffix('dias'),
                            
                        Forms\Components\TextInput::make('estimated_days_max')
                            ->label('Dias Máximos para Entrega')
                            ->numeric()
                            ->default(7)
                            ->required()
                            ->suffix('dias'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Configurações de Localização')
                    ->schema([
                        Forms\Components\TagsInput::make('locations')
                            ->label('Localizações')
                            ->hint('CEPs, Estados, Cidades (separados por vírgula)')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'location_based'),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('type') === 'location_based'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fixed' => 'gray',
                        'weight_based' => 'info',
                        'price_based' => 'success',
                        'location_based' => 'warning',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ShippingRule::getAvailableTypes()[$state] ?? $state),
                    
                Tables\Columns\TextColumn::make('base_cost')
                    ->label('Custo Base')
                    ->money('BRL')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('cost_per_kg')
                    ->label('Por Kg')
                    ->money('BRL')
                    ->placeholder('N/A'),
                    
                Tables\Columns\TextColumn::make('min_order_value')
                    ->label('Valor Mín.')
                    ->money('BRL')
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('estimated_days_min')
                    ->label('Entrega Min.')
                    ->suffix(' dias')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('estimated_days_max')
                    ->label('Entrega Máx.')
                    ->suffix(' dias')
                    ->sortable(),
                    
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(ShippingRule::getAvailableTypes()),
                    
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
            'index' => Pages\ListShippingRules::route('/'),
            'create' => Pages\CreateShippingRule::route('/create'),
            'edit' => Pages\EditShippingRule::route('/{record}/edit'),
        ];
    }
}

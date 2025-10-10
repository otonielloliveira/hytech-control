<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Filament\Resources\PaymentMethodResource\RelationManagers;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Tables\Columns\IconColumn;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?string $navigationLabel = 'Métodos de Pagamento';
    
    protected static ?string $modelLabel = 'Método de Pagamento';
    
    protected static ?string $pluralModelLabel = 'Métodos de Pagamento';
    
    protected static ?string $navigationGroup = 'Loja';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $context, $state, Forms\Set $set) {
                                if ($context === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                            
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(PaymentMethod::class, 'slug', ignoreRecord: true)
                            ->maxLength(255)
                            ->hint('Identificador único para o método de pagamento'),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Configurações do Gateway')
                    ->schema([
                        Forms\Components\Select::make('gateway')
                            ->label('Gateway de Pagamento')
                            ->options(PaymentMethod::getAvailableGateways())
                            ->required()
                            ->live()
                            ->searchable(),
                            
                        Forms\Components\KeyValue::make('config')
                            ->label('Configurações')
                            ->addActionLabel('Adicionar Configuração')
                            ->keyLabel('Chave')
                            ->valueLabel('Valor')
                            ->hint(function (Forms\Get $get) {
                                return match($get('gateway')) {
                                    'asaas' => 'API Key, Environment (sandbox/production)',
                                    'mercadopago' => 'Access Token, Public Key',
                                    'pagseguro' => 'Token, Email',
                                    'pix' => 'Chave PIX, Nome do Beneficiário',
                                    'boleto' => 'Dados Bancários, Instruções',
                                    'card' => 'Configurações específicas do processador',
                                    default => 'Configurações específicas do gateway'
                                };
                            }),
                    ]),
                    
                Forms\Components\Section::make('Taxas e Configurações')
                    ->schema([
                        Forms\Components\TextInput::make('fee_percentage')
                            ->label('Taxa Percentual (%)')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->default(0),
                            
                        Forms\Components\TextInput::make('fee_fixed')
                            ->label('Taxa Fixa')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->prefix('R$')
                            ->default(0),
                            
                        Forms\Components\TagsInput::make('supported_currencies')
                            ->label('Moedas Suportadas')
                            ->default(['BRL'])
                            ->hint('Ex: BRL, USD, EUR'),
                            
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem de Exibição')
                            ->numeric()
                            ->default(0),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                    ])
                    ->columns(2),
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
                    
                Tables\Columns\TextColumn::make('gateway')
                    ->label('Gateway')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'asaas' => 'success',
                        'mercadopago' => 'info',
                        'pagseguro' => 'warning',
                        'pix' => 'primary',
                        'boleto' => 'gray',
                        'card' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => PaymentMethod::getAvailableGateways()[$state] ?? $state),
                    
                Tables\Columns\TextColumn::make('fee_percentage')
                    ->label('Taxa %')
                    ->suffix('%')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('fee_fixed')
                    ->label('Taxa Fixa')
                    ->money('BRL')
                    ->sortable(),
                    
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
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
                Tables\Filters\SelectFilter::make('gateway')
                    ->label('Gateway')
                    ->options(PaymentMethod::getAvailableGateways()),
                    
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
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}

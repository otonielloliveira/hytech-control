<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\IconColumn;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = 'Pedidos';
    
    protected static ?string $modelLabel = 'Pedido';
    
    protected static ?string $pluralModelLabel = 'Pedidos';
    
    protected static ?string $navigationGroup = 'Loja';
    
    protected static ?int $navigationSort = 2;
    
    public static function canAccess(): bool
    {
        return Auth::user()?->canManageStore() || Auth::user()?->is_admin;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Pedido')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Número do Pedido')
                            ->disabled()
                            ->dehydrated(),
                            
                        Forms\Components\Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable(),
                            
                        Forms\Components\Select::make('status')
                            ->label('Status do Pedido')
                            ->options([
                                'pending' => 'Pendente',
                                'processing' => 'Processando',
                                'shipped' => 'Enviado',
                                'delivered' => 'Entregue',
                                'cancelled' => 'Cancelado',
                                'refunded' => 'Reembolsado',
                            ])
                            ->required()
                            ->default('pending'),
                            
                        Forms\Components\Select::make('payment_status')
                            ->label('Status do Pagamento')
                            ->options([
                                'pending' => 'Pendente',
                                'processing' => 'Processando',
                                'paid' => 'Pago',
                                'failed' => 'Falhou',
                                'cancelled' => 'Cancelado',
                                'refunded' => 'Reembolsado',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Valores')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('R$')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('shipping_total')
                            ->label('Frete')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0),
                            
                        Forms\Components\TextInput::make('tax_total')
                            ->label('Impostos')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0),
                            
                        Forms\Components\TextInput::make('discount_total')
                            ->label('Desconto')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0),
                            
                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('R$')
                            ->disabled(),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Pagamento')
                    ->schema([
                        Forms\Components\Select::make('payment_method_id')
                            ->label('Método de Pagamento')
                            ->relationship('paymentMethod', 'name')
                            ->searchable(),
                            
                        Forms\Components\TextInput::make('payment_transaction_id')
                            ->label('ID da Transação')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Endereços')
                    ->schema([
                        Forms\Components\KeyValue::make('billing_address')
                            ->label('Endereço de Cobrança')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor'),
                            
                        Forms\Components\KeyValue::make('shipping_address')
                            ->label('Endereço de Entrega')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Observações')
                    ->schema([
                        Forms\Components\Textarea::make('customer_notes')
                            ->label('Observações do Cliente')
                            ->rows(3)
                            ->disabled(),
                            
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Observações Internas')
                            ->rows(3),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Rastreamento')
                    ->schema([
                        Forms\Components\TextInput::make('tracking_code')
                            ->label('Código de Rastreamento')
                            ->maxLength(255)
                            ->placeholder('Ex: BR123456789BR'),
                            
                        Forms\Components\TextInput::make('tracking_url')
                            ->label('URL de Rastreamento')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://rastreamento.correios.com.br/...'),
                            
                        Forms\Components\DateTimePicker::make('shipped_at')
                            ->label('Data de Envio')
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),
                            
                        Forms\Components\DateTimePicker::make('delivered_at')
                            ->label('Data de Entrega')
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Pedido')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('BRL')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        'refunded' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendente',
                        'processing' => 'Processando',
                        'shipped' => 'Enviado',
                        'delivered' => 'Entregue',
                        'cancelled' => 'Cancelado',
                        'refunded' => 'Reembolsado',
                    }),
                    
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pagamento')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        'refunded' => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendente',
                        'processing' => 'Processando',
                        'paid' => 'Pago',
                        'failed' => 'Falhou',
                        'cancelled' => 'Cancelado',
                        'refunded' => 'Reembolsado',
                    }),
                    
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Método')
                    ->placeholder('Não definido'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('shipped_at')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Não enviado')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('delivered_at')
                    ->label('Entregue em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Não entregue')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('tracking_code')
                    ->label('Código de Rastreamento')
                    ->placeholder('Sem código')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable()
                    ->copyMessage('Código copiado!')
                    ->copyMessageDuration(1500),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'processing' => 'Processando',
                        'shipped' => 'Enviado',
                        'delivered' => 'Entregue',
                        'cancelled' => 'Cancelado',
                        'refunded' => 'Reembolsado',
                    ]),
                    
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Status do Pagamento')
                    ->options([
                        'pending' => 'Pendente',
                        'processing' => 'Processando',
                        'paid' => 'Pago',
                        'failed' => 'Falhou',
                        'cancelled' => 'Cancelado',
                        'refunded' => 'Reembolsado',
                    ]),
                    
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('De'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('add_tracking')
                    ->label('Adicionar Rastreamento')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->visible(fn (Order $record): bool => in_array($record->status, ['processing', 'shipped']) && empty($record->tracking_code))
                    ->form([
                        Forms\Components\TextInput::make('tracking_code')
                            ->label('Código de Rastreamento')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: BR123456789BR'),
                            
                        Forms\Components\TextInput::make('tracking_url')
                            ->label('URL de Rastreamento')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://rastreamento.correios.com.br/...'),
                    ])
                    ->action(function (Order $record, array $data) {
                        $record->update([
                            'tracking_code' => $data['tracking_code'],
                            'tracking_url' => $data['tracking_url'] ?? null,
                        ]);
                    }),
                
                Tables\Actions\Action::make('mark_as_shipped')
                    ->label('Marcar como Enviado')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->visible(fn (Order $record): bool => $record->status === 'processing')
                    ->form([
                        Forms\Components\TextInput::make('tracking_code')
                            ->label('Código de Rastreamento (Opcional)')
                            ->maxLength(255)
                            ->placeholder('Ex: BR123456789BR'),
                            
                        Forms\Components\TextInput::make('tracking_url')
                            ->label('URL de Rastreamento (Opcional)')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://rastreamento.correios.com.br/...'),
                    ])
                    ->action(function (Order $record, array $data) {
                        $record->markAsShipped(
                            $data['tracking_code'] ?? null,
                            $data['tracking_url'] ?? null
                        );
                    }),
                    
                Tables\Actions\Action::make('mark_as_delivered')
                    ->label('Marcar como Entregue')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record): bool => $record->status === 'shipped')
                    ->action(function (Order $record) {
                        $record->markAsDelivered();
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Pedido')
                    ->schema([
                        Infolists\Components\TextEntry::make('order_number')
                            ->label('Número do Pedido'),
                        Infolists\Components\TextEntry::make('client.name')
                            ->label('Cliente'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge(),
                        Infolists\Components\TextEntry::make('payment_status')
                            ->label('Status do Pagamento')
                            ->badge(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime(),
                    ])
                    ->columns(2),
                    
                Infolists\Components\Section::make('Itens do Pedido')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('product_name')
                                    ->label('Produto'),
                                Infolists\Components\TextEntry::make('quantity')
                                    ->label('Quantidade'),
                                Infolists\Components\TextEntry::make('product_price')
                                    ->label('Preço Unitário')
                                    ->money('BRL'),
                                Infolists\Components\TextEntry::make('total')
                                    ->label('Total')
                                    ->money('BRL'),
                            ])
                            ->columns(4),
                    ]),
                    
                Infolists\Components\Section::make('Valores')
                    ->schema([
                        Infolists\Components\TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('BRL'),
                        Infolists\Components\TextEntry::make('shipping_total')
                            ->label('Frete')
                            ->money('BRL'),
                        Infolists\Components\TextEntry::make('total')
                            ->label('Total')
                            ->money('BRL'),
                    ])
                    ->columns(3),
                    
                Infolists\Components\Section::make('Rastreamento')
                    ->schema([
                        Infolists\Components\TextEntry::make('tracking_code')
                            ->label('Código de Rastreamento')
                            ->placeholder('Não informado')
                            ->copyable()
                            ->copyMessage('Código copiado!')
                            ->copyMessageDuration(1500),
                            
                        Infolists\Components\TextEntry::make('tracking_url')
                            ->label('URL de Rastreamento')
                            ->placeholder('Não informado')
                            ->url(fn ($state) => $state)
                            ->openUrlInNewTab(),
                            
                        Infolists\Components\TextEntry::make('shipped_at')
                            ->label('Data de Envio')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Não enviado'),
                            
                        Infolists\Components\TextEntry::make('delivered_at')
                            ->label('Data de Entrega')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Não entregue'),
                    ])
                    ->columns(2)
                    ->visible(fn (Order $record): bool => $record->hasTracking() || $record->isShipped() || $record->isDelivered()),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}

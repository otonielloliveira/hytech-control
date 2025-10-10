<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Order;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_number')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('order_number')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Número do Pedido')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
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
                    
                Tables\Columns\TextColumn::make('tracking_code')
                    ->label('Rastreamento')
                    ->placeholder('Sem código')
                    ->copyable()
                    ->copyMessage('Código copiado!')
                    ->copyMessageDuration(1500),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', $record)),
                Tables\Actions\EditAction::make()
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.edit', $record)),
                    
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
}
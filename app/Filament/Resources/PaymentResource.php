<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Pagamentos';

    protected static ?string $pluralModelLabel = 'Pagamentos';

    protected static ?string $modelLabel = 'Pagamento';

    protected static ?string $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Pagamento')
                    ->schema([
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('ID da Transação')
                            ->disabled(),
                        
                        Forms\Components\Select::make('gateway')
                            ->label('Gateway')
                            ->options([
                                'mercadopago' => 'MercadoPago',
                                'efipay' => 'EFI Pay',
                                'pagseguro' => 'PagSeguro',
                            ])
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('gateway_transaction_id')
                            ->label('ID no Gateway')
                            ->disabled(),
                        
                        Forms\Components\Select::make('payment_method')
                            ->label('Método de Pagamento')
                            ->options(Payment::PAYMENT_METHODS)
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('amount')
                            ->label('Valor')
                            ->numeric()
                            ->prefix('R$')
                            ->disabled(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                Payment::STATUS_PENDING => 'Pendente',
                                Payment::STATUS_PROCESSING => 'Processando',
                                Payment::STATUS_APPROVED => 'Aprovado',
                                Payment::STATUS_REJECTED => 'Rejeitado',
                                Payment::STATUS_CANCELLED => 'Cancelado',
                                Payment::STATUS_REFUNDED => 'Estornado',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Dados do Pagador')
                    ->schema([
                        Forms\Components\TextInput::make('payer_name')
                            ->label('Nome')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('payer_email')
                            ->label('Email')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('payer_phone')
                            ->label('Telefone')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('payer_document')
                            ->label('Documento')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Informações Adicionais')
                    ->schema([
                        Forms\Components\Textarea::make('failure_reason')
                            ->label('Motivo da Falha')
                            ->rows(2)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('installments')
                            ->label('Parcelas')
                            ->numeric()
                            ->default(1),
                        
                        Forms\Components\TextInput::make('fee_amount')
                            ->label('Taxa')
                            ->numeric()
                            ->prefix('R$'),
                        
                        Forms\Components\TextInput::make('net_amount')
                            ->label('Valor Líquido')
                            ->numeric()
                            ->prefix('R$'),
                        
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Pago em'),
                        
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expira em'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\BadgeColumn::make('gateway')
                    ->label('Gateway')
                    ->formatStateUsing(fn (Payment $record) => $record->gateway_label)
                    ->colors([
                        'primary' => 'mercadopago',
                        'success' => 'efipay',
                        'warning' => 'pagseguro',
                    ]),

                Tables\Columns\BadgeColumn::make('payment_method')
                    ->label('Método')
                    ->formatStateUsing(fn (Payment $record) => $record->payment_method_label)
                    ->colors([
                        'info' => 'pix',
                        'primary' => 'credit_card',
                        'secondary' => 'bank_slip',
                    ]),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->formatStateUsing(fn (Payment $record) => $record->formatted_amount)
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (Payment $record) => $record->status_label)
                    ->colors([
                        'warning' => Payment::STATUS_PENDING,
                        'info' => Payment::STATUS_PROCESSING,
                        'success' => Payment::STATUS_APPROVED,
                        'danger' => Payment::STATUS_REJECTED,
                        'secondary' => Payment::STATUS_CANCELLED,
                        'dark' => Payment::STATUS_REFUNDED,
                    ]),

                Tables\Columns\TextColumn::make('payer_name')
                    ->label('Pagador')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payer_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('payable_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Pago em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gateway')
                    ->label('Gateway')
                    ->options([
                        'mercadopago' => 'MercadoPago',
                        'efipay' => 'EFI Pay',
                        'pagseguro' => 'PagSeguro',
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Método')
                    ->options(Payment::PAYMENT_METHODS),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Payment::STATUS_PENDING => 'Pendente',
                        Payment::STATUS_PROCESSING => 'Processando',
                        Payment::STATUS_APPROVED => 'Aprovado',
                        Payment::STATUS_REJECTED => 'Rejeitado',
                        Payment::STATUS_CANCELLED => 'Cancelado',
                        Payment::STATUS_REFUNDED => 'Estornado',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Criado de'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Criado até'),
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
                Tables\Actions\EditAction::make()
                    ->visible(fn (Payment $record) => $record->isPending()),
                Tables\Actions\Action::make('approve')
                    ->label('Aprovar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Payment $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->action(fn (Payment $record) => $record->markAsApproved())
                    ->successNotificationTitle('Pagamento aprovado'),
                Tables\Actions\Action::make('reject')
                    ->label('Rejeitar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Payment $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->action(fn (Payment $record) => $record->markAsRejected('Rejeitado manualmente'))
                    ->successNotificationTitle('Pagamento rejeitado'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::pending()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

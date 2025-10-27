<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationResource\Pages;
use App\Models\Donation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'Doações';

    protected static ?string $pluralModelLabel = 'Doações';

    protected static ?string $modelLabel = 'Doação';

    protected static ?string $navigationGroup = 'Loja';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Doador')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),

                Forms\Components\Section::make('Detalhes da Doação')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Valor')
                            ->numeric()
                            ->prefix('R$')
                            ->step(0.01)
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->label('Método de Pagamento')
                            ->options([
                                'pix' => 'PIX',
                                'credit_card' => 'Cartão de Crédito',
                                'bank_transfer' => 'Transferência Bancária',
                            ])
                            ->default('pix')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                Donation::STATUS_PENDING => 'Aguardando Pagamento',
                                Donation::STATUS_PAID => 'Pago',
                                Donation::STATUS_CANCELLED => 'Cancelado',
                                Donation::STATUS_EXPIRED => 'Expirado',
                            ])
                            ->default(Donation::STATUS_PENDING)
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Informações de Pagamento')
                    ->schema([
                        Forms\Components\Textarea::make('pix_code')
                            ->label('Código PIX')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('payment_id')
                            ->label('ID do Pagamento')
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Data do Pagamento'),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expira em'),
                    ])->columns(2),

                Forms\Components\Section::make('Informações Adicionais')
                    ->schema([
                        Forms\Components\Textarea::make('message')
                            ->label('Mensagem')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('Endereço IP')
                            ->maxLength(45),
                        Forms\Components\Textarea::make('payment_data')
                            ->label('Dados do Pagamento (JSON)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => '#' . str_pad($state, 6, '0', STR_PAD_LEFT)),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => match($state) {
                        Donation::STATUS_PENDING => 'Aguardando',
                        Donation::STATUS_PAID => 'Pago',
                        Donation::STATUS_CANCELLED => 'Cancelado',
                        Donation::STATUS_EXPIRED => 'Expirado',
                        default => 'Desconhecido'
                    })
                    ->colors([
                        'warning' => Donation::STATUS_PENDING,
                        'success' => Donation::STATUS_PAID,
                        'danger' => Donation::STATUS_CANCELLED,
                        'gray' => Donation::STATUS_EXPIRED,
                    ]),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Pagamento')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pix' => 'PIX',
                        'credit_card' => 'Cartão',
                        'bank_transfer' => 'Transferência',
                        default => $state
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Pago em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expira em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Donation::STATUS_PENDING => 'Aguardando Pagamento',
                        Donation::STATUS_PAID => 'Pago',
                        Donation::STATUS_CANCELLED => 'Cancelado',
                        Donation::STATUS_EXPIRED => 'Expirado',
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Método de Pagamento')
                    ->options([
                        'pix' => 'PIX',
                        'credit_card' => 'Cartão de Crédito',
                        'bank_transfer' => 'Transferência Bancária',
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_as_paid')
                    ->label('Marcar como Pago')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Donation $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->action(fn (Donation $record) => $record->markAsPaid())
                    ->successNotificationTitle('Doação marcada como paga'),
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
                Infolists\Components\Section::make('Informações da Doação')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('ID da Doação')
                            ->formatStateUsing(fn ($state) => '#' . str_pad($state, 6, '0', STR_PAD_LEFT))
                            ->weight(FontWeight::Bold),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->formatStateUsing(fn (Donation $record) => $record->status_label)
                            ->badge()
                            ->color(fn (Donation $record) => $record->status_color),

                        Infolists\Components\TextEntry::make('amount')
                            ->label('Valor')
                            ->formatStateUsing(fn (Donation $record) => $record->formatted_amount)
                            ->weight(FontWeight::Bold),

                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Método de Pagamento')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'pix' => 'PIX',
                                'credit_card' => 'Cartão de Crédito',
                                'bank_transfer' => 'Transferência Bancária',
                                default => $state
                            }),
                    ])->columns(2),

                Infolists\Components\Section::make('Informações do Doador')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nome'),

                        Infolists\Components\TextEntry::make('email')
                            ->label('Email'),

                        Infolists\Components\TextEntry::make('phone')
                            ->label('Telefone'),

                        Infolists\Components\TextEntry::make('ip_address')
                            ->label('Endereço IP'),
                    ])->columns(2),

                Infolists\Components\Section::make('Datas')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime('d/m/Y H:i:s'),

                        Infolists\Components\TextEntry::make('paid_at')
                            ->label('Pago em')
                            ->dateTime('d/m/Y H:i:s'),

                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('Expira em')
                            ->dateTime('d/m/Y H:i:s'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->dateTime('d/m/Y H:i:s'),
                    ])->columns(2),

                Infolists\Components\Section::make('Informações de Pagamento')
                    ->schema([
                        Infolists\Components\TextEntry::make('payment_id')
                            ->label('ID do Pagamento'),

                        Infolists\Components\TextEntry::make('pix_code')
                            ->label('Código PIX')
                            ->columnSpanFull(),
                    ])->columns(1),

                Infolists\Components\Section::make('Mensagem do Doador')
                    ->schema([
                        Infolists\Components\TextEntry::make('message')
                            ->label('Mensagem')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Donation $record) => !empty($record->message)),
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
            'index' => Pages\ListDonations::route('/'),
            'create' => Pages\CreateDonation::route('/create'),
            'view' => Pages\ViewDonation::route('/{record}'),
            'edit' => Pages\EditDonation::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', Donation::STATUS_PENDING)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
    
    public static function canAccess(): bool
    {
        return Auth::user()->canManageDonations() || Auth::user()->is_admin;
    }
}

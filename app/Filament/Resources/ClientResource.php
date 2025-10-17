<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Clientes';
    
    protected static ?string $modelLabel = 'Cliente';
    
    protected static ?string $pluralModelLabel = 'Clientes';
    
    protected static ?string $navigationGroup = 'Loja';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Pessoais')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Completo')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(20),
                            
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Data de Nascimento')
                            ->displayFormat('d/m/Y'),
                            
                        Forms\Components\Select::make('gender')
                            ->label('Gênero')
                            ->options([
                                'male' => 'Masculino',
                                'female' => 'Feminino',
                                'other' => 'Outro',
                                'prefer_not_to_say' => 'Prefiro não dizer',
                            ])
                            ->placeholder('Selecione'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Documentos')
                    ->schema([
                        Forms\Components\TextInput::make('cpf')
                            ->label('CPF')
                            ->mask('999.999.999-99')
                            ->unique(ignoreRecord: true)
                            ->maxLength(14),
                            
                        Forms\Components\TextInput::make('rg')
                            ->label('RG')
                            ->maxLength(20),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Configurações da Conta')
                    ->schema([
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('E-mail Verificado em')
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Conta Ativa')
                            ->default(true),
                            
                        Forms\Components\Toggle::make('accepts_marketing')
                            ->label('Aceita Marketing')
                            ->default(false),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Observações')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Observações Internas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
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
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('E-mail copiado!')
                    ->copyMessageDuration(1500),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable()
                    ->placeholder('Não informado'),
                    
                Tables\Columns\TextColumn::make('cpf')
                    ->label('CPF')
                    ->searchable()
                    ->placeholder('Não informado')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('E-mail Verificado')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->email_verified_at !== null)
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Pedidos')
                    ->counts('orders')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total Gasto')
                    ->getStateUsing(fn ($record) => $record->orders()->where('payment_status', 'paid')->sum('total'))
                    ->money('BRL')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status da Conta')
                    ->placeholder('Todos')
                    ->trueLabel('Ativo')
                    ->falseLabel('Inativo'),
                    
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('E-mail Verificado')
                    ->placeholder('Todos')
                    ->trueLabel('Verificado')
                    ->falseLabel('Não Verificado')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),
                    
                Tables\Filters\TernaryFilter::make('accepts_marketing')
                    ->label('Aceita Marketing')
                    ->placeholder('Todos')
                    ->trueLabel('Sim')
                    ->falseLabel('Não'),
                    
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Cadastrado de'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Cadastrado até'),
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
                
                Tables\Actions\Action::make('send_verification_email')
                    ->label('Enviar E-mail de Verificação')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->visible(fn (Client $record): bool => $record->email_verified_at === null)
                    ->action(function (Client $record) {
                        // Implementar envio de e-mail de verificação
                        $record->sendEmailVerificationNotification();
                    })
                    ->requiresConfirmation(),
                    
                Tables\Actions\Action::make('toggle_status')
                    ->label(fn (Client $record): string => $record->is_active ? 'Desativar' : 'Ativar')
                    ->icon(fn (Client $record): string => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Client $record): string => $record->is_active ? 'danger' : 'success')
                    ->action(function (Client $record) {
                        $record->update(['is_active' => !$record->is_active]);
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Ativar Selecionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                        })
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desativar Selecionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações Pessoais')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nome Completo'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('E-mail')
                            ->copyable()
                            ->copyMessage('E-mail copiado!')
                            ->copyMessageDuration(1500),
                        Infolists\Components\TextEntry::make('phone')
                            ->label('Telefone')
                            ->placeholder('Não informado'),
                        Infolists\Components\TextEntry::make('birth_date')
                            ->label('Data de Nascimento')
                            ->date('d/m/Y')
                            ->placeholder('Não informado'),
                        Infolists\Components\TextEntry::make('gender')
                            ->label('Gênero')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'male' => 'Masculino',
                                'female' => 'Feminino',
                                'other' => 'Outro',
                                'prefer_not_to_say' => 'Prefiro não dizer',
                                default => 'Não informado'
                            }),
                    ])
                    ->columns(2),
                    
                Infolists\Components\Section::make('Documentos')
                    ->schema([
                        Infolists\Components\TextEntry::make('cpf')
                            ->label('CPF')
                            ->placeholder('Não informado'),
                        Infolists\Components\TextEntry::make('rg')
                            ->label('RG')
                            ->placeholder('Não informado'),
                    ])
                    ->columns(2),
                    
                Infolists\Components\Section::make('Status da Conta')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Conta Ativa')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('email_verified_at')
                            ->label('E-mail Verificado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Não verificado'),
                        Infolists\Components\IconEntry::make('accepts_marketing')
                            ->label('Aceita Marketing')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Cadastrado em')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
                    
                Infolists\Components\Section::make('Estatísticas')
                    ->schema([
                        Infolists\Components\TextEntry::make('orders_count')
                            ->label('Total de Pedidos')
                            ->getStateUsing(fn ($record) => $record->orders()->count()),
                        Infolists\Components\TextEntry::make('total_spent')
                            ->label('Total Gasto')
                            ->getStateUsing(fn ($record) => $record->orders()->where('payment_status', 'paid')->sum('total'))
                            ->money('BRL'),
                        Infolists\Components\TextEntry::make('last_order_date')
                            ->label('Último Pedido')
                            ->getStateUsing(fn ($record) => $record->orders()->latest()->first()?->created_at)
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Nenhum pedido'),
                        Infolists\Components\TextEntry::make('pending_orders')
                            ->label('Pedidos Pendentes')
                            ->getStateUsing(fn ($record) => $record->orders()->whereIn('status', ['pending', 'processing'])->count()),
                    ])
                    ->columns(2),
                    
                Infolists\Components\Section::make('Observações')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Observações Internas')
                            ->placeholder('Nenhuma observação')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->notes)),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
    
    public static function canAccess(): bool
    {
        return Auth::user()->canManageStore() || Auth::user()->is_admin;
    }
}

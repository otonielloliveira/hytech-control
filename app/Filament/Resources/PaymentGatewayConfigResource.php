<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentGatewayConfigResource\Pages;
use App\Models\PaymentGatewayConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;

class PaymentGatewayConfigResource extends Resource
{
    protected static ?string $model = PaymentGatewayConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Gateways de Pagamento';

    protected static ?string $pluralModelLabel = 'Gateways de Pagamento';

    protected static ?string $modelLabel = 'Gateway de Pagamento';

    protected static ?string $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\Select::make('gateway')
                            ->label('Gateway')
                            ->options(PaymentGatewayConfig::getAvailableGateways())
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => [
                                $set('name', PaymentGatewayConfig::getAvailableGateways()[$state] ?? '')
                            ]),
                        
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->helperText('Apenas um gateway pode estar ativo por vez')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $record) {
                                if ($state && $record) {
                                    // Desativar outros gateways
                                    PaymentGatewayConfig::where('id', '!=', $record->id)
                                        ->update(['is_active' => false]);
                                }
                            }),
                        
                        Forms\Components\Toggle::make('is_sandbox')
                            ->label('Modo Sandbox')
                            ->helperText('Ativar para ambiente de teste')
                            ->default(true),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem de Exibição')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Credenciais')
                    ->schema([
                        // MercadoPago
                        Forms\Components\TextInput::make('credentials.access_token')
                            ->label('Access Token')
                            ->password()
                            ->revealable()
                            ->visible(fn ($get) => $get('gateway') === 'mercadopago')
                            ->required(fn ($get) => $get('gateway') === 'mercadopago'),
                        
                        // EFI Pay
                        Forms\Components\TextInput::make('credentials.client_id')
                            ->label('Client ID')
                            ->visible(fn ($get) => $get('gateway') === 'efipay')
                            ->required(fn ($get) => $get('gateway') === 'efipay'),
                        
                        Forms\Components\TextInput::make('credentials.client_secret')
                            ->label('Client Secret')
                            ->password()
                            ->revealable()
                            ->visible(fn ($get) => $get('gateway') === 'efipay')
                            ->required(fn ($get) => $get('gateway') === 'efipay'),
                        
                        // PagSeguro
                        Forms\Components\TextInput::make('credentials.email')
                            ->label('Email')
                            ->email()
                            ->visible(fn ($get) => $get('gateway') === 'pagseguro')
                            ->required(fn ($get) => $get('gateway') === 'pagseguro'),
                        
                        Forms\Components\TextInput::make('credentials.token')
                            ->label('Token')
                            ->password()
                            ->revealable()
                            ->visible(fn ($get) => $get('gateway') === 'pagseguro')
                            ->required(fn ($get) => $get('gateway') === 'pagseguro'),
                    ])->columns(2),

                Forms\Components\Section::make('Informações Adicionais')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\KeyValue::make('settings')
                            ->label('Configurações Extras')
                            ->keyLabel('Chave')
                            ->valueLabel('Valor')
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
                    ->weight(FontWeight::Medium),

                Tables\Columns\BadgeColumn::make('gateway')
                    ->label('Gateway')
                    ->formatStateUsing(fn ($state) => PaymentGatewayConfig::getAvailableGateways()[$state] ?? $state)
                    ->colors([
                        'primary' => 'mercadopago',
                        'success' => 'efipay',
                        'warning' => 'pagseguro',
                    ]),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (PaymentGatewayConfig $record) => $record->getStatusLabel())
                    ->colors([
                        'success' => fn (PaymentGatewayConfig $record) => $record->getStatusBadgeColor() === 'success',
                        'warning' => fn (PaymentGatewayConfig $record) => $record->getStatusBadgeColor() === 'warning',
                        'gray' => fn (PaymentGatewayConfig $record) => $record->getStatusBadgeColor() === 'gray',
                    ]),

                Tables\Columns\IconColumn::make('is_sandbox')
                    ->label('Sandbox')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->options(PaymentGatewayConfig::getAvailableGateways()),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo'),

                Tables\Filters\TernaryFilter::make('is_sandbox')
                    ->label('Sandbox'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('health_check')
                    ->label('Health Check Geral')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->action(function () {
                        $paymentManager = app(\App\Services\PaymentManager::class);
                        $configs = PaymentGatewayConfig::where('is_active', true)->orWhere(function($query) {
                            $query->whereJsonLength('credentials', '>', 0);
                        })->get();
                        
                        if ($configs->isEmpty()) {
                            \Filament\Notifications\Notification::make()
                                ->title('⚠️ Nenhum Gateway Configurado')
                                ->body('Não há gateways ativos ou configurados para testar.')
                                ->warning()
                                ->send();
                            return;
                        }
                        
                        $results = [];
                        $activeCount = 0;
                        $configuredCount = 0;
                        $workingCount = 0;
                        
                        foreach ($configs as $config) {
                            if ($config->is_active) $activeCount++;
                            if ($config->isConfigured()) {
                                $configuredCount++;
                                $result = $paymentManager->testGatewayConnection($config);
                                if ($result['success']) $workingCount++;
                                
                                $results[] = [
                                    'name' => $config->name,
                                    'active' => $config->is_active,
                                    'configured' => true,
                                    'working' => $result['success'],
                                    'message' => $result['message']
                                ];
                            } else {
                                $results[] = [
                                    'name' => $config->name,
                                    'active' => $config->is_active,
                                    'configured' => false,
                                    'working' => false,
                                    'message' => 'Não configurado'
                                ];
                            }
                        }
                        
                        $summary = "📊 Resumo do Sistema:" .
                            "<br>• Gateways Ativos: {$activeCount}" .
                            "<br>• Gateways Configurados: {$configuredCount}" .
                            "<br>• Gateways Funcionando: {$workingCount}";
                        
                        $details = collect($results)->map(function ($result) {
                            $status = $result['active'] ? '🟢' : '⚪';
                            $config = $result['configured'] ? '⚙️' : '❌';
                            $working = $result['working'] ? '✅' : '❌';
                            return "{$status}{$config}{$working} {$result['name']}: {$result['message']}";
                        })->join('<br>');
                        
                        $overallStatus = $activeCount > 0 && $workingCount > 0 ? 'success' : 'warning';
                        
                        \Filament\Notifications\Notification::make()
                            ->title('🏥 Health Check Completo')
                            ->body($summary . '<br><br>📋 Detalhes:<br>' . $details)
                            ->color($overallStatus)
                            ->duration(15000)
                            ->send();
                    })
                    ->modalHeading('Health Check do Sistema de Pagamentos')
                    ->modalDescription('Esta ação irá verificar o status de todos os gateways de pagamento configurados.')
                    ->requiresConfirmation(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('test_connection')
                    ->label('Testar Conexão')
                    ->icon('heroicon-o-wifi')
                    ->color('info')
                    ->visible(fn (PaymentGatewayConfig $record) => $record->isConfigured())
                    ->action(function (PaymentGatewayConfig $record) {
                        $paymentManager = app(\App\Services\PaymentManager::class);
                        $result = $paymentManager->testGatewayConnection($record);
                        
                        if ($result['success']) {
                            \Filament\Notifications\Notification::make()
                                ->title('✅ Conexão Bem-sucedida!')
                                ->body($result['message'])
                                ->success()
                                ->duration(5000)
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('❌ Falha na Conexão')
                                ->body($result['message'] . '<br><small>' . $result['details'] . '</small>')
                                ->danger()
                                ->duration(8000)
                                ->send();
                        }
                    })
                    ->modalSubmitActionLabel('Testar')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalHeading(fn (PaymentGatewayConfig $record) => 'Testar Conexão - ' . $record->name)
                    ->modalDescription('Esta ação irá verificar se as credenciais estão corretas e se é possível estabelecer conexão com o gateway de pagamento.')
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('activate')
                    ->label('Ativar')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (PaymentGatewayConfig $record) => !$record->is_active && $record->isConfigured())
                    ->requiresConfirmation()
                    ->action(function (PaymentGatewayConfig $record) {
                        // Desativar outros
                        PaymentGatewayConfig::where('id', '!=', $record->id)
                            ->update(['is_active' => false]);
                        
                        // Ativar este
                        $record->update(['is_active' => true]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('test_all_connections')
                        ->label('Testar Todas as Conexões')
                        ->icon('heroicon-o-wifi')
                        ->color('info')
                        ->action(function ($records) {
                            $paymentManager = app(\App\Services\PaymentManager::class);
                            $results = [];
                            $successCount = 0;
                            $failureCount = 0;
                            
                            foreach ($records as $record) {
                                if ($record->isConfigured()) {
                                    $result = $paymentManager->testGatewayConnection($record);
                                    $results[] = [
                                        'gateway' => $record->name,
                                        'success' => $result['success'],
                                        'message' => $result['message']
                                    ];
                                    
                                    if ($result['success']) {
                                        $successCount++;
                                    } else {
                                        $failureCount++;
                                    }
                                } else {
                                    $results[] = [
                                        'gateway' => $record->name,
                                        'success' => false,
                                        'message' => 'Gateway não configurado'
                                    ];
                                    $failureCount++;
                                }
                            }
                            
                            $title = $failureCount === 0 ? 
                                '✅ Todos os Testes Passaram!' : 
                                "⚠️ Resultados: {$successCount} sucessos, {$failureCount} falhas";
                            
                            $body = collect($results)->map(function ($result) {
                                $icon = $result['success'] ? '✅' : '❌';
                                return "{$icon} {$result['gateway']}: {$result['message']}";
                            })->join('<br>');
                            
                            \Filament\Notifications\Notification::make()
                                ->title($title)
                                ->body($body)
                                ->color($failureCount === 0 ? 'success' : 'warning')
                                ->duration(10000)
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Testar Conexões dos Gateways Selecionados')
                        ->modalDescription('Esta ação irá testar a conexão com todos os gateways selecionados que estão configurados.')
                        ->modalSubmitActionLabel('Testar Todos'),
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
            'index' => Pages\ListPaymentGatewayConfigs::route('/'),
            'create' => Pages\CreatePaymentGatewayConfig::route('/create'),
            'edit' => Pages\EditPaymentGatewayConfig::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $active = static::getModel()::active()->count();
        return $active > 0 ? null : '!';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $active = static::getModel()::active()->count();
        return $active > 0 ? null : 'danger';
    }
}

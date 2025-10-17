<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PollResource\Pages;
use App\Filament\Resources\PollResource\RelationManagers;
use App\Models\Poll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PollResource extends Resource
{
    protected static ?string $model = Poll::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationGroup = 'Blog Sidebar';
    
    protected static ?string $modelLabel = 'Enquete';
    
    protected static ?string $pluralModelLabel = 'Enquetes';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Enquete')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->columnSpanFull(),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Ativa')
                                    ->default(true)
                                    ->helperText('Apenas uma enquete pode estar ativa por vez. Ativar esta enquete desativará todas as outras.')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            // Avisar que outras enquetes serão desativadas
                                        }
                                    }),
                                    
                                Forms\Components\TextInput::make('priority')
                                    ->label('Prioridade')
                                    ->numeric()
                                    ->default(1)
                                    ->helperText('Número menor = maior prioridade'),
                            ]),
                    ]),
                    
                Forms\Components\Section::make('Prazo da Enquete')
                    ->schema([
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Data de Expiração')
                            ->helperText('Deixe vazio para enquete sem prazo')
                            ->native(false),
                    ]),
                    
                Forms\Components\Section::make('Opções de Votação')
                    ->schema([
                        Forms\Components\Repeater::make('options')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('option_text')
                                    ->label('Texto da Opção')
                                    ->required()
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('priority')
                                    ->label('Ordem')
                                    ->numeric()
                                    ->default(1),
                            ])
                            ->columns(2)
                            ->defaultItems(2)
                            ->minItems(2)
                            ->maxItems(10)
                            ->addActionLabel('Adicionar Opção')
                            ->reorderableWithButtons()
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('title')
                        ->weight('semibold')
                        ->searchable()
                        ->sortable()
                        ->limit(40)
                        ->tooltip(function (Model $record): ?string {
                            return strlen($record->title) > 40 ? $record->title : null;
                        }),
                    
                    Tables\Columns\TextColumn::make('description')
                        ->color('gray')
                        ->size('sm')
                        ->limit(60)
                        ->tooltip(function (Model $record): ?string {
                            return $record->description && strlen($record->description) > 60 ? $record->description : null;
                        }),
                ])->space(1),
                
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('options_count')
                        ->label('Opções')
                        ->counts('options')
                        ->badge()
                        ->color('info')
                        ->size('sm')
                        ->suffix(' opções'),
                    
                    Tables\Columns\TextColumn::make('total_votes')
                        ->label('Votos')
                        ->badge()
                        ->color('success')
                        ->size('sm')
                        ->suffix(' votos'),
                ])->from('sm'),
                
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expira')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->placeholder('Sem prazo')
                    ->color('warning')
                    ->size('sm')
                    ->alignCenter(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-s-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->alignCenter()
                    ->action(function ($record) {
                        if ($record->is_active) {
                            $record->update(['is_active' => false]);
                        } else {
                            // Desativar todas as outras e ativar esta
                            \App\Models\Poll::query()->update(['is_active' => false]);
                            $record->update(['is_active' => true]);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Status alterado com sucesso!')
                            ->success()
                            ->send();
                    })
                    ->tooltip('Clique para ativar/desativar'),
            ])
            ->defaultSort('priority', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Ativas')
                    ->falseLabel('Inativas'),
                    
                Tables\Filters\Filter::make('expired')
                    ->label('Expiradas')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now()))
                    ->toggle(),
            ])
            ->recordAction('edit')
            ->recordUrl(fn (Model $record): string => static::getUrl('edit', ['record' => $record]))
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Ver Resultados')
                        ->icon('heroicon-o-chart-bar')
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning'),
                    Tables\Actions\Action::make('activate')
                        ->label('Ativar')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->visible(fn ($record) => !$record->is_active)
                        ->action(function ($record) {
                            \App\Models\Poll::query()->update(['is_active' => false]);
                            $record->update(['is_active' => true]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Enquete ativada com sucesso!')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Ativar Enquete')
                        ->modalDescription('Isto irá desativar todas as outras enquetes. Apenas uma enquete pode estar ativa por vez.')
                        ->modalSubmitActionLabel('Sim, ativar'),
                        
                    Tables\Actions\Action::make('deactivate')
                        ->label('Desativar')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->visible(fn ($record) => $record->is_active)
                        ->action(function ($record) {
                            $record->update(['is_active' => false]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Enquete desativada!')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                ])
                ->tooltip('Ações')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações da Enquete')
                    ->schema([
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
                                    ->color(fn ($state) => match($state) {
                                        'single' => 'primary',
                                        'multiple' => 'success',
                                        'rating' => 'warning',
                                        default => 'gray'
                                    }),
                                
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Ativo')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-mark')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                                
                                Infolists\Components\TextEntry::make('start_date')
                                    ->label('Data de Início')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Não definida'),
                                
                                Infolists\Components\TextEntry::make('end_date')
                                    ->label('Data de Término')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Não definida'),
                                
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
                    ]),
                
                Infolists\Components\Section::make('Descrição')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label('')
                            ->html()
                            ->placeholder('Nenhuma descrição fornecida'),
                    ])
                    ->collapsible(),
                
                Infolists\Components\Section::make('Estatísticas')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('total_votes')
                                    ->label('Total de Votos')
                                    ->numeric()
                                    ->default(0)
                                    ->badge()
                                    ->color('success'),
                                
                                Infolists\Components\TextEntry::make('options_count')
                                    ->label('Opções')
                                    ->state(fn ($record) => $record->options()->count())
                                    ->numeric()
                                    ->badge()
                                    ->color('info'),
                                
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->state(fn ($record) => $record->end_date && $record->end_date->isPast() ? 'Encerrada' : 'Ativa')
                                    ->badge()
                                    ->color(fn ($state) => $state === 'Ativa' ? 'success' : 'danger'),
                            ]),
                    ])
                    ->collapsible(),
                
                Infolists\Components\Section::make('Informações do Sistema')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Criado em')
                                    ->dateTime('d/m/Y H:i'),
                                
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Atualizado em')
                                    ->dateTime('d/m/Y H:i'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPolls::route('/'),
            'create' => Pages\CreatePoll::route('/create'),
            'view' => Pages\ViewPoll::route('/{record}'),
            'edit' => Pages\EditPoll::route('/{record}/edit'),
        ];
    }
    
    public static function canAccess(): bool
    {
        return Auth::user()->canManageSettings() || Auth::user()->is_admin;
    }
}

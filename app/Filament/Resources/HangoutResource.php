<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HangoutResource\Pages;
use App\Models\Hangout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;

class HangoutResource extends Resource
{
    protected static ?string $model = Hangout::class;
    
    protected static ?string $modelLabel = 'Hangout';
    protected static ?string $pluralModelLabel = 'Hangouts';
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationGroup = 'Blog Sidebar';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->maxLength(1000),
                        
                        FileUpload::make('cover_image')
                            ->label('Imagem de Capa')
                            ->image()
                            ->directory('hangouts/covers')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageCropAspectRatio('16:9'),
                    ])->columns(2),

                Forms\Components\Section::make('Configurações da Reunião')
                    ->schema([
                        Forms\Components\Select::make('platform')
                            ->label('Plataforma')
                            ->required()
                            ->options([
                                'google-meet' => 'Google Meet',
                                'zoom' => 'Zoom',
                                'teams' => 'Microsoft Teams',
                                'discord' => 'Discord',
                                'jitsi' => 'Jitsi Meet',
                                'webex' => 'Cisco Webex',
                            ])
                            ->searchable(),
                        
                        Forms\Components\TextInput::make('meeting_link')
                            ->label('Link da Reunião')
                            ->url()
                            ->required(),
                        
                        Forms\Components\TextInput::make('meeting_id')
                            ->label('ID da Reunião')
                            ->helperText('ID ou código da reunião (opcional)'),
                        
                        Forms\Components\TextInput::make('meeting_password')
                            ->label('Senha da Reunião')
                            ->password()
                            ->helperText('Senha para acessar a reunião (opcional)'),
                    ])->columns(2),

                Forms\Components\Section::make('Agendamento')
                    ->schema([
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Data e Hora')
                            ->required()
                            ->timezone(config('app.timezone'))
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),
                        
                        Forms\Components\TextInput::make('duration_minutes')
                            ->label('Duração (minutos)')
                            ->numeric()
                            ->required()
                            ->default(60)
                            ->minValue(1)
                            ->maxValue(480),
                        
                        Forms\Components\TextInput::make('max_participants')
                            ->label('Máximo de Participantes')
                            ->numeric()
                            ->helperText('Deixe em branco para ilimitado'),
                    ])->columns(3),

                Forms\Components\Section::make('Organizador')
                    ->schema([
                        Forms\Components\TextInput::make('host_name')
                            ->label('Nome do Anfitrião')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('host_email')
                            ->label('Email do Anfitrião')
                            ->email()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Agenda')
                    ->schema([
                        Forms\Components\RichEditor::make('agenda')
                            ->label('Agenda da Reunião')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                            ]),
                    ]),

                Forms\Components\Section::make('Configurações')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'scheduled' => 'Agendado',
                                'live' => 'Ao Vivo',
                                'ended' => 'Finalizado',
                                'cancelled' => 'Cancelado',
                            ])
                            ->default('scheduled'),
                        
                        Forms\Components\TextInput::make('priority')
                            ->label('Prioridade')
                            ->numeric()
                            ->default(0)
                            ->helperText('Menor número = maior prioridade'),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Público')
                            ->default(true)
                            ->helperText('Hangout visível para todos'),
                        
                        Forms\Components\Toggle::make('requires_registration')
                            ->label('Requer Inscrição')
                            ->default(false)
                            ->helperText('Usuários precisam se inscrever previamente'),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Hangout')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Título')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight('bold'),
                                    
                                Infolists\Components\TextEntry::make('platform')
                                    ->label('Plataforma')
                                    ->badge()
                                    ->color('info')
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'zoom' => 'Zoom',
                                        'teams' => 'Microsoft Teams',
                                        'meet' => 'Google Meet',
                                        'discord' => 'Discord',
                                        'jitsi' => 'Jitsi Meet',
                                        'webex' => 'Cisco Webex',
                                        default => $state
                                    }),
                            ]),
                            
                        Infolists\Components\TextEntry::make('description')
                            ->label('Descrição')
                            ->prose()
                            ->columnSpanFull()
                            ->placeholder('Nenhuma descrição fornecida'),
                    ])->columns(2),
                    
                Infolists\Components\Section::make('Agendamento')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('scheduled_at')
                                    ->label('Data e Hora')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-m-calendar'),
                                    
                                Infolists\Components\TextEntry::make('duration')
                                    ->label('Duração')
                                    ->suffix(' minutos')
                                    ->icon('heroicon-m-clock'),
                                    
                                Infolists\Components\TextEntry::make('max_participants')
                                    ->label('Máx. Participantes')
                                    ->icon('heroicon-m-users')
                                    ->placeholder('Ilimitado'),
                            ]),
                    ])->columns(3),
                    
                Infolists\Components\Section::make('Status e Configurações')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'scheduled' => 'warning',
                                        'live' => 'success',
                                        'ended' => 'gray',
                                        'cancelled' => 'danger',
                                        default => 'gray'
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'scheduled' => 'Agendado',
                                        'live' => 'Ao Vivo',
                                        'ended' => 'Finalizado',
                                        'cancelled' => 'Cancelado',
                                        default => $state
                                    }),
                                    
                                Infolists\Components\IconEntry::make('is_public')
                                    ->label('Público')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-globe-alt')
                                    ->falseIcon('heroicon-o-lock-closed')
                                    ->trueColor('success')
                                    ->falseColor('warning'),
                                    
                                Infolists\Components\IconEntry::make('requires_registration')
                                    ->label('Requer Inscrição')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-user-plus')
                                    ->falseIcon('heroicon-o-users')
                                    ->trueColor('info')
                                    ->falseColor('gray'),
                                    
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Ativo')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),
                    ])->columns(4),
                    
                Infolists\Components\Section::make('Acesso ao Hangout')
                    ->schema([
                        Infolists\Components\TextEntry::make('meeting_link')
                            ->label('Link da Reunião')
                            ->copyable()
                            ->icon('heroicon-m-link')
                            ->placeholder('Link não configurado'),
                            
                        Infolists\Components\Actions::make([
                            Infolists\Components\Actions\Action::make('join')
                                ->label('Entrar no Hangout')
                                ->icon('heroicon-o-video-camera')
                                ->color('success')
                                ->url(fn ($record) => $record->meeting_link)
                                ->openUrlInNewTab()
                                ->visible(fn ($record) => !empty($record->meeting_link)),
                        ]),
                    ]),
                    
                Infolists\Components\Section::make('Mídia')
                    ->schema([
                        Infolists\Components\ImageEntry::make('cover_image')
                            ->label('Imagem de Capa')
                            ->disk('public')
                            ->width(400)
                            ->height(200)
                            ->placeholder('Nenhuma imagem definida'),
                    ])
                    ->visible(fn ($record) => !empty($record->cover_image)),
                    
                Infolists\Components\Section::make('Informações Adicionais')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Criado em')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-m-calendar'),
                                    
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Atualizado em')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-m-pencil'),
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Capa')
                    ->size(60)
                    ->defaultImageUrl(asset('images/hangout-default.png')),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('platform')
                    ->label('Plataforma')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'google-meet' => 'info',
                        'zoom' => 'primary',
                        'teams' => 'warning',
                        'discord' => 'indigo',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'google-meet' => 'Google Meet',
                        'zoom' => 'Zoom',
                        'teams' => 'Teams',
                        'discord' => 'Discord',
                        'jitsi' => 'Jitsi Meet',
                        'webex' => 'Webex',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duração')
                    ->formatStateUsing(fn ($state) => $state . 'min')
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'primary',
                        'live' => 'success',
                        'ended' => 'gray',
                        'cancelled' => 'danger',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Agendado',
                        'live' => 'Ao Vivo',
                        'ended' => 'Finalizado',
                        'cancelled' => 'Cancelado',
                        default => ucfirst($state),
                    }),
                
                Tables\Columns\TextColumn::make('host_name')
                    ->label('Anfitrião')
                    ->searchable()
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->sortable()
                    ->alignCenter(),
                
                ToggleColumn::make('is_public')
                    ->label('Público'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->label('Plataforma')
                    ->options([
                        'google-meet' => 'Google Meet',
                        'zoom' => 'Zoom',
                        'teams' => 'Microsoft Teams',
                        'discord' => 'Discord',
                        'jitsi' => 'Jitsi Meet',
                        'webex' => 'Cisco Webex',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'scheduled' => 'Agendado',
                        'live' => 'Ao Vivo',
                        'ended' => 'Finalizado',
                        'cancelled' => 'Cancelado',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Público'),
                
                Tables\Filters\TernaryFilter::make('requires_registration')
                    ->label('Requer Inscrição'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('join')
                    ->label('Entrar')
                    ->icon('heroicon-o-video-camera')
                    ->color('success')
                    ->url(fn (Hangout $record): string => $record->meeting_link)
                    ->openUrlInNewTab()
                    ->visible(fn (Hangout $record): bool => $record->canJoin()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scheduled_at', 'asc');
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
            'index' => Pages\ListHangouts::route('/'),
            'create' => Pages\CreateHangout::route('/create'),
            'view' => Pages\ViewHangout::route('/{record}'),
            'edit' => Pages\EditHangout::route('/{record}/edit'),
        ];
    }
}

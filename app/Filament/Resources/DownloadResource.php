<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DownloadResource\Pages;
use App\Filament\Resources\DownloadResource\RelationManagers;
use App\Models\Download;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DownloadResource extends Resource
{
    protected static ?string $model = Download::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    
    protected static ?string $navigationGroup = 'Blog Sidebar';
    
    protected static ?string $modelLabel = 'Download';
    
    protected static ?string $pluralModelLabel = 'Downloads';
    
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Arquivo')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->columnSpanFull(),
                            
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Arquivo')
                            ->required()
                            ->directory('downloads')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'image/*', 'video/*', 'audio/*'])
                            ->maxSize(50 * 1024) // 50MB
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $file = $state;
                                    $originalName = $file->getClientOriginalName();
                                    $size = $file->getSize();
                                    $type = $file->getClientOriginalExtension();
                                    
                                    $set('file_name', $originalName);
                                    $set('file_size', $size);
                                    $set('file_type', $type);
                                }
                            })
                            ->columnSpanFull(),
                            
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('file_name')
                                    ->label('Nome do Arquivo')
                                    ->disabled()
                                    ->dehydrated(),
                                    
                                Forms\Components\TextInput::make('file_size')
                                    ->label('Tamanho (bytes)')
                                    ->disabled()
                                    ->dehydrated(),
                                    
                                Forms\Components\TextInput::make('file_type')
                                    ->label('Tipo do Arquivo')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ]),
                    
                Forms\Components\Section::make('Configurações')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('category')
                                    ->label('Categoria')
                                    ->placeholder('Ex: Documentos, Manuais, Software'),
                                    
                                Forms\Components\Select::make('icon')
                                    ->label('Ícone')
                                    ->options([
                                        'fas fa-file-pdf' => 'PDF',
                                        'fas fa-file-word' => 'Word',
                                        'fas fa-file-excel' => 'Excel',
                                        'fas fa-file-powerpoint' => 'PowerPoint',
                                        'fas fa-file-archive' => 'Arquivo ZIP/RAR',
                                        'fas fa-file-image' => 'Imagem',
                                        'fas fa-file-video' => 'Vídeo',
                                        'fas fa-file-audio' => 'Áudio',
                                        'fas fa-file-code' => 'Código',
                                        'fas fa-download' => 'Download Genérico',
                                    ])
                                    ->placeholder('Auto-detectar baseado no tipo'),
                            ]),
                            
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Ativo')
                                    ->default(true),
                                    
                                Forms\Components\Toggle::make('requires_login')
                                    ->label('Requer Login')
                                    ->default(false),
                                    
                                Forms\Components\TextInput::make('priority')
                                    ->label('Prioridade')
                                    ->numeric()
                                    ->default(1)
                                    ->helperText('Número menor = maior prioridade'),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Arquivo')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Título')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight('bold'),
                                    
                                Infolists\Components\TextEntry::make('file_name')
                                    ->label('Nome do Arquivo')
                                    ->copyable()
                                    ->icon('heroicon-m-document-text'),
                            ]),
                            
                        Infolists\Components\TextEntry::make('description')
                            ->label('Descrição')
                            ->prose()
                            ->columnSpanFull()
                            ->placeholder('Nenhuma descrição fornecida'),
                    ])->columns(2),
                    
                Infolists\Components\Section::make('Detalhes Técnicos')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('file_type')
                                    ->label('Tipo')
                                    ->badge()
                                    ->color('info'),
                                    
                                Infolists\Components\TextEntry::make('formatted_file_size')
                                    ->label('Tamanho')
                                    ->icon('heroicon-m-scale'),
                                    
                                Infolists\Components\TextEntry::make('download_count')
                                    ->label('Downloads')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-m-arrow-down-tray'),
                            ]),
                    ])->columns(3),
                    
                Infolists\Components\Section::make('Configurações')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('category')
                                    ->label('Categoria')
                                    ->badge()
                                    ->color('warning')
                                    ->placeholder('Sem categoria'),
                                    
                                Infolists\Components\TextEntry::make('priority')
                                    ->label('Prioridade')
                                    ->badge()
                                    ->color('primary'),
                                    
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),
                    ])->columns(3),
                    
                Infolists\Components\Section::make('Acesso e Segurança')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\IconEntry::make('requires_login')
                                    ->label('Requer Login')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-lock-closed')
                                    ->falseIcon('heroicon-o-lock-open')
                                    ->trueColor('warning')
                                    ->falseColor('success'),
                                    
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Criado em')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-m-calendar'),
                            ]),
                    ])->columns(2),
                    
                Infolists\Components\Section::make('Ações do Arquivo')
                    ->schema([
                        Infolists\Components\Actions::make([
                            Infolists\Components\Actions\Action::make('download')
                                ->label('Fazer Download')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('success')
                                ->url(fn ($record) => $record->file_url)
                                ->openUrlInNewTab(),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('file_name')
                    ->label('Arquivo')
                    ->searchable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('file_type')
                    ->label('Tipo')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('formatted_file_size')
                    ->label('Tamanho')
                    ->sortable('file_size'),
                    
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->color('warning')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('download_count')
                    ->label('Downloads')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\IconColumn::make('requires_login')
                    ->label('Login')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('success'),
                    
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('priority', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos'),
                    
                Tables\Filters\TernaryFilter::make('requires_login')
                    ->label('Requer Login')
                    ->placeholder('Todos')
                    ->trueLabel('Sim')
                    ->falseLabel('Não'),
                    
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoria')
                    ->options(fn () => \App\Models\Download::getCategories()),
                    
                Tables\Filters\SelectFilter::make('file_type')
                    ->label('Tipo de Arquivo')
                    ->options([
                        'pdf' => 'PDF',
                        'doc' => 'Word',
                        'docx' => 'Word',
                        'xls' => 'Excel',
                        'xlsx' => 'Excel',
                        'zip' => 'ZIP',
                        'rar' => 'RAR',
                        'jpg' => 'Imagem',
                        'png' => 'Imagem',
                        'mp4' => 'Vídeo',
                        'mp3' => 'Áudio',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record) => $record->file_url)
                    ->openUrlInNewTab()
                    ->action(fn ($record) => $record->incrementDownloadCount()),
                    
                Tables\Actions\ViewAction::make()
                    ->label('Visualizar'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                ]),
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
            'index' => Pages\ListDownloads::route('/'),
            'create' => Pages\CreateDownload::route('/create'),
            'edit' => Pages\EditDownload::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoticeResource\Pages;
use App\Filament\Resources\NoticeResource\RelationManagers;
use App\Models\Notice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NoticeResource extends Resource
{
    protected static ?string $model = Notice::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    
    protected static ?string $navigationLabel = 'Recados';
    
    protected static ?string $modelLabel = 'Recado';
    
    protected static ?string $pluralModelLabel = 'Recados';
    
    protected static ?string $navigationGroup = 'Blog Sidebar';

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
                        
                        Forms\Components\RichEditor::make('content')
                            ->label('Conteúdo')
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagem')
                            ->image()
                            ->directory('notices')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Configurações de Link')
                    ->schema([
                        Forms\Components\Select::make('link_type')
                            ->label('Tipo de Link')
                            ->options([
                                'none' => 'Sem Link',
                                'internal' => 'Link Interno',
                                'external' => 'Link Externo',
                            ])
                            ->default('none')
                            ->reactive()
                            ->required(),
                        
                        Forms\Components\TextInput::make('link_url')
                            ->label('URL do Link')
                            ->url()
                            ->visible(fn (Forms\Get $get): bool => $get('link_type') === 'external'),
                        
                        Forms\Components\TextInput::make('link_url')
                            ->label('Slug/Rota Interna')
                            ->helperText('Ex: slug-do-post ou ID do registro')
                            ->visible(fn (Forms\Get $get): bool => $get('link_type') === 'internal'),
                        
                        Forms\Components\Select::make('internal_route')
                            ->label('Rota Interna')
                            ->options([
                                'blog.post.show' => 'Post do Blog',
                                'blog.category.show' => 'Categoria do Blog',
                                'blog.tag.show' => 'Tag do Blog',
                            ])
                            ->visible(fn (Forms\Get $get): bool => $get('link_type') === 'internal'),
                    ]),
                
                Forms\Components\Section::make('Configurações de Exibição')
                    ->schema([
                        Forms\Components\TextInput::make('priority')
                            ->label('Prioridade')
                            ->helperText('Maior número = maior prioridade')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true)
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Data de Início')
                            ->helperText('Deixe vazio para exibir imediatamente'),
                        
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Data de Fim')
                            ->helperText('Deixe vazio para exibir indefinidamente'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->size(50)
                    ->defaultImageUrl(asset('images/default-no-image.png')),
                
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
                
                Tables\Columns\TextColumn::make('link_type')
                    ->label('Tipo')
                    ->badge()
                    ->size('sm')
                    ->color(fn (string $state): string => match ($state) {
                        'none' => 'gray',
                        'internal' => 'info',
                        'external' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'none' => 'Sem Link',
                        'internal' => 'Interno',
                        'external' => 'Externo',
                        default => $state,
                    })
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('warning')
                    ->size('sm'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-s-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('link_type')
                    ->label('Tipo de Link')
                    ->options([
                        'none' => 'Sem Link',
                        'internal' => 'Link Interno',
                        'external' => 'Link Externo',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status'),
            ])
            ->recordAction('edit')
            ->recordUrl(fn (Model $record): string => static::getUrl('edit', ['record' => $record]))
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning'),
                    Tables\Actions\Action::make('duplicate')
                        ->label('Duplicar')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->action(function ($record) {
                            $newNotice = $record->replicate();
                            $newNotice->title = $record->title . ' (Cópia)';
                            $newNotice->is_active = false;
                            $newNotice->save();
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Aviso duplicado com sucesso!')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn ($record) => $record->is_active ? 'Desativar' : 'Ativar')
                        ->icon(fn ($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn ($record) => $record->is_active ? 'warning' : 'success')
                        ->action(function ($record) {
                            $record->is_active = !$record->is_active;
                            $record->save();
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Status alterado com sucesso!')
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('priority', 'desc');
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
            'index' => Pages\ListNotices::route('/'),
            'create' => Pages\CreateNotice::route('/create'),
            'edit' => Pages\EditNotice::route('/{record}/edit'),
        ];
    }
}

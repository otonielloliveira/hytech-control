<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SidebarConfigResource\Pages;
use App\Filament\Resources\SidebarConfigResource\RelationManagers;
use App\Models\SidebarConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SidebarConfigResource extends Resource
{
    protected static ?string $model = SidebarConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    
    protected static ?string $navigationLabel = 'Configuração Widgets';
    
    protected static ?string $modelLabel = 'Widget';
    
    protected static ?string $pluralModelLabel = 'Widgets da Sidebar';
    
    protected static ?string $navigationGroup = 'Blog Sidebar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Widget')
                    ->schema([
                        Forms\Components\Select::make('widget_name')
                            ->label('Tipo do Widget')
                            ->options([
                                'notices' => 'Recados',
                                'tags' => 'Tags',
                                'youtube' => 'Canal YouTube', 
                                'polls' => 'Enquetes',
                                'lectures' => 'Palestras',
                                'hangouts' => 'Hangouts',
                                'books' => 'Livros Recomendados',
                                'downloads' => 'Downloads',
                            ])
                            ->required()
                            ->disabled(fn ($record) => $record?->exists)
                            ->helperText('Tipo do widget (não pode ser alterado após criação)'),
                        
                        Forms\Components\TextInput::make('display_name')
                            ->label('Nome de Exibição')
                            ->placeholder('Ex: ENQUETES, VOTAÇÕES, etc.')
                            ->helperText('Nome que aparecerá no site. Deixe vazio para usar o nome padrão.')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true)
                            ->required(),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem')
                            ->helperText('Menor número aparece primeiro')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Aparência')
                    ->schema([
                        Forms\Components\ColorPicker::make('title_color')
                            ->label('Cor do Título')
                            ->default('#1e40af'),
                        
                        Forms\Components\ColorPicker::make('background_color')
                            ->label('Cor de Fundo')
                            ->default('#ffffff'),
                        
                        Forms\Components\ColorPicker::make('text_color')
                            ->label('Cor do Texto')
                            ->default('#1f2937'),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Customização')
                    ->schema([
                        Forms\Components\Textarea::make('custom_css')
                            ->label('CSS Personalizado')
                            ->helperText('CSS adicional para este widget')
                            ->rows(4)
                            ->columnSpanFull(),
                        
                        Forms\Components\KeyValue::make('widget_settings')
                            ->label('Configurações do Widget')
                            ->helperText('Configurações específicas para este widget')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('widget_name')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'notices' => 'Recados',
                        'tags' => 'Tags',
                        'youtube' => 'Canal YouTube',
                        'polls' => 'Enquetes',
                        'lectures' => 'Palestras',
                        'hangouts' => 'Hangouts',
                        'books' => 'Livros Recomendados',
                        'downloads' => 'Downloads',
                        default => $state,
                    })
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Nome de Exibição')
                    ->default(fn ($record) => $record->getDisplayName())
                    ->description(fn ($record) => $record->display_name ? 'Personalizado' : 'Padrão')
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\ColorColumn::make('title_color')
                    ->label('Cor Título'),
                
                Tables\Columns\ColorColumn::make('background_color')
                    ->label('Cor Fundo'),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status'),
                
                Tables\Filters\SelectFilter::make('widget_name')
                    ->label('Tipo de Widget')
                    ->options([
                        'notices' => 'Recados',
                        'tags' => 'Tags',
                        'youtube' => 'Canal YouTube',
                        'polls' => 'Enquetes',
                        'lectures' => 'Palestras',
                        'hangouts' => 'Hangouts',
                        'books' => 'Livros Recomendados',
                        'downloads' => 'Downloads',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar')->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()->label('Excluir')->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Excluir')->icon('heroicon-o-trash'),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações do Widget')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Nome')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->columnSpanFull(),
                                
                                Infolists\Components\TextEntry::make('type')
                                    ->label('Tipo')
                                    ->badge()
                                    ->color(fn ($state) => match($state) {
                                        'banner' => 'primary',
                                        'notice' => 'warning',
                                        'lecture' => 'success',
                                        'post' => 'info',
                                        default => 'gray'
                                    }),
                                
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Ativo')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-mark')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                                
                                Infolists\Components\TextEntry::make('sort_order')
                                    ->label('Ordem')
                                    ->numeric()
                                    ->badge()
                                    ->color('gray'),
                                
                                Infolists\Components\TextEntry::make('max_items')
                                    ->label('Máximo de Itens')
                                    ->numeric()
                                    ->placeholder('Ilimitado'),
                            ]),
                    ]),
                
                Infolists\Components\Section::make('Configurações')
                    ->schema([
                        Infolists\Components\TextEntry::make('config')
                            ->label('Configuração JSON')
                            ->placeholder('Nenhuma configuração específica'),
                    ])
                    ->collapsible()
                    ->collapsed(),
                
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSidebarConfigs::route('/'),
            'create' => Pages\CreateSidebarConfig::route('/create'),
            'edit' => Pages\EditSidebarConfig::route('/{record}/edit'),
        ];
    }
}

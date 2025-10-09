<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SidebarConfigResource\Pages;
use App\Filament\Resources\SidebarConfigResource\RelationManagers;
use App\Models\SidebarConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                            ->label('Nome do Widget')
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
                            ->disabled(fn ($record) => $record?->exists),
                        
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
                    ->label('Widget')
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
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

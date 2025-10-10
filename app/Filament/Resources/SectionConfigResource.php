<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectionConfigResource\Pages;
use App\Models\SectionConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SectionConfigResource extends Resource
{
    protected static ?string $model = SectionConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Configurações de Seções';
    
    protected static ?string $modelLabel = 'Seção';
    
    protected static ?string $pluralModelLabel = 'Configurações de Seções';
    
    protected static ?string $navigationGroup = 'Configurações';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Seção')
                    ->schema([
                        Forms\Components\TextInput::make('section_key')
                            ->label('Chave da Seção')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Identificador único da seção (ex: featured_posts, news_mundial)')
                            ->placeholder('featured_posts'),
                            
                        Forms\Components\TextInput::make('section_name')
                            ->label('Nome da Seção')
                            ->required()
                            ->placeholder('Posts em Destaque'),
                            
                        Forms\Components\TextInput::make('section_icon')
                            ->label('Ícone FontAwesome')
                            ->placeholder('fas fa-star')
                            ->helperText('Ex: fas fa-star, fas fa-globe, fas fa-newspaper'),
                            
                        Forms\Components\Textarea::make('section_description')
                            ->label('Descrição')
                            ->rows(2)
                            ->placeholder('Descrição que aparece abaixo do título'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Configurações')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                            
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordem de Exibição')
                            ->numeric()
                            ->default(0)
                            ->helperText('Número menor = aparece primeiro'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('section_key')
                    ->label('Chave')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('section_name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('section_icon')
                    ->label('Ícone')
                    ->formatStateUsing(fn (string $state): string => $state ? $state : 'Nenhum'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Apenas ativos')
                    ->falseLabel('Apenas inativos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSectionConfigs::route('/'),
            'create' => Pages\CreateSectionConfig::route('/create'),
            'edit' => Pages\EditSectionConfig::route('/{record}/edit'),
        ];
    }
}

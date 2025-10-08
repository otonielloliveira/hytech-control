<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoMetaResource\Pages;
use App\Filament\Resources\SeoMetaResource\RelationManagers;
use App\Models\SeoMeta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeoMetaResource extends Resource
{
    protected static ?string $model = SeoMeta::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    
    protected static ?string $navigationLabel = 'SEO Meta';
    
    protected static ?string $modelLabel = 'Meta SEO';
    
    protected static ?string $pluralModelLabel = 'Metas SEO';
    
    protected static ?string $navigationGroup = 'Blog';
    
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('seoable_type')
                    ->required(),
                Forms\Components\TextInput::make('seoable_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('meta_title'),
                Forms\Components\Textarea::make('meta_description')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('meta_keywords')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('og_title'),
                Forms\Components\Textarea::make('og_description')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('og_image')
                    ->image(),
                Forms\Components\TextInput::make('og_type')
                    ->required(),
                Forms\Components\TextInput::make('twitter_card')
                    ->required(),
                Forms\Components\TextInput::make('twitter_title'),
                Forms\Components\Textarea::make('twitter_description')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('twitter_image')
                    ->image(),
                Forms\Components\TextInput::make('canonical_url'),
                Forms\Components\TextInput::make('robots')
                    ->required(),
                Forms\Components\Textarea::make('schema_markup')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seoable_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seoable_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('meta_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('og_title')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('og_image'),
                Tables\Columns\TextColumn::make('og_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('twitter_card')
                    ->searchable(),
                Tables\Columns\TextColumn::make('twitter_title')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('twitter_image'),
                Tables\Columns\TextColumn::make('canonical_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('robots')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSeoMetas::route('/'),
            'create' => Pages\CreateSeoMeta::route('/create'),
            'view' => Pages\ViewSeoMeta::route('/{record}'),
            'edit' => Pages\EditSeoMeta::route('/{record}/edit'),
        ];
    }
}

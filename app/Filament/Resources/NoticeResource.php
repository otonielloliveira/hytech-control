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
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->size(50),
                
                Tables\Columns\TextColumn::make('link_type')
                    ->label('Tipo de Link')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'none' => 'gray',
                        'internal' => 'info',
                        'external' => 'success',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Início')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Imediato'),
                
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fim')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Indefinido'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LectureResource\Pages;
use App\Filament\Resources\LectureResource\RelationManagers;
use App\Models\Lecture;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LectureResource extends Resource
{
    protected static ?string $model = Lecture::class;

    protected static ?string $navigationIcon = 'heroicon-o-microphone';
    
    protected static ?string $navigationLabel = 'Palestras';
    
    protected static ?string $modelLabel = 'Palestra';
    
    protected static ?string $pluralModelLabel = 'Palestras';
    
    protected static ?string $navigationGroup = 'Blog Sidebar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Palestra')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\RichEditor::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('speaker')
                            ->label('Palestrante')
                            ->maxLength(255),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagem')
                            ->image()
                            ->directory('lectures')
                            ->visibility('public'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Data e Local')
                    ->schema([
                        Forms\Components\DateTimePicker::make('date_time')
                            ->label('Data e Hora'),
                        
                        Forms\Components\TextInput::make('location')
                            ->label('Local')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('link_url')
                            ->label('Link para Mais Informações')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Configurações')
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
                
                Tables\Columns\TextColumn::make('speaker')
                    ->label('Palestrante')
                    ->searchable()
                    ->placeholder('Não informado'),
                
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagem')
                    ->size(50),
                
                Tables\Columns\TextColumn::make('date_time')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Não definida'),
                
                Tables\Columns\TextColumn::make('location')
                    ->label('Local')
                    ->searchable()
                    ->placeholder('Não informado'),
                
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            'index' => Pages\ListLectures::route('/'),
            'create' => Pages\CreateLecture::route('/create'),
            'edit' => Pages\EditLecture::route('/{record}/edit'),
        ];
    }
}

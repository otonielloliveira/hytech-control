<?php

namespace App\Filament\Resources\PollResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    protected static ?string $title = 'Opções de Votação';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('option_text')
                    ->label('Texto da Opção')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('priority')
                    ->label('Ordem')
                    ->numeric()
                    ->default(1)
                    ->helperText('Número menor = maior prioridade'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('option_text')
            ->columns([
                Tables\Columns\TextColumn::make('option_text')
                    ->label('Opção')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('votes_count')
                    ->label('Votos')
                    ->badge()
                    ->color('success'),
                    
                Tables\Columns\TextColumn::make('vote_percentage')
                    ->label('Percentual')
                    ->suffix('%')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('priority')
                    ->label('Ordem')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
            ])
            ->defaultSort('priority', 'asc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nova Opção'),
            ])
            ->actions([
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
}
<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $title = 'Comentários';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                Forms\Components\TextInput::make('author_name')
                    ->label('Nome do Autor')
                    ->visible(fn ($get) => !$get('user_id')),
                
                Forms\Components\TextInput::make('author_email')
                    ->label('E-mail do Autor')
                    ->email()
                    ->visible(fn ($get) => !$get('user_id')),
                
                Forms\Components\Textarea::make('content')
                    ->label('Comentário')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'approved' => 'Aprovado',
                        'spam' => 'Spam',
                        'trash' => 'Lixeira',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Autor')
                    ->getStateUsing(fn ($record) => $record->user ? $record->user->name : $record->author_name)
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('content')
                    ->label('Comentário')
                    ->limit(50)
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'spam' => 'danger',
                        'trash' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendente',
                        'approved' => 'Aprovado',
                        'spam' => 'Spam',
                        'trash' => 'Lixeira',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'approved' => 'Aprovado',
                        'spam' => 'Spam',
                        'trash' => 'Lixeira',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                Tables\Actions\Action::make('approve')
                    ->label('Aprovar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn ($record) => $record->update(['status' => 'approved']))
                    ->visible(fn ($record) => $record->status !== 'approved'),
                
                Tables\Actions\Action::make('spam')
                    ->label('Marcar como Spam')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->action(fn ($record) => $record->update(['status' => 'spam']))
                    ->visible(fn ($record) => $record->status !== 'spam')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Aprovar Selecionados')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 'approved'])),
                    
                    Tables\Actions\BulkAction::make('spam')
                        ->label('Marcar como Spam')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['status' => 'spam']))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
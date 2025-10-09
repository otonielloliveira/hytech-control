<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected static ?string $title = 'Criar Livro';

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Livro criado com sucesso!')
            ->body('O livro foi cadastrado e está disponível no sistema.')
            ->duration(5000)
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Ver listagem')
                    ->url($this->getResource()::getUrl('index'))
                    ->button(),
                \Filament\Notifications\Actions\Action::make('create_another')
                    ->label('Criar outro')
                    ->url($this->getResource()::getUrl('create'))
                    ->button(),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Criar Livro'),
            $this->getCreateAnotherFormAction()
                ->label('Criar e Adicionar Outro Livro'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}

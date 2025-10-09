<?php

namespace App\Filament\Resources\CommentResource\Pages;

use App\Filament\Resources\CommentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateComment extends CreateRecord
{
    protected static string $resource = CommentResource::class;

    protected static ?string $title = 'Criar Comentário';

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Comentário criado com sucesso!')
            ->body('O comentário foi cadastrado e está disponível no sistema.')
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
                ->label('Criar Comentário'),
            $this->getCreateAnotherFormAction()
                ->label('Criar e Adicionar Outro Comentário'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}

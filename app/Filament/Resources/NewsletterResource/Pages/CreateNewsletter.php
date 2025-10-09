<?php

namespace App\Filament\Resources\NewsletterResource\Pages;

use App\Filament\Resources\NewsletterResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsletter extends CreateRecord
{
    protected static string $resource = NewsletterResource::class;

    protected static ?string $title = 'Criar Newsletter';

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Newsletter criada com sucesso!')
            ->body('A newsletter foi cadastrada e está disponível no sistema.')
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
                ->label('Criar Newsletter'),
            $this->getCreateAnotherFormAction()
                ->label('Criar e Adicionar Outra Newsletter'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}

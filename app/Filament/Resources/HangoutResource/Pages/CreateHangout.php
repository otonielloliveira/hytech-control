<?php

namespace App\Filament\Resources\HangoutResource\Pages;

use App\Filament\Resources\HangoutResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateHangout extends CreateRecord
{
    protected static string $resource = HangoutResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Hangout criado com sucesso!')
            ->body('O hangout foi agendado e está disponível para os participantes.')
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

    public function getTitle(): string
    {
        return 'Criar Hangout';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Criar Hangout'),
            $this->getCreateAnotherFormAction()
                ->label('Criar e Adicionar Outro Hangout'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}

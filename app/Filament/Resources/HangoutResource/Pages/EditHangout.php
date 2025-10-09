<?php

namespace App\Filament\Resources\HangoutResource\Pages;

use App\Filament\Resources\HangoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditHangout extends EditRecord
{
    protected static string $resource = HangoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Excluir')
                ->modalHeading('Confirmar exclusão')
                ->modalDescription('Tem certeza que deseja excluir este hangout? Esta ação não pode ser desfeita.')
                ->modalSubmitActionLabel('Excluir')
                ->modalCancelActionLabel('Cancelar'),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Hangout atualizado!')
            ->body('As alterações foram salvas com sucesso.');
    }

    public function getTitle(): string
    {
        return 'Editar Hangout';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Salvar Alterações')
                ->icon('heroicon-m-check'),
            $this->getCancelFormAction()
                ->label('Cancelar')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}

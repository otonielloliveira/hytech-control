<?php

namespace App\Filament\Resources\DownloadResource\Pages;

use App\Filament\Resources\DownloadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditDownload extends EditRecord
{
    protected static string $resource = DownloadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Excluir Download')
                ->icon('heroicon-o-trash')
                ->modalHeading('Confirmar exclusão')
                ->modalDescription('Tem certeza que deseja excluir este download? Esta ação não pode ser desfeita.')
                ->modalSubmitActionLabel('Excluir')
                ->modalCancelActionLabel('Cancelar'),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Download atualizado com sucesso!')
            ->body('As informações do download foram salvas.')
            ->duration(5000)
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Ver listagem')
                    ->url($this->getResource()::getUrl('index'))
                    ->button(),
                \Filament\Notifications\Actions\Action::make('continue')
                    ->label('Continuar editando')
                    ->button()
                    ->close(),
                \Filament\Notifications\Actions\Action::make('view_property')
                    ->label('Visualizar propriedade')
                    ->url($this->getResource()::getUrl('view', ['record' => $this->record]))
                    ->button(),
            ]);
    }

    public function getTitle(): string
    {
        return 'Editar Download';
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

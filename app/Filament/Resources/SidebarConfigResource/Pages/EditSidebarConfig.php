<?php

namespace App\Filament\Resources\SidebarConfigResource\Pages;

use App\Filament\Resources\SidebarConfigResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSidebarConfig extends EditRecord
{
    protected static string $resource = SidebarConfigResource::class;

    protected static ?string $title = 'Editar Widget';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Excluir Widget')
                ->icon('heroicon-m-trash')
                ->requiresConfirmation()
                ->modalHeading('Excluir widget')
                ->modalDescription('Tem certeza que deseja excluir este widget? Esta ação não pode ser desfeita.')
                ->modalSubmitActionLabel('Sim, excluir')
                ->modalCancelActionLabel('Cancelar'),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Widget atualizado com sucesso!')
            ->body('As informações do widget foram salvas.')
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
            ]);
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

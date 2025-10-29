<?php

namespace App\Filament\Resources\LectureResource\Pages;

use App\Filament\Resources\LectureResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLecture extends EditRecord
{
    protected static string $resource = LectureResource::class;

    protected static ?string $title = 'Editar Palestra';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Excluir Palestra')
                ->icon('heroicon-m-trash')
                ->requiresConfirmation()
                ->modalHeading('Excluir palestra')
                ->modalDescription('Tem certeza que deseja excluir esta palestra? Esta ação não pode ser desfeita.')
                ->modalSubmitActionLabel('Sim, excluir')
                ->modalCancelActionLabel('Cancelar'),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Palestra atualizada com sucesso!')
            ->body('As informações da palestra foram salvas.')
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

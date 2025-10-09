<?php

namespace App\Filament\Resources\LectureResource\Pages;

use App\Filament\Resources\LectureResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateLecture extends CreateRecord
{
    protected static string $resource = LectureResource::class;

    protected static ?string $title = 'Criar Palestra';


    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Palestra criada com sucesso!')
            ->body('A palestra foi cadastrada e está disponível no sistema.')
            ->duration(5000)
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Ver listagem')
                    ->url($this->getResource()::getUrl('index'))
                    ->button(),
                \Filament\Notifications\Actions\Action::make('create_another')
                    ->label('Criar outra')
                    ->url($this->getResource()::getUrl('create'))
                    ->button(),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Criar Palestra'),
            $this->getCreateAnotherFormAction()
                ->label('Criar e Adicionar Outra Palestra'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}

<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected static ?string $title = 'Criar Categoria';

    protected function getCreatedNotification(): ?Notification  
    {
        return Notification::make()
            ->success()
            ->title('Categoria criada com sucesso!')
            ->body('A categoria foi cadastrada e está disponível no sistema.')
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
                ->label('Criar Categoria'),
            $this->getCreateAnotherFormAction()
                ->label('Criar e Adicionar Outra Categoria'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}

<?php

namespace App\Filament\Resources\SidebarConfigResource\Pages;

use App\Filament\Resources\SidebarConfigResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSidebarConfig extends CreateRecord
{
    protected static string $resource = SidebarConfigResource::class;

    protected static ?string $title = 'Criar Widget';

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Widget criado com sucesso!')
            ->body('O widget foi cadastrado e está disponível no sistema.')
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
        return 'Criar Widget';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Criar Widget'),
            $this->getCreateAnotherFormAction()
                ->label('Criar e Adicionar Outro Widget'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}

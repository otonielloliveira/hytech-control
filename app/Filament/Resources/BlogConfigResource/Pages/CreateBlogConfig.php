<?php

namespace App\Filament\Resources\BlogConfigResource\Pages;

use App\Filament\Resources\BlogConfigResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogConfig extends CreateRecord
{
    protected static string $resource = BlogConfigResource::class;

    protected static ?string $title = 'Criar Configuração do Blog';

    protected function getCreatedNotification(): ?Notification  
    {
        return Notification::make()
            ->success()
            ->title('Configuração do Blog criada com sucesso!')
            ->body('A configuração do blog foi cadastrada e está disponível no sistema.')
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
                ->label('Criar Configuração do Blog'),
            $this->getCreateAnotherFormAction()
                ->label('Criar e Adicionar Outra Configuração do Blog'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}

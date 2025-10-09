<?php

namespace App\Filament\Resources\SeoMetaResource\Pages;

use App\Filament\Resources\SeoMetaResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSeoMeta extends CreateRecord
{
    protected static string $resource = SeoMetaResource::class;

    protected static ?string $title = 'Criar SEO Meta';

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('SEO Meta criado com sucesso!')
            ->body('O SEO Meta foi cadastrado e está disponível no sistema.')
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
                ->label('Criar Seo Meta'),
            $this->getCreateAnotherFormAction()
                ->label('Criar e Adicionar Outro Seo Meta'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}

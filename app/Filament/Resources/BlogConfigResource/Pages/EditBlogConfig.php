<?php

namespace App\Filament\Resources\BlogConfigResource\Pages;

use App\Filament\Resources\BlogConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogConfig extends EditRecord
{
    protected static string $resource = BlogConfigResource::class;

    protected static ?string $title = 'Editar Configuração do Blog';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Configuração do blog atualizada com sucesso!')
            ->body('As informações da configuração do blog foram salvas.')
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
                ->icon('heroicon-o-x-mark'),
        ];
    }
}

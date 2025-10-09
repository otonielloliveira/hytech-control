<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected static ?string $title = 'Editar Categoria';

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Categoria atualizada com sucesso!')
            ->body('As informações da categoria foram salvas.')
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
        return 'Editar Categoria';
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

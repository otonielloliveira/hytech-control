<?php

namespace App\Filament\Resources\BannerResource\Pages;

use App\Filament\Resources\BannerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBanner extends EditRecord
{
    protected static string $resource = BannerResource::class;

    protected static ?string $title = 'Editar Banner Moderno';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('👁️ Visualizar Banner')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => route('blog.index'), shouldOpenInNewTab: true)
                ->tooltip('Abrir página principal para ver o banner'),
            
            Actions\Action::make('duplicate')
                ->label('📋 Duplicar')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $newBanner = $this->record->replicate();
                    $newBanner->title = $this->record->title . ' (Cópia)';
                    $newBanner->is_active = false;
                    $newBanner->save();
                    
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Banner duplicado!')
                        ->body('Uma cópia do banner foi criada.')
                        ->send();
                    
                    return redirect()->to($this->getResource()::getUrl('edit', ['record' => $newBanner]));
                }),
            
            Actions\DeleteAction::make()
                ->label('Excluir'),
        ];
    }

    protected function getSavedNotification(): ?\Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->success()
            ->title('Banner atualizado com sucesso!')
            ->body('As informações do banner foram salvas.')
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

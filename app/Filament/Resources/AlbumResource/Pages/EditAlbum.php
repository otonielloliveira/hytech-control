<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use App\Models\Photo;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAlbum extends EditRecord
{
    protected static string $resource = AlbumResource::class;
    
    protected array $bulkPhotos = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove bulk_photos do data principal para não tentar salvar no album
        $bulkPhotos = $data['bulk_photos'] ?? [];
        unset($data['bulk_photos']);
        
        // Armazena as fotos em lote para processar depois
        $this->bulkPhotos = $bulkPhotos;
        
        return $data;
    }
    
    protected function afterSave(): void
    {
        // Processa upload em lote se existir
        if (!empty($this->bulkPhotos)) {
            $order = $this->record->photos()->max('order') ?? 0;
            
            foreach ($this->bulkPhotos as $photoPath) {
                Photo::create([
                    'album_id' => $this->record->id,
                    'image_path' => $photoPath,
                    'order' => ++$order,
                    'is_featured' => false,
                ]);
            }
            
            // Atualiza o contador de fotos
            $this->record->update([
                'photo_count' => $this->record->photos()->count()
            ]);
        }
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Álbum atualizado com sucesso!')
            ->body('As informações do álbum foram salvas.')
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
                \Filament\Notifications\Actions\Action::make('view_album')
                    ->label('Visualizar Álbum')
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

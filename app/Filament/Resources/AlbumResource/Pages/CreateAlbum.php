<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use App\Models\Photo;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAlbum extends CreateRecord
{
    protected static string $resource = AlbumResource::class;
    
    protected array $bulkPhotos = [];

    protected static ?string $title = 'Criar Álbum';
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove bulk_photos do data principal para não tentar salvar no album
        $bulkPhotos = $data['bulk_photos'] ?? [];
        unset($data['bulk_photos']);
        
        // Armazena as fotos em lote para processar depois
        $this->bulkPhotos = $bulkPhotos;
        
        return $data;
    }
    
    protected function afterCreate(): void
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

      protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Álbum criado com sucesso!')
            ->body('O álbum foi cadastrado e está disponível no sistema.')
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
                ->label('Criar Álbum'),
            $this->getCreateAnotherFormAction()
                ->label('Criar e Adicionar Outro Álbum'),
            $this->getCancelFormAction()
                ->label('Cancelar'),
        ];
    }
}

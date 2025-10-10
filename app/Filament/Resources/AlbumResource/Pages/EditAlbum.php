<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Resources\AlbumResource;
use App\Models\Photo;
use Filament\Actions;
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
}

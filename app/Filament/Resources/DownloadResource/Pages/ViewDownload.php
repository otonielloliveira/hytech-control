<?php

namespace App\Filament\Resources\DownloadResource\Pages;

use App\Filament\Resources\DownloadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDownload extends ViewRecord
{
    protected static string $resource = DownloadResource::class;
    
    protected static ?string $title = 'Visualizar Download';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar Download')
                ->icon('heroicon-o-pencil'),
        ];
    }
}

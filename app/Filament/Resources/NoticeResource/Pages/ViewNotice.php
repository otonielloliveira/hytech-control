<?php

namespace App\Filament\Resources\NoticeResource\Pages;

use App\Filament\Resources\NoticeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNotice extends ViewRecord
{
    protected static string $resource = NoticeResource::class;
    
    protected static ?string $title = 'Visualizar Recado';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar Recado')
                ->icon('heroicon-o-pencil'),
        ];
    }
}

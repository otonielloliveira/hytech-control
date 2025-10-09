<?php

namespace App\Filament\Resources\LectureResource\Pages;

use App\Filament\Resources\LectureResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLecture extends ViewRecord
{
    protected static string $resource = LectureResource::class;
    
    protected static ?string $title = 'Visualizar Palestra';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar Palestra')
                ->icon('heroicon-o-pencil'),
        ];
    }
}

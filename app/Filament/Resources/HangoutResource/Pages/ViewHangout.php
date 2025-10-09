<?php

namespace App\Filament\Resources\HangoutResource\Pages;

use App\Filament\Resources\HangoutResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHangout extends ViewRecord
{
    protected static string $resource = HangoutResource::class;
    
    protected static ?string $title = 'Visualizar Hangout';
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar Hangout')
                ->icon('heroicon-o-pencil'),
        ];
    }
}

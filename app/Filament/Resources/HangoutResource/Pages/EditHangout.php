<?php

namespace App\Filament\Resources\HangoutResource\Pages;

use App\Filament\Resources\HangoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHangout extends EditRecord
{
    protected static string $resource = HangoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

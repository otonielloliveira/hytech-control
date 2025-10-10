<?php

namespace App\Filament\Resources\SectionConfigResource\Pages;

use App\Filament\Resources\SectionConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSectionConfig extends EditRecord
{
    protected static string $resource = SectionConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

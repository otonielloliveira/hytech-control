<?php

namespace App\Filament\Resources\BlogConfigResource\Pages;

use App\Filament\Resources\BlogConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogConfig extends EditRecord
{
    protected static string $resource = BlogConfigResource::class;

    protected static ?string $title = 'Editar Configuração do Blog';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\BlogConfigResource\Pages;

use App\Filament\Resources\BlogConfigResource;
use App\Models\BlogConfig;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBlogConfigs extends ManageRecords
{
    protected static string $resource = BlogConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    // Garantir que apenas uma configuração existe
                    if (BlogConfig::count() > 0) {
                        $this->halt();
                    }
                    return $data;
                })
                ->visible(fn () => BlogConfig::count() === 0),
        ];
    }

    public function mount(): void
    {
        parent::mount();
        
        // Se não existe configuração, criar uma padrão
        if (BlogConfig::count() === 0) {
            BlogConfig::current();
        }
    }
}
<?php

namespace App\Filament\Resources\BlogConfigResource\Pages;

use App\Filament\Resources\BlogConfigResource;
use App\Models\BlogConfig;
use App\Models\SidebarConfig;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;

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

    protected function afterSave(): void
    {
        $config = $this->record;
        
        // Sincronizar widget do YouTube
        $this->syncYoutubeWidget($config);
    }

    protected function syncYoutubeWidget(BlogConfig $config): void
    {
        // Buscar ou criar configuração do widget do YouTube
        $youtubeWidget = SidebarConfig::firstOrCreate(
            ['widget_name' => 'youtube'],
            [
                'is_active' => false,
                'sort_order' => 50,
                'title_color' => $config->default_widget_title_color ?? '#1e40af',
                'background_color' => $config->default_widget_background_color ?? '#ffffff',
                'text_color' => $config->default_widget_text_color ?? '#1f2937',
            ]
        );

        // Atualizar status do widget baseado na configuração do blog
        if ($youtubeWidget->is_active != $config->show_youtube_widget) {
            $youtubeWidget->update([
                'is_active' => $config->show_youtube_widget,
            ]);

            // Notificar o usuário
            if ($config->show_youtube_widget) {
                Notification::make()
                    ->success()
                    ->title('Widget do YouTube ativado!')
                    ->body('O widget do YouTube foi ativado na sidebar do site.')
                    ->send();
            } else {
                Notification::make()
                    ->warning()
                    ->title('Widget do YouTube desativado!')
                    ->body('O widget do YouTube foi removido da sidebar do site.')
                    ->send();
            }
        }
    }
}
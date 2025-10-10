<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\Client;
use App\Models\Album;
use App\Models\Video;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuickActionsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Estatísticas de hoje
        $todayPosts = Post::whereDate('created_at', today())->count();
        $todayClients = Client::whereDate('created_at', today())->count();
        $pendingPosts = Post::where('status', 'draft')->count();
        $recentComments = \App\Models\Comment::whereDate('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Posts de Hoje', $todayPosts)
                ->description('Novos posts criados')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success')
                ->extraAttributes([
                    'style' => 'cursor: pointer;',
                    'onclick' => 'window.location.href="/admin/posts"'
                ]),
                
            Stat::make('Novos Clientes', $todayClients)
                ->description('Registros de hoje')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info')
                ->extraAttributes([
                    'style' => 'cursor: pointer;',
                    'onclick' => 'window.location.href="/admin/clients"'
                ]),
                
            Stat::make('Posts Pendentes', $pendingPosts)
                ->description('Aguardando publicação')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingPosts > 0 ? 'warning' : 'success')
                ->extraAttributes([
                    'style' => 'cursor: pointer;',
                    'onclick' => 'window.location.href="/admin/posts?tableFilters[status][value]=draft"'
                ]),
                
            Stat::make('Comentários Recentes', $recentComments)
                ->description('Últimos 7 dias')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary')
                ->extraAttributes([
                    'style' => 'cursor: pointer;',
                    'onclick' => 'window.location.href="/admin/comments"'
                ]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\Album;
use App\Models\Video;
use App\Models\Client;
use App\Models\Photo;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPosts = Post::count();
        $publishedPosts = Post::where('status', 'published')->count();
        $totalAlbums = Album::count();
        $totalPhotos = Photo::count();
        $totalVideos = Video::count();
        $totalClients = Client::count();
        
        $recentPosts = Post::where('created_at', '>=', now()->subDays(30))->count();
        $recentClients = Client::where('created_at', '>=', now()->subDays(30))->count();

        return [
            Stat::make('Total de Posts', $totalPosts)
                ->description($publishedPosts . ' publicados')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($publishedPosts > 0 ? 'success' : 'warning')
                ->chart([7, 2, 10, 3, 15, 4, 17, 12, 8, 5, 6, 8]),

            Stat::make('Álbuns de Fotos', $totalAlbums)
                ->description($totalPhotos . ' fotos no total')
                ->descriptionIcon('heroicon-m-photo')
                ->color('info')
                ->chart([3, 8, 5, 12, 7, 9, 4, 6, 11, 8, 3, 7]),

            Stat::make('Vídeos', $totalVideos)
                ->description('Biblioteca multimídia')
                ->descriptionIcon('heroicon-m-video-camera')
                ->color('warning')
                ->chart([2, 5, 8, 3, 9, 6, 12, 4, 7, 10, 5, 8]),

            Stat::make('Clientes Cadastrados', $totalClients)
                ->description($recentClients . ' novos este mês')
                ->descriptionIcon('heroicon-m-users')
                ->color($recentClients > 0 ? 'success' : 'gray')
                ->chart([1, 3, 2, 5, 4, 8, 6, 9, 7, 12, 10, 15]),
        ];
    }
}

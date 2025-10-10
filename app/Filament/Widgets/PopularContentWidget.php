<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\Album;
use App\Models\Video;
use Filament\Widgets\ChartWidget;

class PopularContentWidget extends ChartWidget
{
    protected static ?string $heading = 'Conteúdo Mais Popular (Últimos 30 dias)';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $popularPosts = Post::with('comments')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->get()
            ->sortByDesc(function ($post) {
                return $post->comments->count();
            })
            ->take(5);
            
        $popularAlbums = Album::with('photos')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->get()
            ->sortByDesc(function ($album) {
                return $album->photos->count();
            })
            ->take(5);

        // Se não houver conteúdo suficiente, usar dados dummy
        if ($popularPosts->isEmpty() && $popularAlbums->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'Comentários em Posts',
                        'data' => [15, 12, 8, 6, 4],
                        'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                        'borderColor' => 'rgb(59, 130, 246)',
                        'borderWidth' => 2,
                    ],
                    [
                        'label' => 'Fotos em Álbuns',
                        'data' => [25, 18, 15, 10, 8],
                        'backgroundColor' => 'rgba(168, 85, 247, 0.7)',
                        'borderColor' => 'rgb(168, 85, 247)',
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => ['Conteúdo A', 'Conteúdo B', 'Conteúdo C', 'Conteúdo D', 'Conteúdo E'],
            ];
        }

        $postLabels = $popularPosts->pluck('title')->map(function ($title) {
            return strlen($title) > 20 ? substr($title, 0, 20) . '...' : $title;
        })->toArray();
        
        $postComments = $popularPosts->map(function ($post) {
            return $post->comments->count();
        })->toArray();
        
        $albumLabels = $popularAlbums->pluck('title')->map(function ($title) {
            return strlen($title) > 20 ? substr($title, 0, 20) . '...' : $title;
        })->toArray();
        
        $albumPhotos = $popularAlbums->map(function ($album) {
            return $album->photos->count();
        })->toArray();

        // Combinar labels e garantir que temos 5 itens
        $allLabels = array_merge($postLabels, $albumLabels);
        $allLabels = array_slice(array_pad($allLabels, 5, 'Sem dados'), 0, 5);
        
        // Combinar dados
        $postData = array_slice(array_pad($postComments, 5, 0), 0, 5);
        $albumData = array_slice(array_pad($albumPhotos, 5, 0), 0, 5);

        return [
            'datasets' => [
                [
                    'label' => 'Comentários em Posts',
                    'data' => $postData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Fotos em Álbuns',
                    'data' => $albumData,
                    'backgroundColor' => 'rgba(168, 85, 247, 0.7)',
                    'borderColor' => 'rgb(168, 85, 247)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $allLabels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

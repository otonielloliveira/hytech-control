<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\Client;
use App\Models\Album;
use App\Models\Video;
use Filament\Widgets\ChartWidget;

class RecentActivityWidget extends ChartWidget
{
    protected static ?string $heading = 'Atividades dos Últimos 7 Dias';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = '2';

    protected function getData(): array
    {
        $data = $this->getActivityData();
        
        return [
            'datasets' => [
                [
                    'label' => 'Posts Criados',
                    'data' => $data['posts'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Clientes Registrados',
                    'data' => $data['clients'],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Álbuns Criados',
                    'data' => $data['albums'],
                    'backgroundColor' => 'rgba(168, 85, 247, 0.7)',
                    'borderColor' => 'rgb(168, 85, 247)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Vídeos Adicionados',
                    'data' => $data['videos'],
                    'backgroundColor' => 'rgba(245, 158, 11, 0.7)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    
    private function getActivityData(): array
    {
        $days = collect(range(6, 0))->map(function ($daysBack) {
            $date = now()->subDays($daysBack);
            return [
                'label' => $date->format('d/m'),
                'date' => $date->toDateString(),
            ];
        });

        $labels = $days->pluck('label')->toArray();
        
        $posts = $days->map(function ($day) {
            return Post::whereDate('created_at', $day['date'])->count();
        })->toArray();
        
        $clients = $days->map(function ($day) {
            return Client::whereDate('created_at', $day['date'])->count();
        })->toArray();
        
        $albums = $days->map(function ($day) {
            return Album::whereDate('created_at', $day['date'])->count();
        })->toArray();
        
        $videos = $days->map(function ($day) {
            return Video::whereDate('created_at', $day['date'])->count();
        })->toArray();

        return [
            'labels' => $labels,
            'posts' => $posts,
            'clients' => $clients,
            'albums' => $albums,
            'videos' => $videos,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\BlogConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class YouTubeService
{
    protected $apiKey;
    protected $baseUrl = 'https://www.googleapis.com/youtube/v3';
    
    public function __construct()
    {
        $config = BlogConfig::current();
        $this->apiKey = $config->youtube_api_key;
    }

    /**
     * Extrair Channel ID da URL do YouTube
     */
    public function extractChannelId($url): ?string
    {
        // Patterns para diferentes formatos de URL do YouTube
        $patterns = [
            // youtube.com/channel/UCxxxxx
            '/youtube\.com\/channel\/([a-zA-Z0-9_-]+)/',
            // youtube.com/c/nomecanal
            '/youtube\.com\/c\/([a-zA-Z0-9_-]+)/',
            // youtube.com/@nomecanal
            '/youtube\.com\/@([a-zA-Z0-9_-]+)/',
            // youtube.com/user/nomecanal
            '/youtube\.com\/user\/([a-zA-Z0-9_-]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                $identifier = $matches[1];
                
                // Se já é um channel ID (começa com UC)
                if (str_starts_with($identifier, 'UC')) {
                    return $identifier;
                }
                
                // Caso contrário, buscar o channel ID pelo username/handle
                return $this->getChannelIdByUsername($identifier);
            }
        }

        return null;
    }

    /**
     * Buscar Channel ID pelo username ou handle
     */
    protected function getChannelIdByUsername($username): ?string
    {
        if (!$this->apiKey) {
            return null;
        }

        try {
            // Primeiro tentar como username
            $response = Http::get("{$this->baseUrl}/channels", [
                'part' => 'id',
                'forUsername' => $username,
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['items'])) {
                    return $data['items'][0]['id'];
                }
            }

            // Se não encontrou, tentar buscar como handle (nome customizado)
            $response = Http::get("{$this->baseUrl}/search", [
                'part' => 'snippet',
                'q' => $username,
                'type' => 'channel',
                'maxResults' => 1,
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['items'])) {
                    return $data['items'][0]['snippet']['channelId'];
                }
            }

        } catch (\Exception $e) {
            Log::error('Erro ao buscar Channel ID: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Buscar dados do canal
     */
    public function getChannelData($channelId): ?array
    {
        if (!$this->apiKey || !$channelId) {
            return null;
        }

        $cacheKey = "youtube_channel_data_{$channelId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($channelId) {
            try {
                $response = Http::get("{$this->baseUrl}/channels", [
                    'part' => 'snippet,statistics,brandingSettings',
                    'id' => $channelId,
                    'key' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (!empty($data['items'])) {
                        $channel = $data['items'][0];
                        
                        return [
                            'id' => $channel['id'],
                            'title' => $channel['snippet']['title'],
                            'description' => $channel['snippet']['description'],
                            'thumbnail' => $channel['snippet']['thumbnails']['high']['url'] ?? null,
                            'subscriber_count' => $this->formatNumber($channel['statistics']['subscriberCount'] ?? 0),
                            'video_count' => $this->formatNumber($channel['statistics']['videoCount'] ?? 0),
                            'view_count' => $this->formatNumber($channel['statistics']['viewCount'] ?? 0),
                            'subscriber_count_raw' => $channel['statistics']['subscriberCount'] ?? 0,
                            'video_count_raw' => $channel['statistics']['videoCount'] ?? 0,
                            'view_count_raw' => $channel['statistics']['viewCount'] ?? 0,
                            'custom_url' => $channel['snippet']['customUrl'] ?? null,
                            'published_at' => $channel['snippet']['publishedAt'] ?? null,
                            'last_updated' => now()->toISOString(),
                        ];
                    }
                }

            } catch (\Exception $e) {
                Log::error('Erro ao buscar dados do canal YouTube: ' . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Buscar vídeos recentes do canal
     */
    public function getChannelVideos($channelId, $maxResults = 5): array
    {
        if (!$this->apiKey || !$channelId) {
            return [];
        }

        $cacheKey = "youtube_channel_videos_{$channelId}_{$maxResults}";
        
        return Cache::remember($cacheKey, 1800, function () use ($channelId, $maxResults) {
            try {
                $response = Http::get("{$this->baseUrl}/search", [
                    'part' => 'snippet',
                    'channelId' => $channelId,
                    'type' => 'video',
                    'order' => 'date',
                    'maxResults' => $maxResults,
                    'key' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    $videos = [];
                    foreach ($data['items'] ?? [] as $item) {
                        $videos[] = [
                            'id' => $item['id']['videoId'],
                            'title' => $item['snippet']['title'],
                            'description' => $item['snippet']['description'],
                            'thumbnail' => $item['snippet']['thumbnails']['medium']['url'],
                            'published_at' => $item['snippet']['publishedAt'],
                            'url' => "https://www.youtube.com/watch?v={$item['id']['videoId']}"
                        ];
                    }
                    
                    return $videos;
                }

            } catch (\Exception $e) {
                Log::error('Erro ao buscar vídeos do canal YouTube: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Atualizar dados do canal na configuração
     */
    public function updateChannelData(): bool
    {
        $config = BlogConfig::current();
        
        if (!$config->youtube_channel_url) {
            return false;
        }

        // Extrair ou obter Channel ID
        $channelId = $config->youtube_channel_id;
        if (!$channelId) {
            $channelId = $this->extractChannelId($config->youtube_channel_url);
            if ($channelId) {
                $config->update(['youtube_channel_id' => $channelId]);
            }
        }

        if (!$channelId) {
            return false;
        }

        // Buscar dados atuais
        $channelData = $this->getChannelData($channelId);
        
        if ($channelData) {
            $config->update([
                'youtube_channel_data' => $channelData,
                'youtube_data_last_update' => now(),
                'youtube_channel_name' => $channelData['title'] ?? $config->youtube_channel_name
            ]);

            // Limpar cache
            Cache::forget("youtube_channel_data_{$channelId}");
            
            return true;
        }

        return false;
    }

    /**
     * Formatar números para exibição
     */
    protected function formatNumber($number): string
    {
        if ($number >= 1000000) {
            return number_format($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 1) . 'K';
        }
        
        return number_format($number);
    }

    /**
     * Verificar se a API key é válida
     */
    public function validateApiKey(): bool
    {
        if (!$this->apiKey) {
            return false;
        }

        try {
            $response = Http::get("{$this->baseUrl}/search", [
                'part' => 'snippet',
                'q' => 'test',
                'type' => 'channel',
                'maxResults' => 1,
                'key' => $this->apiKey
            ]);

            return $response->successful();

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obter dados do canal da configuração (com fallback)
     */
    public function getConfigChannelData(): array
    {
        $config = BlogConfig::current();
        
        // Se tem dados armazenados e são recentes (menos de 6 horas)
        if ($config->youtube_channel_data && 
            $config->youtube_data_last_update && 
            $config->youtube_data_last_update->diffInHours() < 6) {
            return $config->youtube_channel_data;
        }

        // Tentar atualizar dados
        if ($this->updateChannelData()) {
            return $config->fresh()->youtube_channel_data ?? [];
        }

        // Fallback para dados antigos ou padrão
        return $config->youtube_channel_data ?? [
            'title' => $config->youtube_channel_name ?? 'Nosso Canal',
            'subscriber_count' => '0',
            'video_count' => '0',
            'view_count' => '0',
        ];
    }
}
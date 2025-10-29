<?php

namespace App\Observers;

use App\Models\Video;

class VideoObserver
{
    /**
     * Handle the Video "creating" event.
     */
    public function creating(Video $video): void
    {
        $this->handleVideoUrlChange($video);
    }

    /**
     * Handle the Video "updating" event.
     */
    public function updating(Video $video): void
    {
        // Verificar se a URL do vídeo mudou
        if ($video->isDirty('video_url')) {
            $this->handleVideoUrlChange($video);
        }
    }

    /**
     * Processar mudanças na URL do vídeo
     */
    private function handleVideoUrlChange(Video $video): void
    {
        if ($video->video_url) {
            // Extrair ID e plataforma
            $video->extractVideoId();
            
            // Se não tem duração, tentar buscar automaticamente
            if (!$video->duration) {
                if ($video->video_platform === 'youtube') {
                    $video->fetchYoutubeDuration();
                } elseif ($video->video_platform === 'vimeo') {
                    $video->fetchVimeoDuration();
                }
            }
        }
    }
}
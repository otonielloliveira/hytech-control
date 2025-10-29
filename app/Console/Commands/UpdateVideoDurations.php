<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;

class UpdateVideoDurations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:update-durations {--force : Atualizar todas as durações, mesmo as que já existem}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza as durações dos vídeos do YouTube e Vimeo automaticamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando atualização das durações dos vídeos...');

        $query = Video::query()
            ->whereNotNull('video_id')
            ->whereIn('video_platform', ['youtube', 'vimeo']);

        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('duration')
                  ->orWhere('duration', '');
            });
        }

        $videos = $query->get();
        
        if ($videos->isEmpty()) {
            $this->info('Nenhum vídeo encontrado para atualizar.');
            return 0;
        }

        $this->info("Encontrados {$videos->count()} vídeos para atualizar.");

        $bar = $this->output->createProgressBar($videos->count());
        $bar->start();

        $updated = 0;
        $errors = 0;

        foreach ($videos as $video) {
            try {
                $originalDuration = $video->duration;

                if ($video->video_platform === 'youtube') {
                    $video->fetchYoutubeDuration();
                } elseif ($video->video_platform === 'vimeo') {
                    $video->fetchVimeoDuration();
                }

                if ($video->duration && $video->duration !== $originalDuration) {
                    $video->save();
                    $updated++;
                    $this->newLine();
                    $this->line("✓ {$video->title}: {$video->duration}");
                }

                // Delay para evitar rate limiting
                usleep(500000); // 0.5 segundos

            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("✗ Erro ao processar {$video->title}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info("Processamento concluído!");
        $this->info("✓ Vídeos atualizados: {$updated}");
        
        if ($errors > 0) {
            $this->warn("✗ Erros encontrados: {$errors}");
        }

        return 0;
    }
}

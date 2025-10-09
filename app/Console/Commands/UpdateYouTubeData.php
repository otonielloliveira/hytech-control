<?php

namespace App\Console\Commands;

use App\Services\YouTubeService;
use App\Models\BlogConfig;
use Illuminate\Console\Command;

class UpdateYouTubeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:update-data {--force : Forçar atualização mesmo se os dados forem recentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza os dados do canal do YouTube via API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando atualização dos dados do YouTube...');
        
        $config = BlogConfig::current();
        
        if (!$config->show_youtube_widget) {
            $this->warn('Widget do YouTube está desabilitado na configuração.');
            return Command::FAILURE;
        }
        
        if (!$config->youtube_channel_url) {
            $this->error('URL do canal do YouTube não configurada.');
            return Command::FAILURE;
        }
        
        if (!$config->youtube_api_key) {
            $this->error('API Key do YouTube não configurada.');
            $this->line('Configure em: Admin > Configurações do Blog > Sidebar > YouTube Integration');
            return Command::FAILURE;
        }
        
        // Verificar se precisa atualizar
        if (!$this->option('force') && 
            $config->youtube_data_last_update && 
            $config->youtube_data_last_update->diffInHours() < 6) {
            
            $this->info('✅ Dados ainda são recentes (última atualização: ' . 
                       $config->youtube_data_last_update->diffForHumans() . ')');
            $this->line('Use --force para forçar atualização.');
            return Command::SUCCESS;
        }
        
        $youtubeService = new YouTubeService();
        
        // Validar API Key
        $this->info('🔑 Validando API Key...');
        if (!$youtubeService->validateApiKey()) {
            $this->error('❌ API Key inválida ou YouTube Data API v3 não habilitado.');
            return Command::FAILURE;
        }
        $this->info('✅ API Key válida');
        
        // Extrair Channel ID se necessário
        if (!$config->youtube_channel_id) {
            $this->info('🔍 Extraindo Channel ID da URL...');
            $channelId = $youtubeService->extractChannelId($config->youtube_channel_url);
            
            if (!$channelId) {
                $this->error('❌ Não foi possível extrair o Channel ID da URL fornecida.');
                return Command::FAILURE;
            }
            
            $config->update(['youtube_channel_id' => $channelId]);
            $this->info('✅ Channel ID detectado: ' . $channelId);
        }
        
        // Buscar dados do canal
        $this->info('📊 Buscando dados do canal...');
        $channelData = $youtubeService->getChannelData($config->youtube_channel_id);
        
        if (!$channelData) {
            $this->error('❌ Não foi possível buscar os dados do canal.');
            return Command::FAILURE;
        }
        
        // Atualizar configuração
        $config->update([
            'youtube_channel_data' => $channelData,
            'youtube_data_last_update' => now(),
            'youtube_channel_name' => $channelData['title']
        ]);
        
        $this->info('✅ Dados atualizados com sucesso!');
        $this->newLine();
        
        // Exibir estatísticas
        $this->line('📈 <comment>Estatísticas do Canal:</comment>');
        $this->line('   Nome: ' . $channelData['title']);
        $this->line('   Inscritos: ' . $channelData['subscriber_count']);
        $this->line('   Vídeos: ' . $channelData['video_count']);
        $this->line('   Visualizações: ' . $channelData['view_count']);
        
        if (isset($channelData['custom_url'])) {
            $this->line('   URL Personalizada: ' . $channelData['custom_url']);
        }
        
        $this->newLine();
        $this->info('🎉 Atualização concluída!');
        
        return Command::SUCCESS;
    }
}

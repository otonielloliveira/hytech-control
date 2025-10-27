<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando processo completo de seeding...');
        
        $this->call([
            // 1. Usuários e configurações básicas
            AdminUserSeeder::class,
            
            // 2. Configurações de pagamento
            PaymentGatewaySeeder::class,
            
            // 3. Configurações de seções do site
            SectionConfigSeeder::class,
            
            // 4. Widgets da sidebar
            SidebarWidgetsSeeder::class,
            
            // 5. Conteúdo do blog (categorias, posts, banners)
            BlogSeeder::class,
            
            // 6. Cursos e materiais educacionais
            CourseSeeder::class,
            
            // 7. Vídeos e tutoriais
            VideoSeeder::class,
            
            // 8. Álbuns e fotos
            AlbumSeeder::class,
            
            // 9. Loja (produtos, métodos de pagamento, frete)
            StoreSeeder::class,
        ]);
        
        $this->command->info('');
        $this->command->info('✅ Seeding completo concluído com sucesso!');
        $this->command->info('📊 Ambiente totalmente preparado para testes');
        $this->command->info('');
        $this->command->info('� Usuário Admin criado:');
        $this->command->info('   Email: admin@hytech.com');
        $this->command->info('   Senha: password');
        $this->command->info('');
        $this->command->info('💳 Gateway de pagamento ASAAS ativo');
        $this->command->info('📝 Blog com categorias, posts e banners');
        $this->command->info('🎓 Cursos de exemplo criados');
        $this->command->info('🎥 Vídeos e tutoriais disponíveis');
        $this->command->info('� Álbuns de fotos criados');
        $this->command->info('�🛒 Loja com produtos de exemplo');
        $this->command->info('⚙️  Configurações de seções e sidebar');
    }
}

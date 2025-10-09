# TODO - Sistema de Sidebar Personalizada para Blog

## 📋 Visão Geral
Criar um sistema completo de widgets laterais personalizáveis para o blog, com gerenciamento via painel administrativo.

## 🎯 Funcionalidades a Implementar

### 1. 📢 SISTEMA DE RECADOS
- [ ] **Modelo `Notice`**
  - [ ] Campos: title, content, image, link_type (internal/external), link_url, internal_route, priority, is_active, start_date, end_date
  - [ ] Migration para tabela `blog_notices`
  - [ ] Relacionamentos e validações

- [ ] **Admin Interface (Filament)**
  - [ ] Resource para gerenciar recados
  - [ ] Upload de imagens
  - [ ] Seletor de tipo de link (interno/externo)
  - [ ] Campo de prioridade para ordenação
  - [ ] Status ativo/inativo
  - [ ] Datas de início e fim de exibição

- [ ] **Frontend**
  - [ ] Widget "Recados" na sidebar
  - [ ] Exibir até 4 recados ativos
  - [ ] Ordenação por prioridade
  - [ ] Links internos e externos funcionais

### 2. 🎤 SISTEMA DE PALESTRAS
- [ ] **Modelo `Lecture`**
  - [ ] Campos: title, description, image, speaker, date_time, location, link_url, is_active, priority
  - [ ] Migration para tabela `blog_lectures`
  
- [ ] **Admin Interface**
  - [ ] Resource para palestras
  - [ ] Campos de data/hora
  - [ ] Upload de imagens
  - [ ] Gerenciamento de palestrante
  
- [ ] **Frontend**
  - [ ] Widget "Palestras" na sidebar
  - [ ] Exibição das próximas palestras
  - [ ] Links para mais informações

### 3. 🏷️ SISTEMA DE TAGS MELHORADO
- [ ] **Funcionalidades**
  - [ ] Widget de tags na sidebar
  - [ ] Contagem de posts por tag
  - [ ] Links funcionais para filtrar posts
  - [ ] Limite de tags exibidas
  
- [ ] **Admin Interface**
  - [ ] Configuração de quantas tags exibir
  - [ ] Ordenação das tags (mais usadas, alfabética)

### 4. 📊 SISTEMA DE ENQUETES
- [ ] **Modelo `Poll`**
  - [ ] Campos: title, description, is_active, start_date, end_date, allow_multiple_votes
  - [ ] Migration para tabela `blog_polls`

- [ ] **Modelo `PollOption`**
  - [ ] Campos: poll_id, option_text, votes_count
  - [ ] Migration para tabela `blog_poll_options`

- [ ] **Modelo `PollVote`**
  - [ ] Campos: poll_id, option_id, ip_address, user_id, voted_at
  - [ ] Migration para tabela `blog_poll_votes`
  - [ ] Controle de votos únicos por IP/usuário

- [ ] **Admin Interface**
  - [ ] Resource para enquetes
  - [ ] Gerenciamento de opções de voto
  - [ ] Visualização de resultados
  - [ ] Configuração de datas ativas

- [ ] **Frontend**
  - [ ] Widget de enquete ativa
  - [ ] Sistema de votação AJAX
  - [ ] Exibição de resultados em tempo real
  - [ ] Botão "Votar" e "Resultado"

### 5. 📺 INTEGRAÇÃO COM YOUTUBE
- [ ] **Configuração do Blog**
  - [ ] Campo `youtube_channel_url` na tabela `blog_configs`
  - [ ] Campo `youtube_channel_name`
  - [ ] Campo `show_youtube_widget` (boolean)

- [ ] **Admin Interface**
  - [ ] Campos no BlogConfigResource para YouTube
  - [ ] Preview do canal

- [ ] **Frontend**
  - [ ] Widget do canal do YouTube
  - [ ] Exibição de estatísticas (se possível via API)
  - [ ] Link para o canal

### 6. 📅 SISTEMA DE HANGOUTS/EVENTOS
- [ ] **Modelo `Hangout`**
  - [ ] Campos: title, description, external_link, start_date, end_date, is_active
  - [ ] Migration para tabela `blog_hangouts`
  - [ ] Scope para eventos ativos (data atual entre start_date e end_date)

- [ ] **Admin Interface**
  - [ ] Resource para hangouts
  - [ ] Validação de datas
  - [ ] Status automático baseado em datas

- [ ] **Frontend**
  - [ ] Widget de hangouts ativos
  - [ ] Auto-remoção após data de fim

### 7. 📚 SISTEMA DE LIVROS/DOWNLOADS
- [ ] **Modelo `BookRecommendation`**
  - [ ] Campos: title, description, image, link_type, link_url, internal_route, is_active, priority, visibility (public/logged_users)
  - [ ] Migration para tabela `blog_book_recommendations`

- [ ] **Admin Interface**
  - [ ] Resource para livros
  - [ ] Upload de capas
  - [ ] Configuração de visibilidade
  - [ ] Links internos/externos

- [ ] **Frontend**
  - [ ] Widget de livros recomendados
  - [ ] Controle de visibilidade por login
  - [ ] Download/links funcionais

### 8. 📁 SISTEMA DE DOWNLOADS
- [ ] **Modelo `Download`**
  - [ ] Campos: title, description, file_path, file_size, download_count, visibility, is_active, category
  - [ ] Migration para tabela `blog_downloads`

- [ ] **Admin Interface**
  - [ ] Resource para downloads
  - [ ] Upload de arquivos
  - [ ] Configuração de visibilidade
  - [ ] Categorização

- [ ] **Frontend**
  - [ ] Widget de downloads
  - [ ] Controle de acesso por login
  - [ ] Contador de downloads
  - [ ] Links diretos para download

### 9. ⚙️ SISTEMA DE CONFIGURAÇÃO DE WIDGETS
- [ ] **Modelo `SidebarConfig`**
  - [ ] Campos: widget_name, is_active, sort_order, title_color, background_color, text_color, custom_css
  - [ ] Migration para tabela `blog_sidebar_configs`

- [ ] **Admin Interface**
  - [ ] Resource para configuração da sidebar
  - [ ] Color pickers para cores
  - [ ] Drag & drop para ordenação
  - [ ] Preview das cores
  - [ ] Campo de CSS customizado

- [ ] **Frontend**
  - [ ] Sistema dinâmico de widgets
  - [ ] Aplicação de cores personalizadas
  - [ ] Ordenação configurável
  - [ ] CSS customizado por widget

### 10. 🎨 MELHORIAS NA CONFIGURAÇÃO DO BLOG
- [ ] **Campos Adicionais**
  - [ ] Cores dos títulos dos blocos
  - [ ] Cores de fundo dos widgets
  - [ ] Configurações globais de sidebar
  - [ ] Layout preferences

- [ ] **Admin Interface**
  - [ ] Seção dedicada para customização visual
  - [ ] Preview em tempo real
  - [ ] Temas pré-definidos
  - [ ] Export/import de configurações

## 🗂️ Estrutura de Arquivos a Criar

### Models
- [ ] `app/Models/Notice.php`
- [ ] `app/Models/Lecture.php`
- [ ] `app/Models/Poll.php`
- [ ] `app/Models/PollOption.php`
- [ ] `app/Models/PollVote.php`
- [ ] `app/Models/Hangout.php`
- [ ] `app/Models/BookRecommendation.php`
- [ ] `app/Models/Download.php`
- [ ] `app/Models/SidebarConfig.php`

### Migrations
- [ ] `create_blog_notices_table.php`
- [ ] `create_blog_lectures_table.php`
- [ ] `create_blog_polls_table.php`
- [ ] `create_blog_poll_options_table.php`
- [ ] `create_blog_poll_votes_table.php`
- [ ] `create_blog_hangouts_table.php`
- [ ] `create_blog_book_recommendations_table.php`
- [ ] `create_blog_downloads_table.php`
- [ ] `create_blog_sidebar_configs_table.php`
- [ ] `add_sidebar_fields_to_blog_configs_table.php`

### Filament Resources
- [ ] `app/Filament/Resources/NoticeResource.php`
- [ ] `app/Filament/Resources/LectureResource.php`
- [ ] `app/Filament/Resources/PollResource.php`
- [ ] `app/Filament/Resources/HangoutResource.php`
- [ ] `app/Filament/Resources/BookRecommendationResource.php`
- [ ] `app/Filament/Resources/DownloadResource.php`
- [ ] `app/Filament/Resources/SidebarConfigResource.php`

### Controllers & Services
- [ ] `app/Http/Controllers/PollController.php` (AJAX voting)
- [ ] `app/Http/Controllers/DownloadController.php` (file serving)
- [ ] `app/Services/SidebarService.php` (widget management)

### Views & Components
- [ ] `resources/views/components/sidebar/notices.blade.php`
- [ ] `resources/views/components/sidebar/lectures.blade.php`
- [ ] `resources/views/components/sidebar/tags.blade.php`
- [ ] `resources/views/components/sidebar/poll.blade.php`
- [ ] `resources/views/components/sidebar/youtube.blade.php`
- [ ] `resources/views/components/sidebar/hangouts.blade.php`
- [ ] `resources/views/components/sidebar/books.blade.php`
- [ ] `resources/views/components/sidebar/downloads.blade.php`
- [ ] `resources/views/layouts/sidebar.blade.php`

### Routes
- [ ] Routes para votação em enquetes
- [ ] Routes para downloads
- [ ] Routes para estatísticas

## 🎯 Ordem de Implementação Sugerida

1. **Fase 1: Base**
   - [ ] Sistema de configuração de widgets
   - [ ] Estrutura básica da sidebar
   - [ ] Melhorias na configuração do blog

2. **Fase 2: Widgets Simples**
   - [ ] Sistema de recados
   - [ ] Melhoria das tags
   - [ ] Integração com YouTube

3. **Fase 3: Widgets Interativos**
   - [ ] Sistema de enquetes
   - [ ] Sistema de hangouts
   - [ ] Sistema de palestras

4. **Fase 4: Downloads e Finalização**
   - [ ] Sistema de livros/downloads
   - [ ] Sistema de arquivos
   - [ ] Testes finais e ajustes

## 📝 Notas Técnicas

- Usar Filament 3 para todas as interfaces administrativas
- Implementar AJAX para enquetes e interações dinâmicas
- Usar Laravel Storage para uploads de arquivos
- Implementar cache para widgets pesados
- Responsividade em todos os widgets
- Acessibilidade (ARIA labels, etc.)
- Validações robustas em todos os formulários
- Sistema de logs para ações importantes

## 🎨 Considerações de Design

- Manter consistência visual com o tema atual
- Cores personalizáveis via admin
- Responsive design
- Loading states para AJAX
- Animações suaves
- Icons apropriados para cada widget
- Hierarquia visual clara
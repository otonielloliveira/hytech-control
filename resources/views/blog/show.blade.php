@extends('layouts.blog')

@section('title', $post->meta_title ?? $post->title)
@section('description', $post->meta_description ?? $post->excerpt)

@section('content')
    <!-- Banner Carousel - Fixo em todas as telas -->
    @php
        $banners = App\Models\Banner::where('is_active', true)->orderBy('sort_order')->get();
    @endphp
    @if($banners->count() > 0)
        <div class="blog-banner">
            <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach($banners as $index => $banner)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}"></button>
                    @endforeach
                </div>
                
                <div class="carousel-inner">
                    @foreach($banners as $index => $banner)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" 
                             style="background-image: url('{{ $banner->image_url }}');">
                            <div class="carousel-overlay">
                                <div class="container">
                                    <div class="carousel-content">
                                        <h1>{{ $banner->title }}</h1>
                                        @if($banner->subtitle)
                                            <h2>{{ $banner->subtitle }}</h2>
                                        @endif
                                        @if($banner->description)
                                            <p>{{ $banner->description }}</p>
                                        @endif
                                        @if($banner->link_url)
                                            <a href="{{ $banner->link_url }}" class="btn-hero" 
                                               target="{{ $banner->target }}">
                                                {{ $banner->button_text }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($banners->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                @endif
            </div>
        </div>
    @endif

    <!-- Barra de Pesquisa e Login -->
    <section class="search-login-bar">
        <div class="container-fluid">
            <div class="row align-items-center py-3">
                <!-- Campo de Pesquisa -->
                <div class="col-lg-6 col-md-8 mb-2 mb-md-0">
                    <form action="{{ route('blog.search') }}" method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" 
                                   name="q" 
                                   class="form-control search-input" 
                                   placeholder="🔍 Pesquisar posts, notícias, petições..." 
                                   value="{{ request('q') }}"
                                   autocomplete="off">
                            <button class="btn btn-search" type="submit">
                                <i class="fas fa-search"></i>
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Área de Login/Cadastro -->
                <div class="col-lg-6 col-md-4 text-end">
                    <div class="auth-buttons">
                        @auth('client')
                            <!-- Cliente logado -->
                            <div class="dropdown">
                                <a href="#" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i>
                                    Olá, {{ auth('client')->user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('client.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Meu Painel
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.profile') }}">
                                        <i class="fas fa-user-edit me-2"></i>Meu Perfil
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.addresses') }}">
                                        <i class="fas fa-map-marker-alt me-2"></i>Endereços
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.preferences') }}">
                                        <i class="fas fa-cog me-2"></i>Preferências
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('client.logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <!-- Cliente não logado -->
                            <a href="#" class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Entrar
                            </a>
                            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <i class="fas fa-user-plus me-1"></i>
                                Cadastrar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Post Header -->
    <article class="post-detail">
        @if($post->featured_image)
            <div class="post-hero" style="background-image: url('{{ asset('storage/' . $post->featured_image) }}');">
                <div class="post-hero-overlay">
                    <div class="container">
                        <div class="post-hero-content">
                            <h1 class="post-title">{{ $post->title }}</h1>
                            
                            <div class="post-meta">
                                @if($post->category)
                                    <span class="category-badge" style="background-color: {{ $post->category->color }};">
                                        {{ $post->category->name }}
                                    </span>
                                @endif
                                
                                <span class="meta-item">
                                    <i class="fas fa-user"></i>
                                    {{ $post->user->name }}
                                </span>
                                
                                <span class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    {{ $post->published_at->format('d/m/Y') }}
                                </span>
                                
                                <span class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    {{ $post->reading_time }} min de leitura
                                </span>
                                
                                <span class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    {{ $post->views_count }} visualizações
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="container">
                <div class="post-header py-5 enhanced-header" data-destination="{{ $post->destination }}">
                    <div class="destination-badge mb-3">
                        <span class="badge destination-{{ $post->destination }}">
                            @switch($post->destination)
                                @case('artigos')
                                    📄 Artigo
                                    @break
                                @case('peticoes')
                                    ✊ Petição
                                    @break
                                @case('ultimas_noticias')
                                    📰 Últimas Notícias
                                    @break
                                @case('noticias_mundiais')
                                    🌍 Notícias Mundiais
                                    @break
                                @case('noticias_nacionais')
                                    🇧🇷 Notícias Nacionais
                                    @break
                                @case('noticias_regionais')
                                    📍 Notícias Regionais
                                    @break
                                @case('politica')
                                    🏛️ Política
                                    @break
                                @case('economia')
                                    💰 Economia
                                    @break
                                @case('amigos_apoiadores')
                                    🤝 Amigos e Apoiadores
                                    @break
                                @default
                                    📝 Conteúdo
                            @endswitch
                        </span>
                    </div>
                    
                    <h1 class="post-title enhanced-title">{{ $post->title }}</h1>
                    
                    <div class="post-meta">
                        @if($post->category)
                            <span class="category-badge" style="background-color: {{ $post->category->color }};">
                                {{ $post->category->name }}
                            </span>
                        @endif
                        
                        <span class="meta-item">
                            <i class="fas fa-user"></i>
                            {{ $post->user->name }}
                        </span>
                        
                        <span class="meta-item">
                            <i class="fas fa-calendar"></i>
                            {{ $post->published_at->format('d/m/Y') }}
                        </span>
                        
                        <span class="meta-item">
                            <i class="fas fa-clock"></i>
                            {{ $post->reading_time }} min de leitura
                        </span>
                        
                        <span class="meta-item">
                            <i class="fas fa-eye"></i>
                            {{ $post->views_count }} visualizações
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Post Content -->
        <div class="container-fluid">
            @php
                $sidebarConfig = App\Services\SidebarService::getSidebarConfig();
                $showSidebar = $sidebarConfig['show_sidebar'];
                $sidebarPosition = $sidebarConfig['position'] ?? 'right';
            @endphp
            
            <div class="row">
                @if($showSidebar && $sidebarPosition === 'left')
                    <div class="col-lg-3">
                        @include('layouts.sidebar')
                    </div>
                @endif
                
                <div class="@if($showSidebar) col-lg-9 @else col-lg-12 @endif">
                    <div class="container">
                        <div class="post-content">
                        @if($post->excerpt)
                            <div class="post-excerpt">
                                <p class="lead">{{ $post->excerpt }}</p>
                            </div>
                        @endif

                        <!-- Vídeo (se configurado para aparecer no conteúdo) -->
                        @if($post->show_video_in_content && $post->video_embed)
                            <div class="post-video mb-4">
                                {!! $post->video_embed !!}
                            </div>
                        @endif

                        <div class="post-body">
                            {!! $post->processed_content !!}
                        </div>

                        <!-- Seção Especial para Petições -->
                        @if($post->destination === 'peticoes')
                            <!-- Vídeos da Petição -->
                            @if($post->petition_videos && count($post->petition_videos) > 0)
                                <div class="petition-videos-section mt-5">
                                    <h4 class="section-title">
                                        <i class="fas fa-video text-danger me-2"></i>
                                        Vídeos da Campanha
                                    </h4>
                                    <div class="row">
                                        @foreach($post->petition_videos as $video)
                                            <div class="col-lg-6 mb-4">
                                                <div class="video-card">
                                                    <h5 class="video-title">{{ $video['titulo'] }}</h5>
                                                    @if($video['tipo'] === 'youtube')
                                                        @php
                                                            $videoId = '';
                                                            if (strpos($video['url'], 'youtube.com/watch?v=') !== false) {
                                                                $videoId = substr($video['url'], strpos($video['url'], 'v=') + 2, 11);
                                                            } elseif (strpos($video['url'], 'youtu.be/') !== false) {
                                                                $videoId = substr($video['url'], strpos($video['url'], 'youtu.be/') + 9, 11);
                                                            }
                                                        @endphp
                                                        @if($videoId)
                                                            <div class="video-embed">
                                                                <iframe 
                                                                    width="100%" 
                                                                    height="315" 
                                                                    src="https://www.youtube.com/embed/{{ $videoId }}" 
                                                                    frameborder="0" 
                                                                    allowfullscreen
                                                                    class="rounded">
                                                                </iframe>
                                                            </div>
                                                        @endif
                                                    @elseif($video['tipo'] === 'vimeo')
                                                        @php
                                                            $videoId = substr($video['url'], strrpos($video['url'], '/') + 1);
                                                        @endphp
                                                        <div class="video-embed">
                                                            <iframe 
                                                                src="https://player.vimeo.com/video/{{ $videoId }}" 
                                                                width="100%" 
                                                                height="315" 
                                                                frameborder="0" 
                                                                allowfullscreen
                                                                class="rounded">
                                                            </iframe>
                                                        </div>
                                                    @else
                                                        <div class="video-embed">
                                                            <video controls width="100%" height="315" class="rounded">
                                                                <source src="{{ $video['url'] }}" type="video/mp4">
                                                                Seu navegador não suporta vídeos.
                                                            </video>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Formulário de Assinatura da Petição -->
                            <div class="petition-form-section mt-5">
                                <div class="petition-form-card">
                                    <!-- Contador de Assinaturas -->
                                    <div class="signatures-counter text-center mb-4">
                                        <div class="counter-box">
                                            <h5 class="counter-number text-primary mb-1">
                                                <i class="fas fa-users me-2"></i>
                                                {{ $post->petitionSignatures()->count() }}
                                            </h5>
                                            <p class="counter-text text-muted mb-0">
                                                {{ $post->petitionSignatures()->count() == 1 ? 'pessoa assinou' : 'pessoas assinaram' }} esta petição
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <h4 class="form-title text-center">
                                        <i class="fas fa-hand-fist text-danger me-2"></i>
                                        Assine esta Petição
                                    </h4>
                                    <p class="text-center text-muted mb-4">
                                        Junte-se à nossa causa e faça a diferença!
                                    </p>
                                    
                                    <!-- Feedback Messages -->
                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="fas fa-check-circle me-2"></i>
                                            {{ session('success') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif
                                    
                                    @if (session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            {{ session('error') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif
                                    
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Por favor, corrija os seguintes erros:</h6>
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <form action="{{ route('blog.petition.signature.store', $post) }}" method="POST" class="petition-form">
                                        @csrf
                                        
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="nome" class="form-label">Nome *</label>
                                                <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror" 
                                                       value="{{ old('nome') }}" required>
                                                @error('nome')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Email *</label>
                                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                                       value="{{ old('email') }}" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="tel_whatsapp" class="form-label">Tel WhatsApp *</label>
                                                <input type="tel" name="tel_whatsapp" id="tel_whatsapp" class="form-control @error('tel_whatsapp') is-invalid @enderror" 
                                                       value="{{ old('tel_whatsapp') }}" placeholder="(11) 99999-9999" required>
                                                @error('tel_whatsapp')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="estado" class="form-label">Estado *</label>
                                                <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                                    <option value="">Selecione um estado</option>
                                                    <option value="AC" {{ old('estado') == 'AC' ? 'selected' : '' }}>Acre</option>
                                                    <option value="AL" {{ old('estado') == 'AL' ? 'selected' : '' }}>Alagoas</option>
                                                    <option value="AP" {{ old('estado') == 'AP' ? 'selected' : '' }}>Amapá</option>
                                                    <option value="AM" {{ old('estado') == 'AM' ? 'selected' : '' }}>Amazonas</option>
                                                    <option value="BA" {{ old('estado') == 'BA' ? 'selected' : '' }}>Bahia</option>
                                                    <option value="CE" {{ old('estado') == 'CE' ? 'selected' : '' }}>Ceará</option>
                                                    <option value="DF" {{ old('estado') == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                                    <option value="ES" {{ old('estado') == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                                    <option value="GO" {{ old('estado') == 'GO' ? 'selected' : '' }}>Goiás</option>
                                                    <option value="MA" {{ old('estado') == 'MA' ? 'selected' : '' }}>Maranhão</option>
                                                    <option value="MT" {{ old('estado') == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                                    <option value="MS" {{ old('estado') == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                                    <option value="MG" {{ old('estado') == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                                    <option value="PA" {{ old('estado') == 'PA' ? 'selected' : '' }}>Pará</option>
                                                    <option value="PB" {{ old('estado') == 'PB' ? 'selected' : '' }}>Paraíba</option>
                                                    <option value="PR" {{ old('estado') == 'PR' ? 'selected' : '' }}>Paraná</option>
                                                    <option value="PE" {{ old('estado') == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                                    <option value="PI" {{ old('estado') == 'PI' ? 'selected' : '' }}>Piauí</option>
                                                    <option value="RJ" {{ old('estado') == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                                    <option value="RN" {{ old('estado') == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                                    <option value="RS" {{ old('estado') == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                                    <option value="RO" {{ old('estado') == 'RO' ? 'selected' : '' }}>Rondônia</option>
                                                    <option value="RR" {{ old('estado') == 'RR' ? 'selected' : '' }}>Roraima</option>
                                                    <option value="SC" {{ old('estado') == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                                    <option value="SP" {{ old('estado') == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                                    <option value="SE" {{ old('estado') == 'SE' ? 'selected' : '' }}>Sergipe</option>
                                                    <option value="TO" {{ old('estado') == 'TO' ? 'selected' : '' }}>Tocantins</option>
                                                </select>
                                                @error('estado')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="cidade" class="form-label">Cidade *</label>
                                                <input type="text" name="cidade" id="cidade" class="form-control @error('cidade') is-invalid @enderror" 
                                                       value="{{ old('cidade') }}" placeholder="Digite sua cidade" required>
                                                @error('cidade')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="link_facebook" class="form-label">Link Facebook</label>
                                                <input type="url" name="link_facebook" id="link_facebook" class="form-control @error('link_facebook') is-invalid @enderror" 
                                                       value="{{ old('link_facebook') }}" placeholder="https://facebook.com/seuperfil">
                                                @error('link_facebook')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="link_instagram" class="form-label">Link Instagram</label>
                                                <input type="url" name="link_instagram" id="link_instagram" class="form-control @error('link_instagram') is-invalid @enderror" 
                                                       value="{{ old('link_instagram') }}" placeholder="https://instagram.com/seuperfil">
                                                @error('link_instagram')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-12 mb-4">
                                                <label for="observacao" class="form-label">Observação</label>
                                                <textarea name="observacao" id="observacao" class="form-control @error('observacao') is-invalid @enderror" 
                                                          rows="4" placeholder="Deixe uma mensagem sobre sua participação na petição (opcional)">{{ old('observacao') }}</textarea>
                                                @error('observacao')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-success btn-lg px-5">
                                                <i class="fas fa-pen-fancy me-2"></i>
                                                CONFIRMAR ASSINATURA
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Grupos WhatsApp -->
                            @if($post->whatsapp_groups && count($post->whatsapp_groups) > 0)
                                <div class="whatsapp-groups-section mt-5">
                                    <h4 class="section-title">
                                        <i class="fab fa-whatsapp text-success me-2"></i>
                                        Grupos WhatsApp por Região
                                    </h4>
                                    <p class="text-muted mb-4">
                                        Participe dos grupos regionais para se organizar com pessoas da sua região
                                    </p>
                                    
                                    <div class="row">
                                        @foreach($post->whatsapp_groups as $group)
                                            <div class="col-lg-4 col-md-6 mb-3">
                                                @if($group['status'] === 'ativo')
                                                    <a href="{{ $group['link_grupo'] }}" 
                                                       target="_blank" 
                                                       class="btn btn-success btn-lg w-100 whatsapp-group-btn">
                                                        <i class="fab fa-whatsapp me-2"></i>
                                                        <div>
                                                            <strong>{{ $group['estado'] }}</strong><br>
                                                            <small>{{ $group['nome_grupo'] }}</small>
                                                        </div>
                                                    </a>
                                                @elseif($group['status'] === 'cheio')
                                                    <button class="btn btn-secondary btn-lg w-100 whatsapp-group-btn" disabled>
                                                        <i class="fas fa-users me-2"></i>
                                                        <div>
                                                            <strong>{{ $group['estado'] }}</strong><br>
                                                            <small>Grupo Cheio</small>
                                                        </div>
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-secondary btn-lg w-100 whatsapp-group-btn" disabled>
                                                        <i class="fas fa-pause me-2"></i>
                                                        <div>
                                                            <strong>{{ $group['estado'] }}</strong><br>
                                                            <small>Inativo</small>
                                                        </div>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Share Buttons -->
                        <div class="share-buttons mt-4 pt-4 border-top">
                            <h6>Compartilhar este post:</h6>
                            <div class="share-icons d-flex gap-2 flex-wrap">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" 
                                   target="_blank" class="btn btn-facebook btn-sm">
                                    <i class="fab fa-facebook-f me-1"></i> Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" 
                                   target="_blank" class="btn btn-twitter btn-sm">
                                    <i class="fab fa-twitter me-1"></i> Twitter
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" 
                                   target="_blank" class="btn btn-linkedin btn-sm">
                                    <i class="fab fa-linkedin-in me-1"></i> LinkedIn
                                </a>
                                <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . request()->fullUrl()) }}" 
                                   target="_blank" class="btn btn-whatsapp btn-sm">
                                    <i class="fab fa-whatsapp me-1"></i> WhatsApp
                                </a>
                                <button onclick="copyToClipboard('{{ request()->fullUrl() }}')" 
                                        class="btn btn-secondary btn-sm">
                                    <i class="fas fa-link me-1"></i> Copiar Link
                                </button>
                            </div>
                        </div>

                        <!-- Tags -->
                        @if($post->tags && $post->tags->count() > 0)
                            <div class="post-tags mt-4">
                                <h6>Tags:</h6>
                                @foreach($post->tags as $tag)
                                    <a href="{{ route('blog.tag.show', $tag->slug) }}" 
                                       class="badge bg-secondary me-2 mb-2 text-decoration-none">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Comments Section -->
                    <div class="comments-section mt-5">
                        <div class="section-title">
                            <h3>💬 Comentários ({{ $post->approved_comments_count }})</h3>
                        </div>

                        <!-- Comment Form -->
                        @if($config->allow_comments)
                            <div class="comment-form-wrapper">
                                <form action="{{ route('blog.comment.store', $post) }}" method="POST" class="comment-form">
                                    @csrf
                                    
                                    @guest
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="author_name" class="form-label">Nome *</label>
                                                <input type="text" name="author_name" id="author_name" 
                                                       class="form-control @error('author_name') is-invalid @enderror" 
                                                       value="{{ old('author_name') }}" required>
                                                @error('author_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="author_email" class="form-label">E-mail *</label>
                                                <input type="email" name="author_email" id="author_email" 
                                                       class="form-control @error('author_email') is-invalid @enderror" 
                                                       value="{{ old('author_email') }}" required>
                                                @error('author_email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-user me-2"></i>Comentando como: <strong>{{ auth()->user()->name }}</strong>
                                    </div>
                                    @endguest
                                    
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Comentário *</label>
                                        <textarea name="content" id="content" rows="4" 
                                                  class="form-control @error('content') is-invalid @enderror" 
                                                  required>{{ old('content') }}</textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Enviar Comentário
                                    </button>
                                </form>
                            </div>
                        @endif

                        <!-- Comments List -->
                        @if($post->approvedComments->count() > 0)
                            <div class="comments-list mt-4">
                                @foreach($post->approvedComments as $comment)
                                    <div class="comment-item">
                                        <div class="comment-header">
                                            <h6 class="comment-author">{{ $comment->name }}</h6>
                                            <small class="comment-date text-muted">
                                                {{ $comment->created_at->format('d/m/Y \à\s H:i') }}
                                            </small>
                                        </div>
                                        <div class="comment-body">
                                            <p>{{ $comment->comment }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">Seja o primeiro a comentar!</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar">
                
                    </div>
                        </div>
                    </div>
                    
                    @if($showSidebar && $sidebarPosition === 'right')
                        <div class="col-lg-3">
                            @include('layouts.sidebar')
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Posts Relacionados - Carousel Fullwidth -->
        @if($relatedPosts->count() > 0)
            <div class="related-posts-carousel py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="section-header text-center mb-4">
                                <h3 class="section-title">
                                    <i class="fas fa-newspaper me-2"></i>
                                    Posts Relacionados
                                </h3>
                                <p class="section-subtitle text-muted">
                                    Outras publicações que podem interessar você
                                </p>
                            </div>
                            
                            <div id="relatedPostsCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    @php
                                        $chunks = $relatedPosts->chunk(3); // 3 posts por slide
                                    @endphp
                                    
                                    @foreach($chunks as $chunk)
                                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                            <div class="row g-4">
                                                @foreach($chunk as $relatedPost)
                                                    <div class="col-md-4">
                                                        <article class="related-post-card h-100">
                                                            <div class="card h-100 shadow-sm border-0">
                                                                <div class="post-image-container">
                                                                    @if($relatedPost->featured_image)
                                                                        <img src="{{ asset('storage/' . $relatedPost->featured_image) }}" 
                                                                             alt="{{ $relatedPost->title }}"
                                                                             class="card-img-top">
                                                                    @else
                                                                        <img src="{{ asset('images/default-no-image.png') }}" 
                                                                             alt="{{ $relatedPost->title }}"
                                                                             class="card-img-top">
                                                                    @endif
                                                                    @if($relatedPost->category)
                                                                        <span class="category-badge" style="background-color: {{ $relatedPost->category->color ?? '#007bff' }}">
                                                                            {{ $relatedPost->category->name }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                
                                                                <div class="card-body d-flex flex-column">
                                                                    <h5 class="card-title">
                                                                        <a href="{{ route('blog.post.show', $relatedPost->slug) }}" 
                                                                           class="text-decoration-none text-dark">
                                                                            {{ Str::limit($relatedPost->title, 70) }}
                                                                        </a>
                                                                    </h5>
                                                                    
                                                                    @if($relatedPost->excerpt)
                                                                        <p class="card-text text-muted flex-grow-1">
                                                                            {{ Str::limit($relatedPost->excerpt, 100) }}
                                                                        </p>
                                                                    @endif
                                                                    
                                                                    <div class="post-meta mt-auto">
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-calendar-alt me-1"></i>
                                                                                {{ $relatedPost->published_at->format('d/m/Y') }}
                                                                            </small>
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-eye me-1"></i>
                                                                                {{ number_format($relatedPost->views_count ?? 0) }}
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </article>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if($chunks->count() > 1)
                                    <!-- Carousel Controls -->
                                    <button class="carousel-control-prev" type="button" data-bs-target="#relatedPostsCarousel" data-bs-slide="prev">
                                        <div class="carousel-control-icon">
                                            <i class="fas fa-chevron-left"></i>
                                        </div>
                                        <span class="visually-hidden">Anterior</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#relatedPostsCarousel" data-bs-slide="next">
                                        <div class="carousel-control-icon">
                                            <i class="fas fa-chevron-right"></i>
                                        </div>
                                        <span class="visually-hidden">Próximo</span>
                                    </button>
                                    
                                    <!-- Carousel Indicators -->
                                    <div class="carousel-indicators">
                                        @foreach($chunks as $index => $chunk)
                                            <button type="button" data-bs-target="#relatedPostsCarousel" 
                                                    data-bs-slide-to="{{ $index }}" 
                                                    class="{{ $index === 0 ? 'active' : '' }}"
                                                    aria-label="Slide {{ $index + 1 }}"></button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </article>
@endsection

@section('styles')
<style>
    /* Video Container - Responsivo */
    .video-container {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        margin: 1rem 0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }
    
    .post-video {
        margin: 2rem 0;
    }

    .post-hero {
        height: 400px;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .post-hero-overlay {
        background: rgba(0,0,0,0.6);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
    }
    
    .post-hero-content {
        position: relative;
        z-index: 2;
        color: white;
    }
    
    .post-hero-content .post-title {
        color: white;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    /* Enhanced Header for Posts without Featured Image */
    .enhanced-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 3rem 2rem;
        margin: 2rem 0;
        position: relative;
        overflow: hidden;
        text-align: center;
        color: white;
    }
    
    .enhanced-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="60" r="0.8" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
        z-index: 1;
    }
    
    .enhanced-header > * {
        position: relative;
        z-index: 2;
    }
    
    .destination-badge {
        margin-bottom: 1rem;
    }
    
    .destination-badge .badge {
        font-size: 1rem;
        padding: 0.6rem 1.2rem;
        border-radius: 25px;
        font-weight: 500;
        border: 2px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(10px);
    }
    
    .enhanced-title {
        font-size: 2.8rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    /* Destination-specific badge colors */
    .destination-artigos { background: rgba(52, 152, 219, 0.9); }
    .destination-peticoes { background: rgba(231, 76, 60, 0.9); }
    .destination-ultimas_noticias { background: rgba(46, 204, 113, 0.9); }
    .destination-noticias_mundiais { background: rgba(155, 89, 182, 0.9); }
    .destination-noticias_nacionais { background: rgba(26, 188, 156, 0.9); }
    .destination-noticias_regionais { background: rgba(241, 196, 15, 0.9); }
    .destination-politica { background: rgba(230, 126, 34, 0.9); }
    .destination-economia { background: rgba(39, 174, 96, 0.9); }
    .destination-amigos_apoiadores { background: rgba(142, 68, 173, 0.9); }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .enhanced-title {
            font-size: 2rem;
        }
        
        .enhanced-header {
            padding: 2rem 1rem;
        }
    }
    
    /* Estilos para Seções de Petição */
    .petition-videos-section {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 15px;
        border-left: 4px solid #dc3545;
    }
    
    .petition-videos-section .section-title {
        color: #dc3545;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }
    
    .video-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        height: 100%;
    }
    
    .video-title {
        font-size: 1.1rem;
        margin-bottom: 1rem;
        color: #2d3748;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }
    
    .video-embed {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .video-embed iframe {
        border-radius: 10px;
    }
    
    /* Formulário de Petição */
    .petition-form-section {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }
    
    .petition-form-card {
        padding: 3rem;
        background: white;
        margin: 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border: 2px solid #e9ecef;
    }
    
    .signatures-counter {
        margin-bottom: 2rem;
    }
    
    .counter-box {
        display: inline-block;
        padding: 1.5rem 3rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        border: 2px solid #dee2e6;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .counter-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: #0d6efd;
    }
    
    .counter-text {
        font-size: 1.1rem;
        font-weight: 500;
    }
    
    /* Share Buttons */
    .share-buttons h6 {
        color: #333;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .share-icons .btn {
        border: none;
        font-weight: 500;
        transition: all 0.3s ease;
        margin-bottom: 0.5rem;
    }
    
    .btn-facebook { background-color: #1877f2; color: white; }
    .btn-facebook:hover { background-color: #166fe5; color: white; transform: translateY(-2px); }
    
    .btn-twitter { background-color: #1da1f2; color: white; }
    .btn-twitter:hover { background-color: #1a91da; color: white; transform: translateY(-2px); }
    
    .btn-linkedin { background-color: #0077b5; color: white; }
    .btn-linkedin:hover { background-color: #006fa6; color: white; transform: translateY(-2px); }
    
    .btn-whatsapp { background-color: #25d366; color: white; }
    .btn-whatsapp:hover { background-color: #22c55e; color: white; transform: translateY(-2px); }
    
    /* Related Posts Carousel */
    .related-posts-carousel {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        margin-top: 2rem;
    }
    
    .related-posts-carousel .section-title {
        color: #2d3748;
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .related-posts-carousel .section-subtitle {
        font-size: 1.1rem;
        margin-bottom: 0;
    }
    
    .related-post-card .card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .related-post-card .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    }
    
    .post-image-container {
        position: relative;
        overflow: hidden;
    }
    
    .post-image-container .card-img-top {
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .related-post-card .card:hover .card-img-top {
        transform: scale(1.05);
    }
    
    .category-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        color: white;
        padding: 4px 8px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 2;
    }
    
    .related-post-card .card-title a {
        color: #2d3748;
        font-weight: 600;
        transition: color 0.3s ease;
        line-height: 1.3;
    }
    
    .related-post-card .card-title a:hover {
        color: #667eea;
    }
    
    .related-post-card .card-text {
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .related-post-card .post-meta {
        border-top: 1px solid #e2e8f0;
        padding-top: 0.75rem;
        font-size: 0.8rem;
    }
    
    /* Carousel Controls Custom */
    .carousel-control-prev,
    .carousel-control-next {
        width: 50px;
        height: 50px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(102, 126, 234, 0.9);
        border-radius: 50%;
        border: none;
        opacity: 0.8;
        transition: all 0.3s ease;
    }
    
    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        opacity: 1;
        background: rgba(102, 126, 234, 1);
        transform: translateY(-50%) scale(1.1);
    }
    
    .carousel-control-prev {
        left: -25px;
    }
    
    .carousel-control-next {
        right: -25px;
    }
    
    .carousel-control-icon {
        width: 20px;
        height: 20px;
        color: white;
        font-size: 1rem;
    }
    
    .carousel-indicators {
        bottom: -50px;
    }
    
    .carousel-indicators button {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: none;
        background-color: #cbd5e0;
        opacity: 0.5;
        transition: all 0.3s ease;
    }
    
    .carousel-indicators button.active,
    .carousel-indicators button:hover {
        background-color: #667eea;
        opacity: 1;
        transform: scale(1.2);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .related-posts-carousel .section-title {
            font-size: 1.5rem;
        }
        
        .carousel-control-prev,
        .carousel-control-next {
            display: none;
        }
        
        .share-icons {
            justify-content: center;
        }
        
        .share-icons .btn {
            margin: 0.25rem;
        }
    }
    
    .form-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
    }
    
    .petition-form .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }
    
    .petition-form .form-control,
    .petition-form .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    
    .petition-form .form-control:focus,
    .petition-form .form-select:focus {
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
        outline: none;
    }
    
    .petition-form .btn-success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem 2rem;
        font-weight: 600;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
    }
    
    .petition-form .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(72, 187, 120, 0.4);
    }
    
    /* Grupos WhatsApp */
    .whatsapp-groups-section {
        background: #f0fff4;
        padding: 2rem;
        border-radius: 15px;
        border-left: 4px solid #25d366;
    }
    
    .whatsapp-groups-section .section-title {
        color: #25d366;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    
    .whatsapp-group-btn {
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-height: 80px;
    }
    
    .whatsapp-group-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 211, 102, 0.3);
    }
    
    .whatsapp-group-btn i {
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    .whatsapp-group-btn div {
        text-align: left;
        line-height: 1.2;
    }
    
    .post-hero-content .post-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .post-hero-content .meta-item {
        color: rgba(255,255,255,0.9);
        font-size: 0.9rem;
    }
    
    .post-hero-content .category-badge {
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .post-content {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .post-excerpt {
        border-left: 4px solid var(--primary-color);
        padding-left: 1rem;
        margin-bottom: 2rem;
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 5px;
    }
    
    .post-body img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        margin: 1rem 0;
    }
    
    .share-buttons .share-icons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .share-buttons .btn {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        text-decoration: none;
        font-size: 0.9rem;
        border: none;
    }
    
    .btn-facebook { background: #1877f2; color: white; }
    .btn-twitter { background: #1da1f2; color: white; }
    .btn-linkedin { background: #0077b5; color: white; }
    .btn-whatsapp { background: #25d366; color: white; }
    .btn-instagram { background: #e4405f; color: white; }
    .btn-youtube { background: #ff0000; color: white; }
    
    .comments-section {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .comment-item {
        border-bottom: 1px solid #eee;
        padding: 1rem 0;
    }
    
    .comment-item:last-child {
        border-bottom: none;
    }
    
    .comment-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .sidebar-widget {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .widget-title {
        color: var(--primary-color);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color);
    }
    
    .related-post {
        display: flex;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
    }
    
    .related-post:last-child {
        border-bottom: none;
    }
    
    .related-post img {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .related-post-content h6 {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    
    .related-post-content a {
        color: var(--text-color);
        text-decoration: none;
    }
    
    .related-post-content a:hover {
        color: var(--primary-color);
    }
    
    .social-links {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .social-links .btn {
        border: none;
        text-align: left;
        border-radius: 5px;
    }
    
    @media (max-width: 768px) {
        .post-hero {
            height: 250px;
        }
        
        .post-hero-content .post-title {
            font-size: 1.8rem;
        }
        
        .post-content {
            padding: 1rem;
        }
        
        .comments-section {
            padding: 1rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Feedback visual
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Copiado!';
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-success');
            
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-secondary');
            }, 2000);
        }).catch(function(err) {
            console.error('Erro ao copiar: ', err);
            alert('Erro ao copiar o link');
        });
    }
    
    // Auto-play carousel pause on hover
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('relatedPostsCarousel');
        if (carousel) {
            carousel.addEventListener('mouseenter', function() {
                bootstrap.Carousel.getInstance(carousel).pause();
            });
            carousel.addEventListener('mouseleave', function() {
                bootstrap.Carousel.getInstance(carousel).cycle();
            });
        }
    });
</script>
@endsection
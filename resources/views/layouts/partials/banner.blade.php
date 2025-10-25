<!-- Banner Carousel Moderno com Layers -->
    @php
        $banners = App\Models\Banner::where('is_active', true)->orderBy('sort_order')->get();
    @endphp
    @if ($banners->count() > 0)
        <div class="blog-banner">
            <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-indicators">
                    @foreach ($banners as $index => $banner)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                            class="{{ $index === 0 ? 'active' : '' }}" aria-label="Banner {{ $index + 1 }}"></button>
                    @endforeach
                </div>

                <div class="carousel-inner">
                    @foreach ($banners as $index => $banner)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}"
                            style="
                                height: {{ $banner->banner_height ?? 500 }}px;
                                @if($banner->background_image)
                                    background-image: url('{{ asset('storage/' . $banner->background_image) }}');
                                    background-position: {{ $banner->background_position ?? 'center center' }};
                                    background-size: {{ $banner->background_size ?? 'cover' }};
                                    background-repeat: no-repeat;
                                @elseif($banner->background_color)
                                    background-color: {{ $banner->background_color }};
                                @elseif($banner->image)
                                    background-image: url('{{ $banner->image_url }}');
                                    background-position: center center;
                                    background-size: cover;
                                    background-repeat: no-repeat;
                                @endif
                            ">
                            
                            <!-- Overlay -->
                            @if($banner->overlay_color && $banner->overlay_opacity > 0)
                                <div style="
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    right: 0;
                                    bottom: 0;
                                    background-color: {{ $banner->overlay_color }};
                                    opacity: {{ $banner->overlay_opacity / 100 }};
                                "></div>
                            @endif
                            
                            <!-- Content Layers -->
                            <div class="carousel-overlay" style="align-items: {{ $banner->content_alignment ?? 'center' }};">
                                <div class="container">
                                    <div class="carousel-content" style="position: relative; z-index: 10;">
                                        @if($banner->layers && is_array($banner->layers))
                                            @foreach($banner->layers as $layer)
                                                @if($layer['type'] === 'text')
                                                    <div style="
                                                        text-align: {{ $layer['data']['text_align'] ?? 'center' }};
                                                        margin-top: {{ $layer['data']['margin_top'] ?? 0 }}px;
                                                        margin-bottom: {{ $layer['data']['margin_bottom'] ?? 0 }}px;
                                                    ">
                                                        <{{ $layer['data']['tag'] ?? 'p' }} style="
                                                            color: {{ $layer['data']['color'] ?? 'inherit' }};
                                                            font-size: {{ $layer['data']['font_size'] ?? 16 }}px;
                                                            font-weight: {{ $layer['data']['font_weight'] ?? '400' }};
                                                            margin: 0;
                                                        ">
                                                            {!! $layer['data']['content'] ?? '' !!}
                                                        </{{ $layer['data']['tag'] ?? 'p' }}>
                                                    </div>
                                                
                                                @elseif($layer['type'] === 'button')
                                                    <div style="text-align: {{ $layer['data']['align'] ?? 'center' }}; margin-top: 1.5rem;">
                                                        <a href="{{ $layer['data']['url'] ?? '#' }}" 
                                                           target="{{ ($layer['data']['target_blank'] ?? false) ? '_blank' : '_self' }}"
                                                           class="banner-button banner-button-{{ $layer['data']['size'] ?? 'md' }}"
                                                           style="
                                                                background-color: {{ $layer['data']['bg_color'] ?? '#c41e3a' }};
                                                                color: {{ $layer['data']['text_color'] ?? '#ffffff' }};
                                                                border-radius: {{ $layer['data']['border_radius'] ?? 5 }}px;
                                                                @if($layer['data']['full_width'] ?? false)
                                                                    display: block;
                                                                    width: 100%;
                                                                @else
                                                                    display: inline-block;
                                                                @endif
                                                           ">
                                                            {{ $layer['data']['text'] ?? 'Saiba Mais' }}
                                                        </a>
                                                    </div>
                                                
                                                @elseif($layer['type'] === 'image')
                                                    <div style="text-align: {{ $layer['data']['align'] ?? 'center' }}; margin: 1rem 0;">
                                                        <img src="{{ asset('storage/' . $layer['data']['image']) }}" 
                                                             alt="Layer Image"
                                                             style="
                                                                @if(isset($layer['data']['width']))
                                                                    width: {{ $layer['data']['width'] }}px;
                                                                @endif
                                                                @if(isset($layer['data']['height']))
                                                                    height: {{ $layer['data']['height'] }}px;
                                                                @endif
                                                                max-width: 100%;
                                                                height: auto;
                                                             ">
                                                    </div>
                                                
                                                @elseif($layer['type'] === 'spacer')
                                                    <div style="height: {{ $layer['data']['height'] ?? 30 }}px;"></div>
                                                
                                                @elseif($layer['type'] === 'badge')
                                                    <div style="
                                                        text-align: {{ $layer['data']['align'] ?? 'center' }};
                                                        margin-bottom: {{ $layer['data']['margin_bottom'] ?? 15 }}px;
                                                    ">
                                                        <span style="
                                                            display: inline-block;
                                                            background-color: {{ $layer['data']['bg_color'] ?? '#c41e3a' }};
                                                            color: {{ $layer['data']['text_color'] ?? '#ffffff' }};
                                                            padding: 0.4rem 1rem;
                                                            border-radius: 20px;
                                                            font-size: 12px;
                                                            font-weight: 700;
                                                            text-transform: uppercase;
                                                            letter-spacing: 1px;
                                                        ">
                                                            {{ $layer['data']['text'] ?? 'NOVO' }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <!-- Fallback para banners antigos sem layers -->
                                            <h1>{{ $banner->title }}</h1>
                                            @if ($banner->subtitle)
                                                <h2>{{ $banner->subtitle }}</h2>
                                            @endif
                                            @if ($banner->description)
                                                <p>{{ $banner->description }}</p>
                                            @endif
                                            @if ($banner->link_url)
                                                <a href="{{ $banner->link_url }}" class="btn-hero"
                                                    target="{{ $banner->target }}">
                                                    {{ $banner->button_text }}
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($banners->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                @endif
            </div>
        </div>
        
        <style>
            /* Estilos para Botões das Layers */
            .banner-button {
                padding: 12px 30px;
                border: none;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }
            
            .banner-button-sm {
                padding: 8px 20px;
                font-size: 14px;
            }
            
            .banner-button-md {
                padding: 12px 30px;
                font-size: 16px;
            }
            
            .banner-button-lg {
                padding: 16px 40px;
                font-size: 18px;
            }
            
            .banner-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
                opacity: 0.9;
            }
            
            /* Responsividade das Layers */
            @media (max-width: 768px) {
                .carousel-item {
                    min-height: 400px !important;
                }
                
                .carousel-content h1 {
                    font-size: 1.5rem !important;
                }
                
                .carousel-content h2 {
                    font-size: 1.2rem !important;
                }
                
                .banner-button {
                    padding: 10px 20px;
                    font-size: 14px;
                }
            }
        </style>
    @endif

    <!-- Barra de Pesquisa e Login -->
    <section class="search-login-bar">
        <div class="container-fluid">
            <div class="row align-items-center py-3">
                <!-- Campo de Pesquisa -->
                <div class="col-lg-6 col-md-8 mb-2 mb-md-0">
                    <form action="{{ route('blog.search') }}" method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control search-input"
                                placeholder="🔍 Pesquisar posts, notícias, petições..." value="{{ request('q') }}"
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
                                <a href="#" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false">
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
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
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
                            <a href="#" class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal"
                                data-bs-target="#loginModal">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Entrar
                            </a>
                            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#registerModal">
                                <i class="fas fa-user-plus me-1"></i>
                                Cadastrar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>
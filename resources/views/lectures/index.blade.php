@extends('layouts.blog')

@section('title', 'Palestras - ' . $config->site_name)
@section('description', 'Participe de nossas palestras e eventos exclusivos.')

@section('content')
    <!-- Page Header -->
    @php
        $banners = App\Models\Banner::where('is_active', true)->orderBy('sort_order')->get();
    @endphp
    @if ($banners->count() > 0)
        <div class="blog-banner">
            <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach ($banners as $index => $banner)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                            class="{{ $index === 0 ? 'active' : '' }}"></button>
                    @endforeach
                </div>

                <div class="carousel-inner">
                    @foreach ($banners as $index => $banner)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}"
                            style="background-image: url('{{ $banner->image_url }}');">
                            <div class="carousel-overlay">
                                <div class="container">
                                    <div class="carousel-content">
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


    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Conteúdo Principal -->
            <div class="col-lg-12">
                <h1 class="text-center mb-4">Palestras</h1>
                <p class="text-center text-muted mb-5">Participe de nossas palestras e eventos exclusivos.</p>

                <div class="row">
                    @forelse($lectures as $lecture)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 lecture-card">
                                @if ($lecture->image_url)
                                    <div class="lecture-image">
                                        <img src="{{ $lecture->image_url }}" class="card-img-top"
                                            alt="{{ $lecture->title }}">
                                        @if ($lecture->date_time)
                                            <div class="lecture-date-badge">
                                                <div class="date-day">{{ $lecture->date_time->format('d') }}</div>
                                                <div class="date-month">{{ $lecture->date_time->format('M') }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="card-body">
                                    <h5 class="card-title">{{ $lecture->title }}</h5>

                                    <div class="lecture-meta mb-3">
                                        <div class="meta-item">
                                            <i class="fas fa-user text-primary me-2"></i>
                                            <span>{{ $lecture->speaker }}</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            <span>{{ $lecture->date_time ? $lecture->date_time->format('d/m/Y - H:i') : 'Data a definir' }}</span>
                                        </div>
                                        @if ($lecture->location)
                                            <div class="meta-item">
                                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                                <span>{{ $lecture->location }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($lecture->description)
                                        <p class="card-text">{{ Str::limit($lecture->description, 120) }}</p>
                                    @endif
                                </div>

                                <div class="card-footer bg-transparent">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('lectures.show', $lecture) }}" class="btn btn-primary">
                                            <i class="fas fa-eye me-2"></i>Ver Detalhes
                                        </a>
                                        @if ($lecture->link_url)
                                            <a href="{{ $lecture->link_url }}" target="_blank"
                                                class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-external-link-alt me-2"></i>Participar
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-microphone-slash fa-4x text-muted mb-3"></i>
                                <h4>Nenhuma palestra disponível</h4>
                                <p class="text-muted">Ainda não há palestras programadas. Volte em breve!</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Paginação -->
                @if ($lectures->hasPages())
                    <div class="row mt-5">
                        <div class="col-12">
                            <nav aria-label="Navegação de palestras">
                                {{ $lectures->links() }}
                            </nav>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->

        </div>
    </div>

    <style>
        .lecture-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }

        .lecture-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .lecture-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .lecture-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .lecture-card:hover .lecture-image img {
            transform: scale(1.05);
        }

        .lecture-date-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            padding: 8px 12px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .date-day {
            font-size: 1.2rem;
            font-weight: bold;
            color: #495057;
            line-height: 1;
        }

        .date-month {
            font-size: 0.8rem;
            color: #6c757d;
            text-transform: uppercase;
            line-height: 1;
        }

        .lecture-meta {
            font-size: 0.9rem;
        }

        .meta-item {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .meta-item i {
            width: 16px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .sidebar {
            padding-top: 2rem;
        }

        .sidebar-widget {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .widget-header {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f8f9fa;
        }

        .widget-header h5 {
            margin: 0;
            color: #495057;
            font-weight: 600;
        }

        .widget-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .widget-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .widget-icon {
            color: #6c757d;
            width: 20px;
            text-align: center;
        }

        .widget-info h6 a {
            color: #495057;
            text-decoration: none;
            font-size: 0.9rem;
            line-height: 1.3;
        }

        .widget-info h6 a:hover {
            color: #007bff;
        }

        .tag-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .tag-link {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            background: #f8f9fa;
            color: #6c757d;
            text-decoration: none;
            border-radius: 15px;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .tag-link:hover {
            background: #007bff;
            color: white;
        }

        .tag-count {
            font-size: 0.7rem;
            opacity: 0.7;
        }
    </style>
@endsection

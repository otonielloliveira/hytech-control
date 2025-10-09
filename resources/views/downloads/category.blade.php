@extends('layouts.blog')

@section('title', 'Downloads - ' . ucfirst($category) . ' - ' . $config->site_name)
@section('description', 'Downloads da categoria ' . $category)

@section('content')
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


    <section class="section">
        <div class="container">
            <!-- Filtros por Categoria -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5><i class="fas fa-filter me-2"></i>Filtrar por Categoria</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('downloads.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-th me-1"></i>Todos
                                </a>
                                @foreach ($categories as $cat)
                                    <a href="{{ route('downloads.category', $cat) }}"
                                        class="btn btn-sm {{ $cat === $category ? 'btn-primary' : 'btn-outline-secondary' }}">
                                        {{ ucfirst($cat) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid de Downloads -->
            <div class="row">
                @forelse($downloads as $download)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 download-card">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="download-icon me-3">
                                        <i class="{{ $download->icon_class }} fa-2x text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1">{{ $download->title }}</h5>
                                        <span class="badge bg-primary mb-2">{{ ucfirst($download->category) }}</span>
                                    </div>
                                </div>

                                @if ($download->description)
                                    <p class="card-text">{{ Str::limit($download->description, 100) }}</p>
                                @endif

                                <div class="download-meta mb-3">
                                    <small class="text-muted d-block">
                                        <i
                                            class="fas fa-file me-1"></i>{{ strtoupper($download->file_type ?? 'Arquivo') }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-weight-hanging me-1"></i>{{ $download->formatted_file_size }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-download me-1"></i>{{ $download->download_count }} downloads
                                    </small>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('downloads.show', $download) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Ver Detalhes
                                    </a>
                                    <a href="{{ route('downloads.download', $download) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-download me-1"></i>Baixar Agora
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                            <h4>Nenhum download na categoria "{{ ucfirst($category) }}"</h4>
                            <p class="text-muted">Não há arquivos disponíveis nesta categoria no momento.</p>
                            <a href="{{ route('downloads.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Ver Todos os Downloads
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Paginação -->
            @if ($downloads->hasPages())
                <div class="row mt-5">
                    <div class="col-12">
                        <nav aria-label="Navegação de downloads">
                            {{ $downloads->appends(['category' => $category])->links() }}
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <style>
        .download-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .download-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .download-icon {
            width: 60px;
            text-align: center;
        }

        .download-meta small {
            margin-bottom: 3px;
        }

        .btn-sm {
            font-size: 0.875rem;
        }
    </style>
@endsection

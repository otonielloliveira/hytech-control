@extends('layouts.blog')

@section('title', 'Downloads - ' . $config->site_name)
@section('description', 'Baixe nossos materiais exclusivos, documentos e recursos.')


@section('content')
    
    

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Downloads</h1>
                <p class="text-center text-muted mb-5">Baixe nossos materiais exclusivos, documentos e recursos.</p>

                <!-- Filtros por Categoria -->
                @if (!empty($categories))
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
                                                class="btn btn-outline-secondary btn-sm">
                                                {{ ucfirst($cat) }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Grid de Downloads -->
                <div class="row">
                    @forelse($downloads as $download)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 download-card">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="download-icon me-3">
                                            <i class="{{ $download->icon_class }} fa-2x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-1 download-title">{{ $download->title }}</h5>
                                            @if ($download->category)
                                                <span
                                                    class="badge bg-secondary mb-2">{{ ucfirst($download->category) }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="download-description mb-3">
                                        @if ($download->description)
                                            <p class="card-text">{{ Str::limit($download->description, 100) }}</p>
                                        @else
                                            <p class="card-text text-muted">Sem descrição disponível.</p>
                                        @endif
                                    </div>

                                    <div class="download-meta mb-3 mt-auto">
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
                                        @if($download->requires_login)
                                            <small class="text-warning d-block">
                                                <i class="fas fa-lock me-1"></i>Requer login de cliente
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-footer bg-transparent">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('downloads.show', $download) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Ver Detalhes
                                        </a>
                                        @if($download->requires_login && !auth()->guard('client')->check())
                                            <a href="{{ route('client.login') }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-lock me-1"></i>Login de Cliente para Baixar
                                            </a>
                                        @else
                                            <a href="{{ route('downloads.download', $download) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-download me-1"></i>Baixar Agora
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                                <h4>Nenhum download disponível</h4>
                                <p class="text-muted">Ainda não há arquivos para download. Volte em breve!</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Paginação -->
                @if ($downloads->hasPages())
                    <div class="row mt-5">
                        <div class="col-12">
                            <nav aria-label="Navegação de downloads">
                                {{ $downloads->links() }}
                            </nav>
                        </div>
                    </div>
                @endif
            </div>


        </div>
    </div>
    </div>

    <style>
        .download-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
        }

        .download-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .download-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .download-icon {
            width: 60px;
            text-align: center;
            flex-shrink: 0;
        }

        .download-title {
            min-height: 3rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5rem;
            font-size: 1.1rem;
        }

        .download-description {
            min-height: 4.5rem;
            flex-grow: 0;
        }

        .download-description p {
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5rem;
        }

        .download-meta {
            flex-shrink: 0;
        }

        .download-meta small {
            margin-bottom: 3px;
        }

        .btn-sm {
            font-size: 0.875rem;
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

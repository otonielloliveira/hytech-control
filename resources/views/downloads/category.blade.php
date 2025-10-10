@extends('layouts.blog')

@section('title', 'Downloads - ' . ucfirst($category) . ' - ' . $config->site_name)
@section('description', 'Downloads da categoria ' . $category)

@section('content')
    


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

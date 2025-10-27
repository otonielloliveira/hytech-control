@extends('layouts.blog')

@section('title', $download->title . ' - Downloads - ' . $config->site_name)
@section('description', $download->description ?: 'Baixe ' . $download->title)

@section('content')
    <!-- Banner Carousel -->


    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <div class="download-icon-large mb-3">
                                    <i class="{{ $download->icon_class }} fa-4x text-primary"></i>
                                </div>
                                <h2>{{ $download->title }}</h2>
                                @if ($download->category)
                                    <span class="badge bg-primary fs-6">{{ ucfirst($download->category) }}</span>
                                @endif
                            </div>

                            @if ($download->description)
                                <div class="mb-4">
                                    <h5><i class="fas fa-info-circle me-2"></i>Descrição</h5>
                                    <p class="lead">{{ $download->description }}</p>
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <h6><i class="fas fa-file me-2"></i>Tipo de Arquivo</h6>
                                        <p>{{ strtoupper($download->file_type ?? 'Arquivo') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <h6><i class="fas fa-weight-hanging me-2"></i>Tamanho</h6>
                                        <p>{{ $download->formatted_file_size }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <h6><i class="fas fa-download me-2"></i>Downloads</h6>
                                        <p>{{ $download->download_count }} vezes</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <h6><i class="fas fa-calendar me-2"></i>Adicionado em</h6>
                                        <p>{{ $download->created_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                @if($download->requires_login)
                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-lock me-2"></i>
                                            <strong>Atenção:</strong> Este download requer que você esteja logado como cliente no sistema.
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="text-center">
                                @if($download->requires_login && !auth()->guard('client')->check())
                                    <a href="{{ route('client.login') }}" class="btn btn-warning btn-lg me-3">
                                        <i class="fas fa-lock me-2"></i>Login de Cliente para Baixar
                                    </a>
                                @else
                                    <a href="{{ route('downloads.download', $download) }}" class="btn btn-primary btn-lg me-3">
                                        <i class="fas fa-download me-2"></i>Baixar Agora
                                    </a>
                                @endif
                                <a href="{{ route('downloads.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Voltar à Lista
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .download-icon-large {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-item {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .info-item h6 {
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .info-item p {
            margin: 0;
            font-weight: 500;
        }
    </style>
@endsection

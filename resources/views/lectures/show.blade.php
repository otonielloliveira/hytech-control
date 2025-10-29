@extends('layouts.blog')

@section('title', $lecture->title . ' - Palestras - ' . $config->site_name)
@section('description', $lecture->description ?: 'Palestra com ' . $lecture->speaker)

@section('content')


    <section class="section">
        <div class="container">
            <div class="row">
                <!-- Conteúdo Principal -->
                <div class="col-lg-8">
                    <div class="lecture-content">
                        <!-- Informações da Palestra -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="mb-4">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    Sobre a Palestra
                                </h3>

                                @if ($lecture->description)
                                    <div class="lecture-description">
                                        {!! $lecture->formatted_description !!}
                                    </div>
                                @endif

                                <!-- Detalhes do Evento -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <i class="fas fa-calendar text-primary me-2"></i>
                                            <strong>Data e Horário:</strong><br>
                                            {{ $lecture->date_time ? $lecture->date_time->format('d/m/Y às H:i') : 'Data a definir' }}
                                        </div>
                                    </div>
                                    @if ($lecture->location)
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                                <strong>Local:</strong><br>
                                                {{ $lecture->location }}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if ($lecture->link_url)
                                    <div class="text-center mt-4">
                                        <a href="{{ $lecture->link_url }}" target="_blank" class="btn btn-success btn-lg">
                                            <i class="fas fa-external-link-alt me-2"></i>
                                            Participar da Palestra
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Sobre o Palestrante -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="mb-4">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    Palestrante
                                </h3>

                                <div class="speaker-info">
                                    <h4>{{ $lecture->speaker }}</h4>
                                    <p class="text-muted">Palestrante Principal</p>

                                    <!-- Aqui você pode adicionar mais informações sobre o palestrante se tiver -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Ações Rápidas -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                Ações
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($lecture->link_url)
                                <div class="d-grid gap-2 mb-3">
                                    <a href="{{ $lecture->link_url }}" target="_blank" class="btn btn-success">
                                        <i class="fas fa-external-link-alt me-2"></i>
                                        Participar
                                    </a>
                                </div>
                            @endif

                            <div class="d-grid gap-2">
                                <a href="{{ route('lectures.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Voltar às Palestras
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Próximas Palestras -->
                    @if ($relatedLectures->count() > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar me-2"></i>
                                    Próximas Palestras
                                </h5>
                            </div>
                            <div class="card-body">
                                @foreach ($relatedLectures as $related)
                                    <div class="related-lecture {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                                        <h6 class="mb-1">
                                            <a href="{{ route('lectures.show', $related) }}" class="text-decoration-none">
                                                {{ $related->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-user me-1"></i>{{ $related->speaker }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i
                                                class="fas fa-calendar me-1"></i>{{ $related->date_time ? $related->date_time->format('d/m/Y - H:i') : 'Data a definir' }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <style>
        .lecture-description {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .lecture-description p {
            margin-bottom: 1rem;
        }

        .lecture-description h1,
        .lecture-description h2,
        .lecture-description h3,
        .lecture-description h4,
        .lecture-description h5,
        .lecture-description h6 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .lecture-description ul,
        .lecture-description ol {
            margin-bottom: 1rem;
            padding-left: 2rem;
        }

        .lecture-description li {
            margin-bottom: 0.5rem;
        }

        .lecture-description blockquote {
            border-left: 4px solid var(--primary-color);
            padding-left: 1rem;
            margin: 1.5rem 0;
            font-style: italic;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0 8px 8px 0;
        }

        .lecture-description strong,
        .lecture-description b {
            color: #333;
            font-weight: 600;
        }

        .lecture-description em,
        .lecture-description i {
            font-style: italic;
        }

        .lecture-description a {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .lecture-description a:hover {
            text-decoration: none;
        }

        .detail-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
        }

        .speaker-info h4 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .related-lecture h6 a {
            color: var(--text-color);
            transition: color 0.3s ease;
        }

        .related-lecture h6 a:hover {
            color: var(--primary-color);
        }

        .related-lecture small {
            margin-bottom: 2px;
        }
    </style>
@endsection

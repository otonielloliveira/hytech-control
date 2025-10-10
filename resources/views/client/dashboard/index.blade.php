@extends('layouts.blog')

@section('title', 'Meu Painel - ' . $client->name)

@section('content')
    <!-- Banner com informações do cliente -->
    <div class="client-header-banner">
        <div class="container">
            <div class="row align-items-center py-4">
                <div class="col-md-2">
                    <div class="client-avatar">
                        <img src="{{ $client->avatar_url }}" alt="{{ $client->name }}" class="rounded-circle">
                    </div>
                </div>
                <div class="col-md-8">
                    <h1 class="h3 mb-1">Bem-vindo, {{ $client->name }}!</h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-envelope me-2"></i>{{ $client->email }}
                        @if ($client->phone)
                            <span class="ms-3">
                                <i class="fas fa-phone me-2"></i>{{ $client->phone }}
                            </span>
                        @endif
                    </p>
                    @if ($client->last_login_at)
                        <small class="text-muted">
                            Último acesso: {{ $client->last_login_at->format('d/m/Y \à\s H:i') }}
                        </small>
                    @endif
                </div>
                <div class="col-md-2 text-end">
                    <div class="client-actions">
                        <a href="{{ route('client.profile') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Editar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Menu lateral do cliente -->
            
            @include('client.dashboard.partial-menu')

            <!-- Conteúdo principal -->
            <div class="col-lg-9">
                <div class="client-content">
                    <!-- Estatísticas rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="stats-info">
                                    <h3>{{ $stats['total_courses'] }}</h3>
                                    <p>Cursos Matriculados</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                                <div class="stats-info">
                                    <h3>{{ $stats['active_courses'] }}</h3>
                                    <p>Cursos Ativos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <div class="stats-info">
                                    <h3>{{ $stats['completed_courses'] }}</h3>
                                    <p>Cursos Concluídos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-icon">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <div class="stats-info">
                                    <h3>{{ $stats['total_orders'] }}</h3>
                                    <p>Total de Pedidos</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cursos Recentes -->
                    @if ($enrollments->count() > 0)
                        <div class="dashboard-section mb-4">
                            <div class="section-header">
                                <h4><i class="fas fa-graduation-cap me-2"></i>Meus Cursos Recentes</h4>
                                <a href="{{ route('courses.my-courses') }}" class="btn btn-outline-primary btn-sm">
                                    Ver Todos os Cursos <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                            <div class="section-content">
                                <div class="row">
                                    @foreach ($enrollments as $enrollment)
                                        @php $course = $enrollment->course; @endphp
                                        <div class="col-md-4 mb-3">
                                            <div class="course-card-mini">
                                                @if ($course->image)
                                                    <img src="{{ asset('storage/' . $course->image) }}"
                                                        alt="{{ $course->title }}" class="course-image">
                                                @else
                                                    <div class="course-image-placeholder">
                                                        <i class="fas fa-graduation-cap"></i>
                                                    </div>
                                                @endif

                                                <div class="course-info">
                                                    <div class="course-status mb-2">
                                                        @switch($enrollment->status)
                                                            @case('active')
                                                                <span class="badge badge-success">Ativo</span>
                                                            @break

                                                            @case('completed')
                                                                <span class="badge badge-primary">Concluído</span>
                                                            @break

                                                            @case('pending')
                                                                <span class="badge badge-warning">Pendente</span>
                                                            @break
                                                        @endswitch
                                                    </div>

                                                    <h6 class="course-title">{{ Str::limit($course->title, 40) }}</h6>

                                                    @if ($enrollment->status === 'active' || $enrollment->status === 'completed')
                                                        <div class="progress mb-2" style="height: 6px;">
                                                            <div class="progress-bar" role="progressbar"
                                                                style="width: {{ $enrollment->progress_percentage }}%"
                                                                aria-valuenow="{{ $enrollment->progress_percentage }}"
                                                                aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">{{ $enrollment->progress_percentage }}%
                                                            concluído</small>
                                                    @endif

                                                    <div class="course-action mt-2">
                                                        @if ($enrollment->status === 'active')
                                                            <a href="{{ route('courses.learning', $course->slug) }}"
                                                                class="btn btn-primary btn-sm btn-block">
                                                                <i class="fas fa-play"></i> Continuar
                                                            </a>
                                                        @elseif($enrollment->status === 'completed')
                                                            <a href="{{ route('courses.certificate', $course->slug) }}"
                                                                class="btn btn-success btn-sm btn-block">
                                                                <i class="fas fa-certificate"></i> Certificado
                                                            </a>
                                                        @else
                                                            <a href="{{ route('courses.show', $course->slug) }}"
                                                                class="btn btn-outline-secondary btn-sm btn-block">
                                                                Ver Curso
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Pedidos Recentes -->
                    @if ($recentOrders->count() > 0)
                        <div class="dashboard-section mb-4">
                            <div class="section-header">
                                <h4><i class="fas fa-shopping-bag me-2"></i>Pedidos Recentes</h4>
                                <a href="{{ route('client.orders') }}" class="btn btn-outline-primary btn-sm">
                                    Ver Todos os Pedidos <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                            <div class="section-content">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Pedido</th>
                                                <th>Data</th>
                                                <th>Status</th>
                                                <th>Total</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentOrders as $order)
                                                <tr>
                                                    <td>
                                                        <strong>#{{ $order->order_number }}</strong>
                                                    </td>
                                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $order->getStatusColor() }}">
                                                            {{ $order->getStatusLabel() }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong>R$
                                                            {{ number_format($order->total_amount, 2, ',', '.') }}</strong>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('client.orders.detail', $order->id) }}"
                                                            class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye"></i> Ver
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif



                    <!-- Informações rápidas -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-user me-2"></i>Informações do Perfil</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-row">
                                        <span class="label">Nome:</span>
                                        <span class="value">{{ $client->name }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="label">E-mail:</span>
                                        <span class="value">{{ $client->email }}</span>
                                    </div>
                                    @if ($client->phone)
                                        <div class="info-row">
                                            <span class="label">Telefone:</span>
                                            <span class="value">{{ $client->phone }}</span>
                                        </div>
                                    @endif
                                    @if ($client->birth_date)
                                        <div class="info-row">
                                            <span class="label">Nascimento:</span>
                                            <span class="value">{{ $client->birth_date->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                    <div class="mt-3">
                                        <a href="{{ route('client.profile') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit me-1"></i>Editar Perfil
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-map-marker-alt me-2"></i>Endereços</h5>
                                </div>
                                <div class="card-body">
                                    @if ($client->addresses->count() > 0)
                                        @foreach ($client->addresses->take(2) as $address)
                                            <div class="address-item">
                                                <strong>{{ $address->name }}</strong>
                                                @if ($address->is_default)
                                                    <span class="badge bg-primary ms-2">Padrão</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $address->full_address }}</small>
                                            </div>
                                        @endforeach
                                        @if ($client->addresses->count() > 2)
                                            <small class="text-muted">e mais {{ $client->addresses->count() - 2 }}
                                                endereços...</small>
                                        @endif
                                    @else
                                        <p class="text-muted">Nenhum endereço cadastrado.</p>
                                    @endif
                                    <div class="mt-3">
                                        <a href="{{ route('client.addresses') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>Gerenciar Endereços
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Dashboard Sections */
        .dashboard-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .section-header {
            padding: 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .section-header h4 {
            margin: 0;
            color: #333;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .section-content {
            padding: 1.5rem;
        }

        /* Course Cards Mini */
        .course-card-mini {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }

        .course-card-mini:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .course-image {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .course-image-placeholder {
            width: 100%;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }

        .course-info {
            padding: 1rem;
        }

        .course-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .progress {
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
        }

        .progress-bar {
            background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
            border-radius: 3px;
        }

        /* Quick Action Cards */
        .quick-action-card {
            display: block;
            padding: 1.5rem;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            height: 100%;
        }

        .quick-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: #007bff;
            text-decoration: none;
            border-color: #007bff;
        }

        .quick-action-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .quick-action-text h6 {
            margin: 0;
            font-weight: 600;
            color: inherit;
        }

        .quick-action-text small {
            color: #6c757d;
            font-size: 0.8rem;
        }

        /* Stats Cards Adjustments */
        .stats-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            margin: 0 auto 1rem;
        }

        .stats-info h3 {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stats-info p {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 0;
        }

        /* Table Enhancements */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .client-header-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-bottom: 0;
        }

        .client-avatar img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .client-sidebar {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .sidebar-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .sidebar-header h5 {
            margin: 0;
            color: #2d3748;
        }

        .client-nav {
            padding: 1rem 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #4a5568;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .nav-item:hover {
            background: #f7fafc;
            color: #667eea;
            text-decoration: none;
        }

        .nav-item.active {
            background: #667eea;
            color: white;
        }

        .nav-item i {
            width: 20px;
            margin-right: 0.75rem;
        }

        .logout-btn {
            color: #e53e3e !important;
        }

        .logout-btn:hover {
            background: #fed7d7 !important;
            color: #c53030 !important;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .stats-info h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
        }

        .stats-info p {
            margin: 0;
            color: #718096;
            font-size: 0.9rem;
        }

        .dashboard-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .dashboard-card .card-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .dashboard-card .card-header h5 {
            margin: 0;
            color: #2d3748;
        }

        .dashboard-card .card-body {
            padding: 1.5rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f1f3f4;
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .info-row .label {
            font-weight: 600;
            color: #4a5568;
        }

        .info-row .value {
            color: #2d3748;
        }

        .address-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f3f4;
        }

        .address-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .client-header-banner .row {
                text-align: center;
            }

            .client-sidebar {
                margin-bottom: 2rem;
            }
        }
    </style>
@endsection

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', $config->meta_title ?? $config->site_name)</title>
    <meta name="description" content="@yield('description', $config->meta_description ?? $config->site_description)">
    <meta name="keywords" content="{{ implode(', ', $config->meta_keywords ?? []) }}">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', $config->meta_title ?? $config->site_name)">
    <meta property="og:description" content="@yield('description', $config->meta_description ?? $config->site_description)">
    <meta property="og:image" content="@yield('og_image', $config->logo_url)">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', $config->meta_title ?? $config->site_name)">
    <meta name="twitter:description" content="@yield('description', $config->meta_description ?? $config->site_description)">
    <meta name="twitter:image" content="@yield('og_image', $config->logo_url)">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $config->favicon_url }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #f59e0b;
            --text-color: #1f2937;
            --light-bg: #f8fafc;
            --border-color: #e5e7eb;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
        }
        
        /* Header fixo */
        .navbar {
            background: #fff !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0.5rem 0;
        }
        
        .navbar-brand img {
            height: 40px;
        }
        
        .navbar-nav .nav-link {
            color: var(--text-color) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: color 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        /* Banner Carousel */
        .hero-carousel {
            margin-top: 76px; /* altura do navbar fixo */
        }
        
        .carousel-item {
            height: 500px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .carousel-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(0,0,0,0.7), rgba(0,0,0,0.3));
            display: flex;
            align-items: center;
        }
        
        .carousel-content {
            color: white;
            max-width: 600px;
        }
        
        .carousel-content h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .carousel-content h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        
        .carousel-content p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .btn-hero {
            background: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-hero:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Seções */
        .section {
            padding: 4rem 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 1rem;
        }
        
        .section-title p {
            font-size: 1.1rem;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Cards de Posts */
                /* Hero Carousel */
        .hero-carousel {
            height: 500px;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .carousel-item {
            height: 500px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        
        .carousel-overlay {
            background: linear-gradient(45deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
        }
        
        .carousel-content {
            color: white;
            z-index: 2;
            position: relative;
        }
        
        .carousel-content h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .carousel-content h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        
        .carousel-content p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.8;
        }
        
        .btn-hero {
            display: inline-block;
            padding: 1rem 2rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .btn-hero:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            color: white;
        }
        
        .carousel-indicators {
            bottom: 20px;
        }
        
        .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            background: transparent;
        }
        
        .carousel-indicators button.active {
            background: white;
        }
        
        .carousel-control-prev,
        .carousel-control-next {
            width: 5%;
            color: white;
        }
        
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-size: 20px 20px;
            width: 20px;
            height: 20px;
        }

        /* Additional Blog Styles */
        .post-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            border: none;
        }
        
        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .post-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .post-card-body {
            padding: 1.5rem;
        }
        
        .post-category {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
            margin-bottom: 1rem;
        }
        
        .post-title {
            font-size: 1.2rem;
            margin-bottom: 0.8rem;
            line-height: 1.4;
        }
        
        .post-title a {
            color: var(--text-color);
            text-decoration: none;
        }
        
        .post-title a:hover {
            color: var(--primary-color);
        }
        
        .post-excerpt {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .post-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .section {
            padding: 4rem 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        /* Newsletter Form */
        .newsletter-form {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .newsletter-form .form-control {
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
        }
        
        .newsletter-form .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(var(--primary-color-rgb), 0.25);
        }
        
        .newsletter-form .btn {
            border-radius: 25px;
            padding: 0.75rem 2rem;
        }
        
        /* Footer */
        .footer {
            background: var(--dark-bg);
            color: white;
            padding: 3rem 0 1rem;
        }
        
        .footer h5 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .footer a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: var(--primary-color);
        }
        
        .footer-bottom {
            border-top: 1px solid #495057;
            padding-top: 1rem;
            margin-top: 2rem;
            text-align: center;
            color: #adb5bd;
        }
        
        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin: 0 0.5rem;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        
        .social-links a:hover {
            transform: translateY(-2px);
            background: var(--secondary-color);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar-brand img {
                height: 40px;
            }
            
            .hero-carousel {
                height: 300px;
            }
            
            .carousel-item {
                height: 300px;
            }
            
            .carousel-content h1 {
                font-size: 2rem;
            }
            
            .carousel-content h2 {
                font-size: 1.2rem;
            }
            
            .carousel-content p {
                font-size: 1rem;
            }
            
            .section {
                padding: 2rem 0;
            }
        }
        
        /* Footer */
        .footer {
            background: #1f2937;
            color: white;
            padding: 3rem 0 1rem;
        }
        
        .footer h5 {
            color: white;
            margin-bottom: 1rem;
        }
        
        .footer a {
            color: #d1d5db;
            text-decoration: none;
        }
        
        .footer a:hover {
            color: white;
        }
        
        .social-links a {
            display: inline-block;
            margin-right: 1rem;
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }
        
        .social-links a:hover {
            color: var(--primary-color);
        }
        
        /* Newsletter */
        .newsletter-form {
            background: var(--light-bg);
            padding: 3rem 0;
        }
        
        .newsletter-form .form-control {
            border-radius: 5px;
            border: 2px solid var(--border-color);
            padding: 12px 15px;
        }
        
        .newsletter-form .btn {
            border-radius: 5px;
            padding: 12px 30px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .carousel-content h1 {
                font-size: 2rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .navbar-collapse {
                background: white;
                margin-top: 1rem;
                padding: 1rem;
                border-radius: 5px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
        }
    </style>
    
    @yield('styles')
    
    @if($config->custom_head_code)
        {!! $config->custom_head_code !!}
    @endif
</head>
<body>
    <!-- Header Fixo -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('blog.index') }}">
                @if($config->site_logo)
                    <img src="{{ $config->logo_url }}" alt="{{ $config->site_name }}">
                @else
                    <strong>{{ $config->site_name }}</strong>
                @endif
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('blog.index') }}">
                            <i class="fas fa-home me-1"></i>Início
                        </a>
                    </li>
                    @foreach($categories as $category)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('blog.category.show', $category->slug) }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/admin') }}">
                            <i class="fas fa-cog me-1"></i>Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Newsletter -->
    <section class="newsletter-form">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <h3 class="mb-3">📧 Receba nossas novidades</h3>
                    <p class="mb-4">Inscreva-se na nossa newsletter e fique por dentro das últimas postagens!</p>
                    
                    <form action="{{ route('blog.newsletter.subscribe') }}" method="POST" class="row g-2">
                        @csrf
                        <div class="col-md-8">
                            <input type="email" name="email" class="form-control" placeholder="Seu e-mail" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Inscrever</button>
                        </div>
                    </form>
                    
                    @if(session('success'))
                        <div class="alert alert-success mt-3">{{ session('success') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>{{ $config->site_name }}</h5>
                    <p>{{ $config->site_description }}</p>
                    
                    @if($config->social_links)
                        <div class="social-links">
                            @foreach($config->social_links as $platform => $url)
                                @if($url)
                                    <a href="{{ $url }}" target="_blank">
                                        <i class="fab fa-{{ $platform }}"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h5>Contato</h5>
                    @if($config->contact_email)
                        <p><i class="fas fa-envelope me-2"></i>{{ $config->contact_email }}</p>
                    @endif
                    @if($config->contact_phone)
                        <p><i class="fas fa-phone me-2"></i>{{ $config->contact_phone }}</p>
                    @endif
                    @if($config->address)
                        <p><i class="fas fa-map-marker-alt me-2"></i>{{ $config->address }}</p>
                    @endif
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h5>Categorias</h5>
                    @foreach($categories->take(5) as $category)
                        <p><a href="{{ route('blog.category.show', $category->slug) }}">{{ $category->name }}</a></p>
                    @endforeach
                </div>
            </div>
            
            <hr style="border-color: #374151;">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    @if($config->footer_text)
                        {!! $config->footer_text !!}
                    @endif
                </div>
                <div class="col-md-6 text-md-end">
                    <small>Desenvolvido por <a href="http://www.hytech.com.br/" target="_blank">HYTECH TECNOLOGIA LTDA</a></small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
    
    @if($config->custom_footer_code)
        {!! $config->custom_footer_code !!}
    @endif
</body>
</html>
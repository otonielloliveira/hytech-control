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
            --primary-color: #c41e3a;
            --secondary-color: #8b1428;
            --accent-color: #1a73e8;
            --text-color: #1a1a1a;
            --light-bg: #fafafa;
            --border-color: #e0e0e0;
            --gray-900: #111827;
            --gray-800: #1f2937;
            --gray-700: #374151;
            --gray-100: #f3f4f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.7;
            color: var(--text-color);
            background: #fff;
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Header profissional */
        .navbar {
            background: #fff !important;
            border-bottom: 3px solid var(--primary-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 0.75rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 24px;
            letter-spacing: -0.5px;
        }

        .navbar-brand img {
            height: 45px;
        }

        .navbar-nav .nav-link {
            color: var(--gray-700) !important;
            font-weight: 600;
            font-size: 12px;
            padding: 0.5rem 0.65rem !important;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
            background: rgba(196, 30, 58, 0.05);
            border-radius: 4px;
        }

        /* Badges de Notificação e Carrinho - Estilo Mercado Livre */
        .navbar-nav .nav-link.position-relative {
            padding: 0.5rem 0.75rem !important;
        }

        .navbar-nav .nav-link .badge {
            position: absolute;
            top: 3px !important;
            right: -5px !important;
            min-width: 14px;
            height: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 3px;
            font-size: 9px;
            font-weight: 600;
            border-radius: 7px;
            border: 1.5px solid #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
        }

        .navbar-nav .nav-link .badge.bg-danger {
            background: #ff3838 !important;
        }

        .navbar-nav .nav-link .badge.bg-primary {
            background: #3483fa !important;
        }

        .navbar-nav .nav-link i {
            font-size: 1.2rem;
        }

        /* Banner Carousel Estilo Editorial */
        .hero-carousel {
            margin-top: 76px;
            height: 450px;
            margin-bottom: 0;
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid var(--primary-color);
        }

        /* Breaking News Bar */
        .breaking-news {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 0;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .breaking-news .badge {
            background: white;
            color: var(--primary-color);
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            margin-right: 1rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Layout com Sidebar */
        .main-content {
            padding: 2rem 0;
            min-height: calc(100vh - 200px);
            background: var(--light-bg);
        }

        .main-content .container {
            margin-top: 0;
        }

        @media (max-width: 991.98px) {
            .main-content .col-lg-4 {
                margin-top: 2rem;
            }
        }

        .carousel-item {
            height: 500px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        .carousel-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.3) 50%, rgba(0, 0, 0, 0.85) 100%);
            display: flex;
            align-items: flex-end;
        }

        .carousel-content {
            color: white;
            max-width: 800px;
            z-index: 2;
            position: relative;
            padding-bottom: 3rem;
        }

        .carousel-content h1 {
            font-size: 2.75rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.6);
            line-height: 1.2;
            letter-spacing: -0.5px;
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
            border-radius: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-hero:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
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


        /* Seções */
        .section {
            padding: 3rem 0;
        }

        .section-title {
            margin-bottom: 2rem;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 1rem;
        }

        .section-title h2 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }

        .section-title p {
            font-size: 1rem;
            color: var(--gray-700);
            margin: 0.5rem 0 0 0;
        }

        /* Cards de posts estilo editorial */
        .post-card {
            background: white;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.25s ease;
            height: 100%;
            border: 1px solid var(--border-color);
        }

        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            border-color: var(--primary-color);
        }

        .post-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .post-card:hover img {
            transform: scale(1.05);
        }

        .post-card-body {
            padding: 1.5rem;
        }

        .post-category {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 700;
            color: white;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background: var(--primary-color);
        }

        .post-title {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            line-height: 1.4;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .post-title a {
            color: var(--gray-900);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .post-title a:hover {
            color: var(--primary-color);
        }

        .post-excerpt {
            color: var(--gray-700);
            font-size: 0.95rem;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.6;
        }

        .post-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.8rem;
            color: var(--gray-700);
            font-weight: 500;
            border-top: 1px solid var(--border-color);
            padding-top: 0.75rem;
            margin-top: auto;
        }

        .post-meta i {
            color: var(--primary-color);
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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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

        /* Footer Profissional */
        .footer {
            background: var(--gray-900);
            color: #e5e7eb;
            padding: 3rem 0 1.5rem;
            position: relative;
            border-top: 3px solid var(--primary-color);
        }

        .footer h5 {
            color: white;
            margin-bottom: 1.5rem;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .footer p {
            color: #9ca3af;
            margin-bottom: 1rem;
            line-height: 1.7;
            font-size: 0.9rem;
        }

        .footer a {
            color: #d1d5db;
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .footer a:hover {
            color: white;
            padding-left: 5px;
        }

        .footer small {
            color: #64748b;
            font-size: 0.85rem;
        }

        .footer i {
            color: inherit;
        }

        .footer hr {
            border-color: #334155;
            margin: 2.5rem 0 1.5rem;
            opacity: 0.6;
        }

        .footer-bottom {
            border-top: 1px solid #495057;
            padding-top: 1rem;
            margin-top: 2rem;
            text-align: center;
            color: #adb5bd;
        }

        .footer-bottom-section {
            padding-top: 1.5rem;
            background: rgba(15, 23, 42, 0.5);
            margin: 0 -15px;
            padding-left: 15px;
            padding-right: 15px;
            border-radius: 12px;
        }

        .footer-text-content {
            color: #cbd5e1;
            font-weight: 500;
        }

        .footer-credits {
            color: #64748b;
            font-weight: 500;
        }

        .footer-link {
            color: #60a5fa;
            text-decoration: none;
            font-weight: 600;
            position: relative;
        }

        .footer-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: linear-gradient(90deg, #60a5fa, #3b82f6);
            transition: width 0.3s ease;
        }

        .footer-link:hover {
            color: #3b82f6;
            text-shadow: 0 0 8px rgba(96, 165, 250, 0.3);
        }

        .footer-link:hover::after {
            width: 100%;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            padding: 0.25rem 0;
        }

        .footer-links a:hover {
            color: #60a5fa;
            transform: translateX(5px);
        }

        .footer-links i {
            width: 16px;
            text-align: center;
            opacity: 0.7;
        }

        .social-links {
            margin-top: 1.5rem;
        }

        .social-links a {
            display: inline-block;
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border-radius: 12px;
            text-align: center;
            line-height: 45px;
            margin: 0 0.75rem 0.75rem 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .social-links a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .social-links a:hover {
            transform: translateY(-3px) scale(1.05);
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .social-links a:hover::before {
            left: 100%;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-brand img {
                height: 40px;
            }

            .hero-carousel {
                height: 300px;
                margin-top: 70px;
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

            .section-title h2 {
                font-size: 2rem;
            }

            .navbar-collapse {
                background: white;
                margin-top: 1rem;
                padding: 1rem;
                border-radius: 5px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            /* Badges responsivos em mobile */
            .navbar-nav .nav-link .badge {
                top: -3px !important;
                right: -8px !important;
            }
        }
    </style>

    @yield('styles')

    <style>
        .category-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        /* Estilos para Petições */
        .petition-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .petition-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .petition-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .petition-card-body {
            padding: 1.5rem;
        }

        .petition-category {
            display: inline-block;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .petition-title {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .petition-title a {
            color: #2d3748;
            text-decoration: none;
        }

        .petition-title a:hover {
            color: #dc3545;
        }

        .petition-excerpt {
            color: #6c757d;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .whatsapp-preview {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 8px;
            border-left: 3px solid #25d366;
        }

        /* Estilos para Notícias */
        .news-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .news-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .news-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .news-card-body {
            padding: 1.2rem;
        }

        .breaking-badge {
            background: #dc3545;
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: bold;
            margin-right: 0.5rem;
            animation: pulse 2s infinite;
        }

        .news-category {
            display: inline-block;
            color: white;
            padding: 0.3rem 0.6rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
        }

        .news-title {
            font-size: 1rem;
            margin-bottom: 0.8rem;
            line-height: 1.4;
        }

        .news-title a {
            color: #2d3748;
            text-decoration: none;
        }

        .news-title a:hover {
            color: #0984e3;
        }

        .news-excerpt {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .news-meta {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Colunas de Notícias */
        .news-column {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .column-title {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.8rem;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        .news-item {
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid #f1f3f4;
        }

        .news-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .news-item h5 {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .news-item a {
            color: #2d3748;
            text-decoration: none;
        }

        .news-item a:hover {
            color: #0984e3;
        }

        .news-excerpt-small {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        /* Seções Compactas (Política/Economia) */
        .section-title-inline {
            border-bottom: 3px solid #e9ecef;
            padding-bottom: 0.8rem;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }

        .compact-card {
            display: flex;
            gap: 1rem;
            background: white;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .compact-card:hover {
            transform: translateY(-2px);
        }

        .compact-card img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0;
        }

        .compact-content h6 {
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
            line-height: 1.3;
        }

        .compact-content a {
            color: #2d3748;
            text-decoration: none;
        }

        .compact-content a:hover {
            color: #0984e3;
        }

        /* Amigos e Apoiadores Carousel */
        .amigos-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .amigos-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .amigos-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .amigos-content h5 {
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
            color: #2d3748;
        }

        .amigos-content h5 a {
            color: #2d3748;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .amigos-content h5 a:hover {
            color: #667eea;
        }

        .amigos-content p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.8rem;
            line-height: 1.4;
        }

        #amigosCarousel .carousel-control-prev,
        #amigosCarousel .carousel-control-next {
            width: 5%;
            color: white;
        }

        #amigosCarousel .carousel-control-prev-icon,
        #amigosCarousel .carousel-control-next-icon {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 40px;
            height: 40px;
        }

        /* Search and Login Bar */
        .search-login-bar {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .search-form .input-group {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 25px;
            overflow: hidden;
        }

        .search-input {
            border: none;
            padding: 12px 20px;
            font-size: 1rem;
            background: white;
        }

        .search-input:focus {
            box-shadow: none;
            border-color: transparent;
        }

        .btn-search {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            color: white;
            transform: translateY(-1px);
        }

        .auth-buttons .btn {
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .auth-buttons .btn-outline-primary {
            border-color: #667eea;
            color: #667eea;
        }

        .auth-buttons .btn-outline-primary:hover {
            background: #667eea;
            border-color: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .auth-buttons .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .auth-buttons .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .search-login-bar .col-lg-6 {
                text-align: center !important;
            }

            .auth-buttons {
                justify-content: center;
                display: flex;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Cores de Categorias Políticas */
        .cat-politica { background: #c41e3a; }
        .cat-economia { background: #059669; }
        .cat-sociedade { background: #1a73e8; }
        .cat-internacional { background: #7c3aed; }
        .cat-justica { background: #d97706; }
        .cat-eleicoes { background: #dc2626; }
        .cat-analise { background: #6366f1; }
        .cat-opiniao { background: #8b5cf6; }

        /* Featured Post Destacado */
        .featured-post {
            position: relative;
            height: 500px;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .featured-post img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .featured-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%);
            padding: 3rem 2rem 2rem;
            color: white;
        }

        .featured-overlay .category {
            background: var(--primary-color);
            display: inline-block;
            padding: 0.4rem 1rem;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        .featured-overlay h2 {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
        }

        .featured-overlay p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 1rem;
            opacity: 0.95;
        }

        /* Grid de Notícias */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Sidebar Widgets */
        .sidebar-widget {
            background: white;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .sidebar-widget h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar-widget ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-widget ul li {
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-widget ul li:last-child {
            border-bottom: none;
        }

        .sidebar-widget ul li a {
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .sidebar-widget ul li a:hover {
            color: var(--primary-color);
        }

        /* Trending Badge */
        .trending {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            font-weight: 700;
            font-size: 0.85rem;
        }

        .trending::before {
            content: '🔥';
            animation: pulse 1.5s infinite;
        }
    </style>

    @if ($config->custom_head_code)
        {!! $config->custom_head_code !!}
    @endif
</head>

<body>
    <!-- Breaking News Bar -->
    @if(isset($breakingNews) && $breakingNews)
    <div class="breaking-news">
        <div class="container">
            <div class="d-flex align-items-center">
                <span class="badge">URGENTE</span>
                <marquee behavior="scroll" direction="left" scrollamount="5">
                    {{ $breakingNews }}
                </marquee>
            </div>
        </div>
    </div>
    @endif

    <!-- Header Fixo -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('blog.index') }}">
                @if ($config->site_logo)
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
                    @auth('web')
                        <li class="nav-item">
                            <a class="nav-link text-primary" href="{{ route('filament.admin.pages.custom-dashboard') }}" title="Acessar Painel Administrativo">
                                <i class="fas fa-cog"></i> Painel
                            </a>
                        </li>
                    @endauth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('blog.index') }}">
                            Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('courses.index') }}">
                            Cursos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('store.index') }}">
                            Loja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lectures.index') }}">
                            Palestras
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('albums.index') }}">
                            Álbuns
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('videos.index') }}">
                            Vídeos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('downloads.index') }}">
                            Downloads
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="{{ route('donations.index') }}">
                            Apoiar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            Contato
                        </a>
                    </li>

                    <!-- Badge de Notificação -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="#">
                            <i class="fas fa-bell"></i>
                            <span class="badge bg-danger" style="display: none;">
                                0
                            </span>
                        </a>
                    </li>

                    <!-- Badge de Carrinho -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('store.cart') }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge bg-primary cart-counter" style="display: none;">
                                0
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @include('layouts.partials.banner')

    <!-- Conteúdo principal -->
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
                            <input type="email" name="email" class="form-control" placeholder="Seu e-mail"
                                required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Inscrever</button>
                        </div>
                    </form>

                    @if (session('success'))
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

                    @if ($config->social_links)
                        <div class="social-links">
                            @foreach ($config->social_links as $platform => $url)
                                @if ($url)
                                    <a href="{{ $url }}" target="_blank" title="{{ ucfirst($platform) }}">
                                        <i class="fab fa-{{ $platform }}"></i>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-lg-4 mb-4">
                    <h5>Contato</h5>
                    @if ($config->contact_email)
                        <p><i class="fas fa-envelope me-2"></i>{{ $config->contact_email }}</p>
                    @endif
                    @if ($config->contact_phone)
                        <p><i class="fas fa-phone me-2"></i>{{ $config->contact_phone }}</p>
                    @endif
                    @if ($config->address)
                        <p><i class="fas fa-map-marker-alt me-2"></i>{{ $config->address }}</p>
                    @endif
                </div>

                <div class="col-lg-4 mb-4">
                    <h5>Links Úteis</h5>
                    <div class="footer-links">
                        <p><a href="{{ route('blog.index') }}"><i class="fas fa-home me-2"></i>Página Inicial</a></p>
                        <p><a href="{{ route('albums.index') }}"><i class="fas fa-images me-2"></i>Galeria de
                                Fotos</a></p>
                        <p><a href="{{ route('videos.index') }}"><i class="fas fa-video me-2"></i>Vídeos</a></p>
                        <p><a href="{{ route('downloads.index') }}"><i class="fas fa-download me-2"></i>Downloads</a>
                        </p>
                        <p><a href="{{ route('lectures.index') }}"><i
                                    class="fas fa-chalkboard-teacher me-2"></i>Palestras</a></p>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row align-items-center footer-bottom-section">
                <div class="col-md-6">
                    @if ($config->footer_text)
                        <div class="footer-text-content">
                            {!! $config->footer_text !!}
                        </div>
                    @else
                        <div class="footer-text-content">
                            <small>&copy; {{ date('Y') }} {{ $config->site_name }}. Todos os direitos
                                reservados.</small>
                        </div>
                    @endif
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="footer-credits">
                        Desenvolvido por <a href="http://www.hytech.com.br/" target="_blank"
                            class="footer-link">HYTECH TECNOLOGIA LTDA</a>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Carregar contador do carrinho ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCounterFromServer();
        });

        // Função para atualizar contador do carrinho
        function updateCartCounter(count) {
            const counter = document.querySelector('.cart-counter');
            if (counter) {
                if (count > 0) {
                    counter.textContent = count;
                    counter.style.display = 'flex';
                } else {
                    counter.style.display = 'none';
                }
            }
        }

        // Buscar quantidade de itens no carrinho do servidor
        function updateCartCounterFromServer() {
            // Verifica se há dados no localStorage como cache
            const cachedCount = localStorage.getItem('cart_count');
            if (cachedCount !== null) {
                updateCartCounter(parseInt(cachedCount));
            }

            // Busca do servidor para atualizar
            fetch('{{ route("store.cart") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Extrai o contador do HTML retornado
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const cartItems = doc.querySelectorAll('.cart-item, [data-cart-item]');
                const count = cartItems.length;
                
                updateCartCounter(count);
                localStorage.setItem('cart_count', count);
            })
            .catch(error => {
                console.log('Erro ao carregar carrinho:', error);
            });
        }

        // Atualizar contador quando adicionar produto
        window.addEventListener('storage', function(e) {
            if (e.key === 'cart_count') {
                updateCartCounter(parseInt(e.newValue) || 0);
            }
        });
    </script>

    <!-- Modais de Login e Cadastro -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="loginModalLabel">
                        <i class="fas fa-sign-in-alt me-2 text-primary"></i>
                        Entrar na sua conta
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <form id="loginForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="loginEmail" name="email" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="loginPassword" name="password" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                            <label class="form-check-label" for="rememberMe">
                                Lembrar de mim
                            </label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Entrar
                            </button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <p class="mb-0">
                            Não tem uma conta? 
                            <a href="#" onclick="switchToRegister()" class="text-primary text-decoration-none">
                                Cadastre-se aqui
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="registerModalLabel">
                        <i class="fas fa-user-plus me-2 text-success"></i>
                        Criar nova conta
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <form id="registerForm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="registerName" class="form-label">Nome completo *</label>
                                <input type="text" class="form-control" id="registerName" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="registerEmail" class="form-label">E-mail *</label>
                                <input type="email" class="form-control" id="registerEmail" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="registerPhone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="registerPhone" name="phone" placeholder="(11) 99999-9999">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="registerPassword" class="form-label">Senha *</label>
                                <input type="password" class="form-control" id="registerPassword" name="password" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="registerPasswordConfirmation" class="form-label">Confirmar senha *</label>
                                <input type="password" class="form-control" id="registerPasswordConfirmation" name="password_confirmation" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="registerBirthDate" class="form-label">Data de nascimento</label>
                                <input type="date" class="form-control" id="registerBirthDate" name="birth_date">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="registerGender" class="form-label">Gênero</label>
                                <select class="form-select" id="registerGender" name="gender">
                                    <option value="">Selecione</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="feminino">Feminino</option>
                                    <option value="outro">Outro</option>
                                    <option value="nao_informar">Prefiro não informar</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i>
                                Criar conta
                            </button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <p class="mb-0">
                            Já tem uma conta? 
                            <a href="#" onclick="switchToLogin()" class="text-primary text-decoration-none">
                                Faça login aqui
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Switch between login and register modals
        function switchToRegister() {
            const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
            if (loginModal) loginModal.hide();
            const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
            registerModal.show();
        }

        function switchToLogin() {
            const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
            if (registerModal) registerModal.hide();
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        }

        // Handle login form submission
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Clear previous errors
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Entrando...';
            
            fetch('{{ route("client.auth.login") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    email: form.email.value,
                    password: form.password.value,
                    remember: form.remember.checked
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Login realizado!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    throw new Error(data.message || 'Erro ao fazer login');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: error.message || 'Ocorreu um erro. Tente novamente.'
                });
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        // Handle register form submission
        document.getElementById('registerForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Clear previous errors
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Criando conta...';
            
            const formData = new FormData(form);
            
            fetch('{{ route("client.auth.register") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Conta criada!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    if (data.errors) {
                        for (const field in data.errors) {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const feedback = input.nextElementSibling;
                                if (feedback && feedback.classList.contains('invalid-feedback')) {
                                    feedback.textContent = data.errors[field][0];
                                }
                            }
                        }
                    }
                    throw new Error(data.message || 'Erro ao criar conta');
                }
            })
            .catch(error => {
                if (!error.message.includes('Erro ao criar conta')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: error.message || 'Ocorreu um erro. Tente novamente.'
                    });
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    </script>

    @yield('scripts')

    @if ($config->custom_footer_code)
        {!! $config->custom_footer_code !!}
    @endif
</body>

</html>

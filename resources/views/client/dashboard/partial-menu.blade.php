    <div class="col-lg-3">
        <div class="client-sidebar">
            <div class="sidebar-header">
                <h5><i class="fas fa-user-circle me-2"></i>Minha Conta</h5>
            </div>
            <nav class="client-nav">
                <a href="{{ route('client.dashboard') }}" class="nav-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
        
                <a href="{{ route('client.profile') }}" class="nav-item {{ request()->routeIs('client.profile') ? 'active' : '' }}">
                    <i class="fas fa-user-edit"></i>
                    Meu Perfil
                </a>
                <a href="{{ route('courses.my-courses') }}" class="nav-item {{ request()->routeIs('courses.my-courses') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i>
                    Meus Cursos
                </a>
                <a href="{{ route('client.orders') }}" class="nav-item {{ request()->routeIs('client.orders') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    Meus Pedidos
                </a>
                <a href="{{ route('client.addresses') }}" class="nav-item {{ request()->routeIs('client.addresses') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt"></i>
                    Endereços
                </a>
                <a href="{{ route('client.preferences') }}" class="nav-item {{ request()->routeIs('client.preferences') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    Preferências
                </a>
                <a href="{{ route('blog.index') }}" class="nav-item">
                    <i class="fas fa-home"></i>
                    Voltar ao Blog
                </a>
                <hr>
                <form action="{{ route('client.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-item logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        Sair da Conta
                    </button>
                </form>
            </nav>
        </div>
    </div>

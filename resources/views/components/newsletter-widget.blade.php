<div class="sidebar-widget">
    <h5 class="widget-title">📧 Newsletter</h5>
    <p class="text-muted mb-3">Receba nossas novidades e posts diretamente no seu e-mail!</p>
    
    <form action="{{ route('blog.newsletter.subscribe') }}" method="POST" class="newsletter-form">
        @csrf
        <div class="mb-3">
            <input type="text" name="name" class="form-control" 
                   placeholder="Seu nome" required>
        </div>
        <div class="mb-3">
            <input type="email" name="email" class="form-control" 
                   placeholder="Seu e-mail" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-paper-plane me-2"></i>Inscrever-se
        </button>
    </form>
    
    <small class="text-muted mt-2 d-block">
        💡 Prometemos não enviar spam!
    </small>
</div>

<style>
    .newsletter-form .form-control {
        border-radius: 25px;
        padding: 0.75rem 1rem;
        border: 2px solid #e9ecef;
    }
    
    .newsletter-form .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-color-rgb), 0.25);
    }
    
    .newsletter-form .btn {
        border-radius: 25px;
        padding: 0.75rem 1rem;
        font-weight: 500;
    }
</style>
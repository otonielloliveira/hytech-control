@extends('layouts.app')

@section('title', 'Certificado - ' . $course->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="certificate-container bg-white shadow-lg">
                <!-- Certificate Header -->
                <div class="certificate-header text-center p-4 bg-primary text-white">
                    <h2 class="mb-1">CERTIFICADO DE CONCLUSÃO</h2>
                    <p class="mb-0">{{ $course->certificateType->name ?? 'Curso Online' }}</p>
                </div>
                
                <!-- Certificate Body -->
                <div class="certificate-body p-5">
                    <div class="certificate-content text-center">
                        <!-- Decorative Elements -->
                        <div class="certificate-decoration mb-4">
                            <i class="fas fa-award fa-4x text-warning mb-3"></i>
                        </div>
                        
                        <!-- Main Text -->
                        <div class="certificate-text mb-4">
                            <h4 class="mb-4">Certificamos que</h4>
                            
                            <div class="student-name mb-4">
                                <h2 class="text-primary border-bottom border-primary pb-2 d-inline-block">
                                    {{ $enrollment->client->name }}
                                </h2>
                            </div>
                            
                            <h5 class="mb-4">concluiu com êxito o curso</h5>
                            
                            <div class="course-title mb-4">
                                <h3 class="font-weight-bold">{{ $course->title }}</h3>
                            </div>
                            
                            <div class="course-details mb-4">
                                <p class="mb-2">
                                    <strong>Carga Horária:</strong> {{ $course->estimated_duration }} horas
                                </p>
                                <p class="mb-2">
                                    <strong>Nível:</strong> {{ ucfirst($course->level) }}
                                </p>
                                <p class="mb-2">
                                    <strong>Data de Conclusão:</strong> {{ $enrollment->completed_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Certificate Info -->
                        <div class="certificate-info mt-5">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="certificate-code">
                                        <small class="text-muted">Código do Certificado:</small>
                                        <br>
                                        <strong>{{ $enrollment->certificate_number }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="issue-date">
                                        <small class="text-muted">Data de Emissão:</small>
                                        <br>
                                        <strong>{{ $enrollment->certificate_issued_at->format('d/m/Y') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Signature Section -->
                        <div class="signature-section mt-5 pt-4 border-top">
                            <div class="row">
                                <div class="col-md-6 offset-md-3">
                                    <div class="signature text-center">
                                        <div class="signature-line border-top border-dark pt-2">
                                            <strong>Sistema de Ensino</strong>
                                            <br>
                                            <small class="text-muted">Coordenação Acadêmica</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Certificate Footer -->
                <div class="certificate-footer bg-light p-3 text-center">
                    <small class="text-muted">
                        Este certificado pode ser verificado através do código acima em nosso sistema.
                        <br>
                        Emitido automaticamente em {{ $enrollment->certificate_issued_at->format('d/m/Y') }}.
                    </small>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="certificate-actions text-center mt-4 mb-5">
                <button onclick="window.print()" class="btn btn-primary btn-lg mr-3">
                    <i class="fas fa-print"></i> Imprimir Certificado
                </button>
                <a href="{{ route('courses.my-courses') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-arrow-left"></i> Voltar aos Meus Cursos
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.certificate-container {
    border: 3px solid #007bff;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.certificate-container::before {
    content: '';
    position: absolute;
    top: 15px;
    left: 15px;
    right: 15px;
    bottom: 15px;
    border: 2px solid #6c757d;
    border-radius: 5px;
    pointer-events: none;
    opacity: 0.3;
}

.certificate-decoration {
    position: relative;
}

.certificate-decoration::before,
.certificate-decoration::after {
    content: '✦';
    position: absolute;
    font-size: 2rem;
    color: #ffc107;
    top: 50%;
    transform: translateY(-50%);
}

.certificate-decoration::before {
    left: -60px;
}

.certificate-decoration::after {
    right: -60px;
}

.student-name h2 {
    font-family: 'Georgia', serif;
    font-style: italic;
    letter-spacing: 1px;
}

.course-title h3 {
    color: #333;
    font-family: 'Georgia', serif;
}

.certificate-text h4,
.certificate-text h5 {
    color: #555;
    font-weight: 300;
}

.signature-line {
    width: 200px;
    margin: 0 auto;
    padding-top: 5px;
}

.certificate-info {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}

/* Print Styles */
@media print {
    body * {
        visibility: hidden;
    }
    
    .certificate-container,
    .certificate-container * {
        visibility: visible;
    }
    
    .certificate-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        box-shadow: none !important;
        border: 2px solid #000 !important;
    }
    
    .certificate-actions {
        display: none !important;
    }
    
    .certificate-header {
        background-color: #007bff !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .certificate-footer {
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .text-primary {
        color: #007bff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .text-warning {
        color: #ffc107 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .border-primary {
        border-color: #007bff !important;
    }
    
    /* Force page break */
    .certificate-container {
        page-break-before: always;
        page-break-after: always;
    }
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .certificate-body {
        padding: 2rem 1rem;
    }
    
    .certificate-decoration::before,
    .certificate-decoration::after {
        display: none;
    }
    
    .student-name h2 {
        font-size: 1.5rem;
    }
    
    .course-title h3 {
        font-size: 1.25rem;
    }
    
    .certificate-actions .btn {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
        margin-right: 0 !important;
    }
}

/* Animations */
.certificate-container {
    animation: slideIn 0.8s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.certificate-decoration i {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}
</style>

<!-- Additional Scripts for Certificate Features -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add fancy hover effects
    const certificate = document.querySelector('.certificate-container');
    
    certificate.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.02)';
        this.style.transition = 'transform 0.3s ease';
    });
    
    certificate.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
    
    // Print button enhancement
    const printBtn = document.querySelector('button[onclick="window.print()"]');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            // Add a small delay to ensure proper print rendering
            setTimeout(() => {
                window.print();
            }, 100);
        });
    }
});
</script>
@endsection
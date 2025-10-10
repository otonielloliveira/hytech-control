@extends('layouts.blog')

@section('title', 'Meus Endereços - ' . $client->name)

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
                <div class="col-md-10">
                    <h1 class="h3 mb-1">Meus Endereços</h1>
                    <p class="text-muted mb-0">Gerencie seus endereços de entrega e correspondência</p>
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
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Botão adicionar endereço -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4><i class="fas fa-map-marker-alt me-2"></i>Meus Endereços</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="fas fa-plus me-2"></i>Adicionar Endereço
                        </button>
                    </div>

                    <!-- Lista de endereços -->
                    <div class="row">
                        @forelse($addresses as $address)
                            <div class="col-md-6 mb-4">
                                <div class="address-card">
                                    <div class="address-header">
                                        <h5>{{ $address->name }}</h5>
                                        @if($address->is_default)
                                            <span class="badge bg-success">Padrão</span>
                                        @endif
                                    </div>
                                    <div class="address-body">
                                        <p class="address-text">{{ $address->full_address }}</p>
                                        <small class="text-muted">CEP: {{ $address->postal_code }}</small>
                                    </div>
                                    <div class="address-actions">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editAddress({{ $address }})">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        @if(!$address->is_default)
                                            <form action="{{ route('client.addresses.update', $address) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="is_default" value="1">
                                                <input type="hidden" name="name" value="{{ $address->name }}">
                                                <input type="hidden" name="postal_code" value="{{ $address->postal_code }}">
                                                <input type="hidden" name="street" value="{{ $address->street }}">
                                                <input type="hidden" name="number" value="{{ $address->number }}">
                                                <input type="hidden" name="complement" value="{{ $address->complement }}">
                                                <input type="hidden" name="neighborhood" value="{{ $address->neighborhood }}">
                                                <input type="hidden" name="city" value="{{ $address->city }}">
                                                <input type="hidden" name="state" value="{{ $address->state }}">
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-star"></i> Tornar Padrão
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('client.addresses.delete', $address) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este endereço?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="empty-state">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <h5>Nenhum endereço cadastrado</h5>
                                    <p>Adicione seu primeiro endereço para facilitar futuras operações.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                        <i class="fas fa-plus me-2"></i>Adicionar Primeiro Endereço
                                    </button>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Adicionar Endereço -->
    <div class="modal fade" id="addAddressModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Adicionar Novo Endereço
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('client.addresses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nome do endereço *</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Casa, Trabalho, etc." required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="postal_code" class="form-label">CEP *</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="00000-000" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="street" class="form-label">Logradouro *</label>
                                <input type="text" class="form-control" id="street" name="street" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="number" class="form-label">Número *</label>
                                <input type="text" class="form-control" id="number" name="number" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="complement" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="complement" name="complement" placeholder="Apto, bloco, etc.">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="neighborhood" class="form-label">Bairro *</label>
                                <input type="text" class="form-control" id="neighborhood" name="neighborhood" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="city" class="form-label">Cidade *</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">Estado *</label>
                                <select class="form-select" id="state" name="state" required>
                                    <option value="">Selecione</option>
                                    <option value="AC">Acre</option>
                                    <option value="AL">Alagoas</option>
                                    <option value="AP">Amapá</option>
                                    <option value="AM">Amazonas</option>
                                    <option value="BA">Bahia</option>
                                    <option value="CE">Ceará</option>
                                    <option value="DF">Distrito Federal</option>
                                    <option value="ES">Espírito Santo</option>
                                    <option value="GO">Goiás</option>
                                    <option value="MA">Maranhão</option>
                                    <option value="MT">Mato Grosso</option>
                                    <option value="MS">Mato Grosso do Sul</option>
                                    <option value="MG">Minas Gerais</option>
                                    <option value="PA">Pará</option>
                                    <option value="PB">Paraíba</option>
                                    <option value="PR">Paraná</option>
                                    <option value="PE">Pernambuco</option>
                                    <option value="PI">Piauí</option>
                                    <option value="RJ">Rio de Janeiro</option>
                                    <option value="RN">Rio Grande do Norte</option>
                                    <option value="RS">Rio Grande do Sul</option>
                                    <option value="RO">Rondônia</option>
                                    <option value="RR">Roraima</option>
                                    <option value="SC">Santa Catarina</option>
                                    <option value="SP">São Paulo</option>
                                    <option value="SE">Sergipe</option>
                                    <option value="TO">Tocantins</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1">
                            <label class="form-check-label" for="is_default">
                                Definir como endereço padrão
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Endereço
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Endereço -->
    <div class="modal fade" id="editAddressModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Editar Endereço
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAddressForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_name" class="form-label">Nome do endereço *</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_postal_code" class="form-label">CEP *</label>
                                <input type="text" class="form-control" id="edit_postal_code" name="postal_code" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="edit_street" class="form-label">Logradouro *</label>
                                <input type="text" class="form-control" id="edit_street" name="street" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_number" class="form-label">Número *</label>
                                <input type="text" class="form-control" id="edit_number" name="number" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_complement" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="edit_complement" name="complement">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_neighborhood" class="form-label">Bairro *</label>
                                <input type="text" class="form-control" id="edit_neighborhood" name="neighborhood" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="edit_city" class="form-label">Cidade *</label>
                                <input type="text" class="form-control" id="edit_city" name="city" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_state" class="form-label">Estado *</label>
                                <select class="form-select" id="edit_state" name="state" required>
                                    <option value="">Selecione</option>
                                    <option value="AC">Acre</option>
                                    <option value="AL">Alagoas</option>
                                    <option value="AP">Amapá</option>
                                    <option value="AM">Amazonas</option>
                                    <option value="BA">Bahia</option>
                                    <option value="CE">Ceará</option>
                                    <option value="DF">Distrito Federal</option>
                                    <option value="ES">Espírito Santo</option>
                                    <option value="GO">Goiás</option>
                                    <option value="MA">Maranhão</option>
                                    <option value="MT">Mato Grosso</option>
                                    <option value="MS">Mato Grosso do Sul</option>
                                    <option value="MG">Minas Gerais</option>
                                    <option value="PA">Pará</option>
                                    <option value="PB">Paraíba</option>
                                    <option value="PR">Paraná</option>
                                    <option value="PE">Pernambuco</option>
                                    <option value="PI">Piauí</option>
                                    <option value="RJ">Rio de Janeiro</option>
                                    <option value="RN">Rio Grande do Norte</option>
                                    <option value="RS">Rio Grande do Sul</option>
                                    <option value="RO">Rondônia</option>
                                    <option value="RR">Roraima</option>
                                    <option value="SC">Santa Catarina</option>
                                    <option value="SP">São Paulo</option>
                                    <option value="SE">Sergipe</option>
                                    <option value="TO">Tocantins</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_default" name="is_default" value="1">
                            <label class="form-check-label" for="edit_is_default">
                                Definir como endereço padrão
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .client-header-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        margin-bottom: 0;
    }

    .client-avatar img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border: 3px solid rgba(255,255,255,0.3);
    }

    .client-sidebar {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

    .address-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 1.5rem;
        height: 100%;
        transition: transform 0.3s ease;
    }

    .address-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    .address-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .address-header h5 {
        margin: 0;
        color: #2d3748;
    }

    .address-body {
        margin-bottom: 1rem;
    }

    .address-text {
        margin-bottom: 0.5rem;
        color: #4a5568;
        line-height: 1.5;
    }

    .address-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .empty-state i {
        font-size: 3rem;
        color: #cbd5e0;
        margin-bottom: 1rem;
    }

    .empty-state h5 {
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #718096;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .client-header-banner .row {
            text-align: center;
        }
        
        .client-sidebar {
            margin-bottom: 2rem;
        }

        .address-actions {
            justify-content: center;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // CEP Mask
    $('#cep, #edit_cep').on('input', function() {
        let value = this.value.replace(/\D/g, '');
        value = value.replace(/^(\d{5})(\d)/, '$1-$2');
        this.value = value;
    });

    // CEP Lookup
    $('#postal_code').on('blur', function() {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            lookupCEP(cep, '');
        }
    });

    $('#edit_postal_code').on('blur', function() {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            lookupCEP(cep, 'edit_');
        }
    });

    function lookupCEP(cep, prefix) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById(prefix + 'street').value = data.logradouro || '';
                    document.getElementById(prefix + 'neighborhood').value = data.bairro || '';
                    document.getElementById(prefix + 'city').value = data.localidade || '';
                    document.getElementById(prefix + 'state').value = data.uf || '';
                }
            })
            .catch(error => {
                console.error('Erro ao buscar CEP:', error);
            });
    }

    function editAddress(address) {
        document.getElementById('editAddressForm').action = `/cliente/enderecos/${address.id}`;
        document.getElementById('edit_name').value = address.name;
        document.getElementById('edit_postal_code').value = address.postal_code;
        document.getElementById('edit_street').value = address.street;
        document.getElementById('edit_number').value = address.number;
        document.getElementById('edit_complement').value = address.complement || '';
        document.getElementById('edit_neighborhood').value = address.neighborhood;
        document.getElementById('edit_city').value = address.city;
        document.getElementById('edit_state').value = address.state;
        document.getElementById('edit_is_default').checked = address.is_default;
        
        new bootstrap.Modal(document.getElementById('editAddressModal')).show();
    }
</script>
@endsection
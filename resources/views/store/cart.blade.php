@extends('layouts.blog')

@section('title', 'Carrinho de Compras')

@section('styles')
    <style>
        .cart-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            margin-top: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .product-image {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .quantity-input {
            width: 60px;
            text-align: center;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .product-image {
                width: 50px;
                height: 60px;
            }

            .quantity-input {
                width: 50px;
            }
        }
    </style>
@endsection

@section('content')


    <div class="container my-4">
        <div class="cart-container">
            <!-- Header -->
            <!-- Título do carrinho -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0">🛒 Seu Carrinho</h2>
                <span class="badge bg-secondary">{{ count($cartItems) }}
                    {{ count($cartItems) === 1 ? 'item' : 'itens' }}</span>
            </div>

            @if (count($cartItems) > 0)
                <!-- Cart Table -->
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Produto(s)</th>
                                <th>Quantidade</th>
                                <th>Entrega</th>
                                <th>Valor Unitário</th>
                                <th>Valor Total</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $itemId => $item)
                                <tr data-item-id="{{ $itemId }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item['product_image'] }}" alt="{{ $item['product_name'] }}"
                                                class="rounded me-3"
                                                style="width: 60px; height: 80px; object-fit: cover; border: 1px solid #dee2e6;">
                                            <div>
                                                <strong>{{ $item['product_name'] }}</strong><br>
                                                <small
                                                    class="text-muted">{{ $item['product_sku'] ?? 'Necessidade Pública' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm w-50"
                                            value="{{ $item['quantity'] }}" min="1"
                                            onchange="updateQuantity('{{ $itemId }}', this.value)">
                                    </td>
                                    <td>R$ {{ number_format($cartTotals['shipping'] / count($cartItems), 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($item['product_price'], 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</td>
                                    <td>
                                        <button onclick="removeItem('{{ $itemId }}')"
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
        </div>

        <!-- Summary Box -->
        <!-- Resumo do Pedido -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Resumo do Pedido</h5>

                <div class="d-flex justify-content-between mb-2">
                    <span>{{ count($cartItems) }} {{ count($cartItems) === 1 ? 'Produto' : 'Produtos' }}:</span>
                    <strong>R$ {{ number_format($cartTotals['subtotal'], 2, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Frete:</span>
                    <strong>R$ {{ number_format($cartTotals['shipping'], 2, ',', '.') }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Valor Total:</span>
                    <span>R$ {{ number_format($cartTotals['total'], 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('store.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-plus me-2"></i>Escolher + produtos
            </a>
            @auth('client')
                <a href="{{ route('store.checkout') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-shopping-cart me-2"></i>Finalizar Compra
                </a>
            @else
                <a href="{{ route('client.login') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-user me-2"></i>Entrar para finalizar
                </a>
            @endauth
           
        </div>

        <!-- Info Box -->
        <div class="mt-4 p-3 bg-light rounded">
            <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Informações Importantes</h6>
            <ul class="mb-0 ps-4">
                <li>Atenção: Atualmente o frete é fixo de acordo com a quantidade de itens do carrinho.</li>
                <li>Atenção: O prazo de entrega começa a contar a partir da aprovação do pagamento.</li>
            </ul>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <div class="fs-1 mb-3">🛒</div>
            <h2 class="fw-bold mb-3">Ops! Seu carrinho está vazio</h2>
            <p class="text-muted mb-4">Que tal explorar nossa seleção de produtos?</p>
            <a href="{{ route('store.index') }}" class="btn-primary">
                Descobrir Produtos Incríveis
            </a>
        </div>
        @endif
    </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function updateQuantity(itemId, newQty) {
            if (newQty < 1) return;

            Swal.fire({
                title: 'Atualizando...',
                text: 'Processando alteração',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("store.cart.update") }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    items: [{
                        id: itemId,
                        quantity: parseInt(newQty)
                    }]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Quantidade atualizada!',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao atualizar',
                        text: data.message || 'Tente novamente'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Não foi possível atualizar o carrinho'
                });
            });
        }

        function removeItem(itemId) {
            Swal.fire({
                title: 'Remover item?',
                text: 'Tem certeza que deseja remover este produto?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, remover!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Removendo...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('{{ route("store.cart.remove") }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            cart_item_id: itemId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Item removido!',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Atualiza localStorage
                                if (data.cart_totals && data.cart_totals.items_count !== undefined) {
                                    localStorage.setItem('cart_count', data.cart_totals.items_count);
                                }
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro ao remover',
                                text: data.message || 'Tente novamente'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Não foi possível remover o item'
                        });
                    });
                }
            });
        }
    </script>
@endsection

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'client_id',
        'status',
        'subtotal',
        'shipping_total',
        'tax_total',
        'discount_total',
        'total',
        'currency',
        'payment_method_id',
        'payment_status',
        'payment_transaction_id',
        'billing_address',
        'shipping_address',
        'customer_notes',
        'admin_notes',
        'shipped_at',
        'delivered_at',
        'tracking_info',
        'tracking_code',
        'tracking_url',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'total' => 'decimal:2',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'tracking_info' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Relacionamentos
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['delivered']);
    }

    // Métodos
    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->product_price;
        });
        
        // Calcular frete (implementar lógica de shipping rules)
        $this->shipping_total = $this->calculateShipping();
        
        // Calcular total
        $this->total = $this->subtotal + $this->shipping_total + $this->tax_total - $this->discount_total;
        
        $this->save();
    }

    private function calculateShipping()
    {
        // Implementar lógica de cálculo de frete baseada nas shipping rules
        return 10.00; // Valor fixo temporário
    }

    public function getStatusLabel()
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
            default => 'Desconhecido'
        };
    }

    public function getStatusColor()
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary',
            default => 'secondary'
        };
    }

    public function getPaymentStatusLabel()
    {
        return match($this->payment_status) {
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'paid' => 'Pago',
            'failed' => 'Falhou',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
            default => 'Desconhecido'
        };
    }

    /**
     * Check if order is shipped
     */
    public function isShipped(): bool
    {
        return $this->shipped_at !== null;
    }

    /**
     * Check if order is delivered
     */
    public function isDelivered(): bool
    {
        return $this->delivered_at !== null;
    }

    /**
     * Check if order has tracking information
     */
    public function hasTracking(): bool
    {
        return !empty($this->tracking_code) || !empty($this->tracking_url);
    }

    /**
     * Get tracking status
     */
    public function getTrackingStatus(): string
    {
        if ($this->isDelivered()) {
            return 'delivered';
        }
        
        if ($this->isShipped()) {
            return 'shipped';
        }
        
        if ($this->status === 'processing') {
            return 'processing';
        }
        
        return 'pending';
    }

    /**
     * Mark order as shipped
     */
    public function markAsShipped(?string $trackingCode = null, ?string $trackingUrl = null): void
    {
        $this->update([
            'status' => 'shipped',
            'shipped_at' => now(),
            'tracking_code' => $trackingCode,
            'tracking_url' => $trackingUrl,
        ]);
    }

    /**
     * Mark order as delivered
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Gera código PIX para pagamento do pedido
     */
    public function generatePixCode()
    {
        $pixService = app(\App\Services\PixService::class);
        
        try {
            $pixData = $pixService->generatePixCode(
                amount: $this->total,
                description: "Pedido {$this->order_number}",
                txid: "ORD{$this->id}" . time()
            );
            
            // Armazenar dados do PIX no billing_address ou criar campo separado
            $billingAddress = $this->billing_address ?? [];
            $billingAddress['pix_data'] = [
                'payload' => $pixData['payload'],
                'qr_code' => $pixData['qr_code_base64'],
                'pix_key' => $pixData['pix_key'] ?? null,
                'beneficiary_name' => $pixData['beneficiary_name'] ?? null,
                'generated_at' => now()->toIso8601String(),
            ];
            
            $this->update(['billing_address' => $billingAddress]);
            
            return $pixData;
        } catch (\Exception $e) {
            logger()->error('Erro ao gerar código PIX para pedido: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retorna o QR Code PIX
     */
    public function getPixQrCode()
    {
        return $this->billing_address['pix_data']['qr_code'] ?? null;
    }

    /**
     * Retorna o payload PIX
     */
    public function getPixPayload()
    {
        return $this->billing_address['pix_data']['payload'] ?? null;
    }

    /**
     * Verifica se tem código PIX gerado
     */
    public function hasPixCode()
    {
        return isset($this->billing_address['pix_data']['payload']);
    }
}
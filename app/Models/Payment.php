<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = [
        'transaction_id',
        'gateway',
        'gateway_transaction_id',
        'payment_method',
        'payable_type',
        'payable_id',
        'amount',
        'currency',
        'status',
        'payer_name',
        'payer_email',
        'payer_phone',
        'payer_document',
        'pix_code',
        'qr_code_url',
        'qr_code_base64',
        'checkout_url',
        'gateway_response',
        'metadata',
        'expires_at',
        'paid_at',
        'failure_reason',
        'installments',
        'fee_amount',
        'net_amount',
        'ip_address',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'installments' => 'integer',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    const PAYMENT_METHODS = [
        'pix' => 'PIX',
        'credit_card' => 'Cartão de Crédito',
        'debit_card' => 'Cartão de Débito',
        'bank_slip' => 'Boleto Bancário',
        'bank_transfer' => 'Transferência Bancária',
    ];

    // Boot
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($payment) {
            if (empty($payment->transaction_id)) {
                $payment->transaction_id = self::generateTransactionId();
            }
        });
    }

    // Relationships
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeByGateway($query, $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Aguardando Pagamento',
            self::STATUS_PROCESSING => 'Processando',
            self::STATUS_APPROVED => 'Aprovado',
            self::STATUS_REJECTED => 'Rejeitado',
            self::STATUS_CANCELLED => 'Cancelado',
            self::STATUS_REFUNDED => 'Estornado',
            default => 'Desconhecido'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            self::STATUS_REFUNDED => 'dark',
            default => 'secondary'
        };
    }

    public function getPaymentMethodLabelAttribute()
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    public function getGatewayLabelAttribute()
    {
        return match($this->gateway) {
            'mercadopago' => 'MercadoPago',
            'efipay' => 'EFI Pay',
            'pagseguro' => 'PagSeguro',
            default => ucfirst($this->gateway)
        };
    }

    // Methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast() && $this->isPending();
    }

    public function markAsApproved($gatewayTransactionId = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'paid_at' => now(),
            'gateway_transaction_id' => $gatewayTransactionId ?? $this->gateway_transaction_id,
        ]);
    }

    public function markAsRejected($reason = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'failure_reason' => $reason,
        ]);
    }

    public function markAsCancelled()
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    public static function generateTransactionId()
    {
        do {
            $id = 'TXN' . Str::upper(Str::random(8)) . time();
        } while (self::where('transaction_id', $id)->exists());
        
        return $id;
    }

    public function updateFromGatewayResponse($response)
    {
        $currentResponse = $this->gateway_response ?? [];
        $this->update([
            'gateway_response' => array_merge($currentResponse, $response)
        ]);
    }

    public function calculateNetAmount()
    {
        if ($this->fee_amount) {
            $this->update([
                'net_amount' => $this->amount - $this->fee_amount
            ]);
        }
    }
}

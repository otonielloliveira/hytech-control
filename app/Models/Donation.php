<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'amount',
        'message',
        'payment_method',
        'payment_id',
        'pix_code',
        'status',
        'paid_at',
        'expires_at',
        'payment_data',
        'ip_address',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'payment_data' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    // Relationships
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function latestPayment()
    {
        return $this->payments()->latest()->first();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
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
            self::STATUS_PAID => 'Pago',
            self::STATUS_CANCELLED => 'Cancelado',
            self::STATUS_EXPIRED => 'Expirado',
            default => 'Desconhecido'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PAID => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_EXPIRED => 'secondary',
            default => 'secondary'
        };
    }

    // Methods
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function markAsPaid($paymentId = null)
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
            'payment_id' => $paymentId ?? $this->payment_id,
        ]);
    }

    public function markAsExpired()
    {
        if ($this->isPending()) {
            $this->update(['status' => self::STATUS_EXPIRED]);
        }
    }

    public function generatePixCode()
    {
        $pixService = app(\App\Services\PixService::class);
        
        try {
            $pixData = $pixService->generatePixCode(
                amount: $this->amount,
                description: "Doacao #{$this->id}",
                txid: "DON{$this->id}" . time()
            );
            
            $this->update([
                'pix_code' => $pixData['payload'],
                'payment_data' => array_merge($this->payment_data ?? [], [
                    'pix_qr_code' => $pixData['qr_code_base64'],
                    'pix_key' => $pixData['pix_key'] ?? null,
                    'beneficiary_name' => $pixData['beneficiary_name'] ?? null,
                    'generated_at' => now()->toIso8601String(),
                ]),
            ]);
            
            return $pixData;
        } catch (\Exception $e) {
            logger()->error('Erro ao gerar código PIX para doação: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getPixQrCode()
    {
        return $this->payment_data['pix_qr_code'] ?? null;
    }

    public function hasPixCode()
    {
        return !empty($this->pix_code);
    }
}
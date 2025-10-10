<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        // Aqui você pode integrar com seu gateway de pagamento favorito
        // Por enquanto, vou usar um formato básico do PIX
        $pixKey = config('services.pix.key', 'seu@email.com'); // Configure no .env
        $merchantName = config('services.pix.merchant_name', 'Seu Projeto');
        $merchantCity = config('services.pix.merchant_city', 'Sua Cidade');
        
        // Gerar código PIX básico (você pode usar bibliotecas específicas)
        $pixCode = $this->generateBasicPixCode($pixKey, $merchantName, $merchantCity, $this->amount);
        
        $this->update(['pix_code' => $pixCode]);
        
        return $pixCode;
    }

    private function generateBasicPixCode($pixKey, $merchantName, $merchantCity, $amount)
    {
        // Implementação básica do PIX - recomendo usar uma biblioteca específica
        return "00020126" . 
               "580014br.gov.bcb.pix" . 
               "0136" . $pixKey . 
               "5204" . "0000" . 
               "5303986" . 
               "54" . sprintf("%02d", strlen($amount)) . $amount . 
               "5802BR" . 
               "59" . sprintf("%02d", strlen($merchantName)) . $merchantName . 
               "60" . sprintf("%02d", strlen($merchantCity)) . $merchantCity . 
               "6304" . "0000"; // CRC16 simplificado
    }
}
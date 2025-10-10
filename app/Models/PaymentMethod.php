<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'gateway',
        'config',
        'is_active',
        'sort_order',
        'fee_percentage',
        'fee_fixed',
        'supported_currencies',
    ];

    protected $casts = [
        'config' => 'array',
        'supported_currencies' => 'array',
        'is_active' => 'boolean',
        'fee_percentage' => 'decimal:2',
        'fee_fixed' => 'decimal:2',
    ];

    // Relacionamentos
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Métodos
    public function calculateFee($amount)
    {
        $percentageFee = ($amount * $this->fee_percentage) / 100;
        return $percentageFee + $this->fee_fixed;
    }

    public function getGatewayConfig($key = null)
    {
        if ($key) {
            return $this->config[$key] ?? null;
        }
        
        return $this->config;
    }

    public function setGatewayConfig($key, $value)
    {
        $config = $this->config ?? [];
        $config[$key] = $value;
        $this->config = $config;
        $this->save();
    }

    public static function getAvailableGateways()
    {
        return [
            'asaas' => 'Asaas',
            'mercadopago' => 'Mercado Pago',
            'pagseguro' => 'PagSeguro',
            'pix' => 'PIX',
            'boleto' => 'Boleto Bancário',
            'card' => 'Cartão de Crédito/Débito',
        ];
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;

class PaymentGatewayConfig extends Model
{
    protected $fillable = [
        'gateway',
        'name',
        'is_active',
        'is_sandbox',
        'credentials',
        'settings',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_sandbox' => 'boolean',
        'credentials' => 'array',
        'settings' => 'array',
        'sort_order' => 'integer',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderedBySort($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    protected function credentials(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return [];
                $decoded = json_decode($value, true);
                
                // Descriptografar credenciais sensíveis
                if (isset($decoded['encrypted']) && $decoded['encrypted']) {
                    foreach (['client_secret', 'access_token', 'private_key'] as $key) {
                        if (isset($decoded[$key])) {
                            try {
                                $decoded[$key] = Crypt::decryptString($decoded[$key]);
                            } catch (\Exception $e) {
                                // Se falhar na descriptografia, manter o valor original
                            }
                        }
                    }
                }
                
                return $decoded;
            },
            set: function ($value) {
                if (!is_array($value)) return $value;
                
                // Criptografar credenciais sensíveis
                $encrypted = $value;
                foreach (['client_secret', 'access_token', 'private_key'] as $key) {
                    if (isset($value[$key]) && !empty($value[$key])) {
                        $encrypted[$key] = Crypt::encryptString($value[$key]);
                    }
                }
                $encrypted['encrypted'] = true;
                
                return json_encode($encrypted);
            }
        );
    }

    // Methods
    public static function getActiveGateway()
    {
        return self::active()->orderedBySort()->first();
    }

    public static function getAvailableGateways()
    {
        return [
            'asaas' => 'ASAAS',
            'pix_manual' => 'PIX Manual (Chave Própria)',
            'mercadopago' => 'MercadoPago',
            'efipay' => 'EFI Pay (PIX)',
            'pagseguro' => 'PagSeguro',
        ];
    }

    public function getCredential($key, $default = null)
    {
        return $this->credentials[$key] ?? $default;
    }

    public function getSetting($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    public function isConfigured()
    {
        $requiredFields = $this->getRequiredCredentials();
        
        foreach ($requiredFields as $field) {
            if (empty($this->getCredential($field))) {
                return false;
            }
        }
        
        return true;
    }

    public function getRequiredCredentials()
    {
        return match($this->gateway) {
            'asaas' => ['api_key'],
            'pix_manual' => ['pix_key', 'pix_key_type'],
            'mercadopago' => ['access_token'],
            'efipay' => ['client_id', 'client_secret'],
            'pagseguro' => ['email', 'token'],
            default => []
        };
    }

    public function getStatusBadgeColor()
    {
        if (!$this->is_active) return 'gray';
        if (!$this->isConfigured()) return 'warning';
        return 'success';
    }

    public function getStatusLabel()
    {
        if (!$this->is_active) return 'Inativo';
        if (!$this->isConfigured()) return 'Não Configurado';
        return $this->is_sandbox ? 'Ativo (Sandbox)' : 'Ativo (Produção)';
    }
}

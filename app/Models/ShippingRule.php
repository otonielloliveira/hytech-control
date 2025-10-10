<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'base_cost',
        'cost_per_kg',
        'min_weight',
        'max_weight',
        'min_order_value',
        'max_order_value',
        'locations',
        'is_active',
        'sort_order',
        'estimated_days_min',
        'estimated_days_max',
    ];

    protected $casts = [
        'locations' => 'array',
        'base_cost' => 'decimal:2',
        'cost_per_kg' => 'decimal:2',
        'min_weight' => 'decimal:3',
        'max_weight' => 'decimal:3',
        'min_order_value' => 'decimal:2',
        'max_order_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Métodos
    public function calculateCost($orderValue, $weight, $location = null)
    {
        if (!$this->isApplicable($orderValue, $weight, $location)) {
            return null;
        }

        $cost = $this->base_cost;

        switch ($this->type) {
            case 'weight_based':
                if ($this->cost_per_kg && $weight) {
                    $cost += $weight * $this->cost_per_kg;
                }
                break;
                
            case 'price_based':
                // Frete grátis ou desconto baseado no valor
                if ($orderValue >= 100) { // Exemplo: frete grátis acima de R$ 100
                    $cost = 0;
                }
                break;
                
            case 'location_based':
                // Diferentes custos por localização
                if ($location && $this->locations) {
                    // Implementar lógica de localização
                }
                break;
        }

        return $cost;
    }

    public function isApplicable($orderValue, $weight, $location = null)
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->min_order_value && $orderValue < $this->min_order_value) {
            return false;
        }

        if ($this->max_order_value && $orderValue > $this->max_order_value) {
            return false;
        }

        if ($this->min_weight && $weight < $this->min_weight) {
            return false;
        }

        if ($this->max_weight && $weight > $this->max_weight) {
            return false;
        }

        return true;
    }

    public function getEstimatedDelivery()
    {
        $min = now()->addDays($this->estimated_days_min);
        $max = now()->addDays($this->estimated_days_max);
        
        return [
            'min' => $min,
            'max' => $max,
            'range' => $min->format('d/m') . ' - ' . $max->format('d/m')
        ];
    }

    public static function getAvailableTypes()
    {
        return [
            'fixed' => 'Valor Fixo',
            'weight_based' => 'Baseado no Peso',
            'price_based' => 'Baseado no Valor',
            'location_based' => 'Baseado na Localização',
        ];
    }
}
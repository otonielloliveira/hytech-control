<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'product_price',
        'quantity',
        'total',
        'product_meta',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'total' => 'decimal:2',
        'product_meta' => 'array',
    ];

    // Relacionamentos
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Métodos
    public function calculateTotal()
    {
        $this->total = $this->quantity * $this->product_price;
        $this->save();
    }
}
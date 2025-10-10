<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'sku',
        'price',
        'sale_price',
        'stock_quantity',
        'manage_stock',
        'in_stock',
        'weight',
        'length',
        'width',
        'height',
        'images',
        'gallery',
        'featured',
        'status',
        'sort_order',
        'meta_data',
    ];

    protected $casts = [
        'images' => 'array',
        'gallery' => 'array',
        'meta_data' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:3',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'featured' => 'boolean',
        'manage_stock' => 'boolean',
        'in_stock' => 'boolean',
    ];

    // Relacionamentos
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    // Métodos
    public function getEffectivePrice()
    {
        return $this->sale_price ?? $this->price;
    }

    public function isOnSale()
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function getDiscountPercentage()
    {
        if (!$this->isOnSale()) {
            return 0;
        }
        
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getMainImage()
    {
        return $this->images[0] ?? '/images/placeholder-product.jpg';
    }

    public function decreaseStock($quantity)
    {
        if ($this->manage_stock) {
            $this->stock_quantity = max(0, $this->stock_quantity - $quantity);
            $this->in_stock = $this->stock_quantity > 0;
            $this->save();
        }
    }

    public function increaseStock($quantity)
    {
        if ($this->manage_stock) {
            $this->stock_quantity += $quantity;
            $this->in_stock = true;
            $this->save();
        }
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'flash_sale_price',
        'is_flash_sale',
        'stock',
    ];

    protected $casts = [
        'is_flash_sale' => 'boolean',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // dapat harga efektif (jika flash sale aktif, gunakan harga flash sale, jika tidak gunakan harga normal)
    public function getEffectivePrice(): float {
        if ($this->is_flash_sale && $this->flash_sale_price !== null) {
            return $this->flash_sale_price;
        }
        return $this->price;
    }
}

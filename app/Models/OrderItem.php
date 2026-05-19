<?php
// app/Models/OrderItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'price', 'subtotal','variant_id','image_path'
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order()   { return $this->belongsTo(Order::class); }
    public function product() { return $this->belongsTo(Product::class); }

    // TAMBAHKAN INI: Relasi ke Varian
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
    public function getDisplayImageAttribute()
    {
    // Cek image_path di varian, jika kosong ambil image di produk
    $path = $this->variant->image_path ?? $this->product->image ?? $this->product->image_path;
    
    return $path ? asset('storage/products/gallery/' . basename($path)) : null;
    }
}
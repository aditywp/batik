<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 
        'product_id', 
        'quantity', 
        'price', 
        'subtotal', 
        'variant_id', 
        'image_path',
        // --- KOLOM SNAPSHOT BARU ---
        'product_name_snapshot', 
        'price_snapshot', 
        'image_snapshot'
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'subtotal'       => 'decimal:2',
        'price_snapshot' => 'decimal:2',
    ];

    // Relasi Varian
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // Relasi Order
    public function order() 
    { 
        return $this->belongsTo(Order::class); 
    }

    // Relasi Produk (Tanpa withTrashed untuk menghindari BadMethodCallException)
    public function product() 
    { 
        return $this->belongsTo(Product::class); 
    }
    
    // Aksesoris Gambar: Cek snapshot dulu, jika kosong baru cari ke varian/produk
    public function getDisplayImageAttribute()
    {
        $path = $this->image_snapshot ?? $this->variant?->image_path ?? $this->product?->image ?? $this->product?->image_path;
        return $path ? asset('storage/products/gallery/' . basename($path)) : null;
    }
}
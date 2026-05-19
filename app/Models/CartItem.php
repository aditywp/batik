<?php
// app/Models/CartItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    /**
     * Atribut yang dapat diisi melalui mass assignment.
     * Pastikan 'variant_id' ada di sini agar data motif/ukuran bisa tersimpan.
     */
    protected $fillable = [
        'user_id', 
        'product_id', 
        'variant_id', 
        'quantity'
    ];

    /**
     * Relasi ke produk utama.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke spesifik varian (motif dan ukuran).
     * Ini penting agar di halaman keranjang kita bisa tahu motif/ukuran apa yang dibeli.
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Relasi ke pemilik keranjang (User).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
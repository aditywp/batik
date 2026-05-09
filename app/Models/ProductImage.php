<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     * Penting agar fungsi store di Controller tidak error.
     */
    protected $fillable = ['product_id', 'image_path', 'is_primary', 'sort_order'];

    /**
     * Relasi balik ke Product (Many to One).
     * Setiap gambar dimiliki oleh satu produk tertentu.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
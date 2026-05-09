<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     * Sesuaikan dengan kolom yang ada di file migrasi kamu.
     */
    protected $fillable = [
        'product_id',
        'motif',
        'image_path',
        'size',
        'price',
        'stock',
    ];

    /**
     * Relasi balik ke Produk (Belongs To)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal.
     * name: Nama kategori (contoh: Batik Tulis)
     * slug: Versi URL (contoh: batik-tulis)
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Relasi ke Product (One to Many).
     * Satu kategori bisa memiliki banyak produk batik.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
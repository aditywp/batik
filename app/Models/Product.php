<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Gunakan penamaan tunggal (PascalCase) agar konsisten
use App\Models\Category; 
use App\Models\ProductImage;
use App\Models\ProductVariant;

class Product extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal.
     * WAJIB ada agar fungsi Product::create() di Controller berjalan.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
        'category_id',
        'collection',
    ];

    // Relasi ke Category (Many to One)
    public function category()
    {
        // Pastikan nama class-nya Category (tunggal), bukan categories
        return $this->belongsTo(Category::class);
    }

    // Relasi ke ProductImage (One to Many)
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Relasi ke Review (One to Many)
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Relasi ke CartItem (One to Many)
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Relasi ke OrderItem (One to Many)
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Helper untuk total stok otomatis
    public function getTotalStockAttribute()
    {
        return $this->variants->sum('stock');
    }
}
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // 1. Tambahkan ini
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 2. Tambahkan 'implements MustVerifyEmail' di class
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // Helper: cek apakah user adalah admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Relasi
    public function orders()    { return $this->hasMany(Order::class); }
    public function reviews()   { return $this->hasMany(Review::class); }
    
    public function cartItems()
    {
        return $this->hasMany(\App\Models\CartItem::class);
    }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'user_vouchers')
                    ->withPivot('id', 'is_used', 'used_at')
                    ->withTimestamps();
    }

    // Logika Otomatis saat user baru dibuat
    protected static function booted()
    {
        static::created(function ($user) {
            // Ambil waktu sekarang
            $now = \Carbon\Carbon::now('Asia/Jakarta');

            // PERBAIKAN: Pastikan Welcome Voucher yang dibagikan masih aktif dan belum expired
            $welcomeVoucher = \App\Models\Voucher::where('is_welcome_voucher', true)
                                ->where('is_active', true)
                                ->where(function($query) use ($now) {
                                    $query->whereNull('valid_until')
                                          ->orWhere('valid_until', '>=', $now);
                                })
                                ->first();

            if ($welcomeVoucher) {
                $user->vouchers()->attach($welcomeVoucher->id);
            }
        });
    }
}
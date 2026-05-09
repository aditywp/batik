<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
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

    // Relasi (sudah didefinisikan di fase sebelumnya)
    public function orders()    { return $this->hasMany(Order::class); }
    public function reviews()   { return $this->hasMany(Review::class); }
    // app/Models/User.php — pastikan ini ada
    public function cartItems()
    {
        return $this->hasMany(\App\Models\CartItem::class);
    }
}
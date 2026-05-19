<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_code', 'snap_token', 'payment_url',
        'midtrans_transaction_id', 'subtotal', 'shipping_cost',
        'total', 'status', 'shipping_address', 'courier',
        'courier_service', 'shipping_weight', 'payment_method',
        'payment_status', 'paid_at',
        'tracking_number', // WAJIB ADA DI SINI
    ];

    protected $casts = [
        'paid_at'       => 'datetime',
        'subtotal'      => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total'         => 'decimal:2',
    ];

    // ==========================================
    // RELASI
    // ==========================================

    public function user()    { return $this->belongsTo(User::class); }
    public function items()   { return $this->hasMany(OrderItem::class); }
    public function reviews() { return $this->hasMany(Review::class); }

    // ==========================================
    // STATUS HELPERS
    // ==========================================

    // Semua kemungkinan status beserta label & warna Tailwind-nya
    public static function statusList(): array
    {
        return [
            'pending'    => ['label' => 'Pending',     'color' => 'amber'],
            'processing' => ['label' => 'Diproses',    'color' => 'blue'],
            'shipped'    => ['label' => 'Dikirim',     'color' => 'purple'],
            'delivered'  => ['label' => 'Selesai',     'color' => 'green'],
            'cancelled'  => ['label' => 'Dibatalkan',  'color' => 'red'],
        ];
    }

    public function statusLabel(): string
    {
        return self::statusList()[$this->status]['label'] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::statusList()[$this->status]['color'] ?? 'gray';
    }

    // Status apa saja yang boleh dipilih selanjutnya dari status saat ini
    public function allowedNextStatuses(): array
    {
        return match ($this->status) {
            'pending'    => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped'    => ['delivered'],
            'delivered'  => [],
            'cancelled'  => [],
            default      => [],
        };
    }

    public function isCancellable(): bool
    {
        return in_array('cancelled', $this->allowedNextStatuses());
    }
}
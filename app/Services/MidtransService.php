<?php
// app/Services/MidtransService.php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * Buat Snap Token untuk order tertentu
     */
    public function createSnapToken(Order $order): string
    {
        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_code,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email'      => $order->user->email,
            ],
            'item_details' => $order->items->map(fn($item) => [
                'id'       => (string) $item->product_id,
                'price'    => (int) $item->price,
                'quantity' => $item->quantity,
                'name'     => substr($item->product->name, 0, 50), // Midtrans max 50 char
            ])->toArray(),
            'callbacks' => [
                'finish' => route('customer.payment.finish'),
            ],
        ];

        // Tambahkan ongkir sebagai item tersendiri jika ada
        if ($order->shipping_cost > 0) {
            $params['item_details'][] = [
                'id'       => 'SHIPPING',
                'price'    => (int) $order->shipping_cost,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim ' . strtoupper($order->courier ?? ''),
            ];
        }

        return Snap::getSnapToken($params);
    }

    /**
     * Verifikasi notifikasi dari Midtrans (webhook)
     */
    public function verifyNotification(array $payload): bool
    {
        $signatureKey = hash('sha512',
            $payload['order_id'] .
            $payload['status_code'] .
            $payload['gross_amount'] .
            config('services.midtrans.server_key')
        );

        return $signatureKey === $payload['signature_key'];
    }
}
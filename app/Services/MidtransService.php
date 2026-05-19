<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        // DEBUG (hapus kalau sudah normal)
        // dd(config('services.midtrans'));

        Config::$serverKey = config('services.midtrans.server_key');

        Config::$isProduction = config(
            'services.midtrans.is_production',
            false
        );

        Config::$isSanitized = true;

        Config::$is3ds = true;
    }

    /**
     * CREATE SNAP TOKEN
     */
    public function createSnapToken(Order $order): string
    {
        $params = [

            // TRANSACTION
            'transaction_details' => [

                'order_id' => $order->order_code,

                'gross_amount' => (int) $order->total,
            ],

            // CUSTOMER
            'customer_details' => [

                'first_name' => $order->user->name ?? 'Customer',

                'email' => $order->user->email ?? 'customer@email.com',
            ],

            // ITEMS
            'item_details' => $order->items->map(function ($item) {

                return [

                    'id' => (string) $item->product_id,

                    'price' => (int) $item->price,

                    'quantity' => (int) $item->quantity,

                    'name' => substr(
                        $item->product->name ?? 'Product',
                        0,
                        50
                    ),
                ];

            })->toArray(),

            // CALLBACK
            'callbacks' => [

                'finish' => route('customer.checkout.finish'),
            ],
        ];

        /**
         * TAMBAH ONGKIR
         */
        if ($order->shipping_cost > 0) {

            $params['item_details'][] = [

                'id' => 'SHIPPING',

                'price' => (int) $order->shipping_cost,

                'quantity' => 1,

                'name' => 'Ongkos Kirim',
            ];
        }

        /**
         * GET SNAP TOKEN
         */
        return Snap::getSnapToken($params);
    }

    /**
     * VERIFY MIDTRANS NOTIFICATION
     */
    public function verifyNotification(array $payload): bool
    {
        $signatureKey = hash(
            'sha512',

            $payload['order_id'] .

            $payload['status_code'] .

            $payload['gross_amount'] .

            config('services.midtrans.server_key')
        );

        return $signatureKey === $payload['signature_key'];
    }
}
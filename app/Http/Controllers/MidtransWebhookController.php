<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request, MidtransService $midtrans)
    {
        $payload = $request->all();

        // 1. Verifikasi Signature (Hanya butuh payload jika logika hash ada di service)
        if (! $midtrans->verifyNotification($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 2. Cari Order (Gunakan class Order, bukan orders)
        $order = Order::where('order_code', $payload['order_id'])->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // 3. Update status
        $transactionStatus = $payload['transaction_status'];
        
        match ($transactionStatus) {
            'capture', 'settlement' => $order->update([
                'payment_status'           => 'paid',
                'status'                   => 'processing',
                'midtrans_transaction_id'  => $payload['transaction_id'],
                'paid_at'                  => now(),
            ]),
            'pending' => $order->update(['payment_status' => 'unpaid']),
            'deny', 'cancel', 'expire' => $order->update([
                'payment_status' => 'unpaid',
                'status'         => 'cancelled',
            ]),
            default => null,
        };

        return response()->json(['message' => 'OK']);
    }
}
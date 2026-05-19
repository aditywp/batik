<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request, MidtransService $midtrans)
    {
        $payload = $request->all();

        // 1. Verifikasi Signature
        if (! $midtrans->verifyNotification($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 2. Cari Order berdasarkan kode
        $order = Order::where('order_code', $payload['order_id'])->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // 3. Ambil status transaksi dari Midtrans
        $transactionStatus = $payload['transaction_status'];
        
        // Gunakan DB Transaction agar jika gagal potong stok, status order tidak berubah (keamanan data)
        DB::transaction(function () use ($transactionStatus, $order, $payload) {
            
            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                
                // Cek dulu apakah sebelumnya sudah paid? (mencegah double decrement stok)
                if ($order->payment_status !== 'paid') {
                    
                    // A. Update Status Order
                    $order->update([
                        'payment_status'           => 'paid',
                        'status'                   => 'processing',
                        'midtrans_transaction_id'  => $payload['transaction_id'],
                        'paid_at'                  => now(),
                    ]);

                    // B. LOGIKA POTONG STOK (SINKRONISASI DUA TABEL)
                    foreach ($order->items as $item) {
                        
                        // 1. Potong stok di tabel product_variants (Agar detail produk sinkron)
                        if ($item->variant_id) {
                            $variant = ProductVariant::find($item->variant_id);
                            if ($variant) {
                                $variant->decrement('stock', $item->quantity);
                            }
                        }

                        // 2. Potong stok di tabel products (Agar daftar produk/admin sinkron)
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->decrement('stock', $item->quantity);
                        }
                    }
                }

            } elseif ($transactionStatus == 'pending') {
                $order->update(['payment_status' => 'unpaid']);
                
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $order->update([
                    'payment_status' => 'unpaid',
                    'status'         => 'cancelled',
                ]);
            }
        });

        return response()->json(['message' => 'OK']);
    }
}
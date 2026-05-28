<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        // 1. Query dasar untuk pesanan milik user yang sedang login
        $query = \App\Models\Order::where('user_id', auth()->id())
                    ->with(['items.product.category', 'items.variant'])
                    ->latest();

        // 2. Fitur Cari Berdasarkan Order Code / ID Pesanan
        if ($request->filled('search')) {
            $query->where('order_code', 'like', '%' . $request->search . '%');
        }

        // 3. Fitur Filter Berdasarkan Tanggal Pesanan
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // 4. Eksekusi Pagination (5 data per halaman) dan pertahankan query string URL
        $orders = $query->paginate(5)->withQueryString();

        return view('customer.orders.index', compact('orders'));
    }

    public function show($order_code)
    {
        // 1. Ambil data pesanan dasar milik pelanggan
        $order = Order::where('order_code', $order_code)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'items.variant'])
            ->firstOrFail();

        // 2. OTOMATISASI SYNC: Jika status masih unpaid/pending, langsung jemput status asli ke server Midtrans
        if ($order->payment_status === 'unpaid' || $order->payment_status === 'pending') {
            try {
                \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
                \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);

                $status = \Midtrans\Transaction::status($order->order_code);
                $midtransStatus = $status->transaction_status;

                if (in_array($midtransStatus, ['expire', 'cancel', 'deny'])) {
                    $order->update([
                        'payment_status'          => 'cancelled',
                        'status'                  => 'cancelled',
                        'midtrans_transaction_id' => $status->transaction_id ?? null,
                        'payment_method'          => $status->payment_type ?? null,
                    ]);
                } elseif ($midtransStatus == 'settlement' || $midtransStatus == 'capture') {
                    $order->update([
                        'payment_status'          => 'paid',
                        'status'                  => 'processing',
                        'midtrans_transaction_id' => $status->transaction_id ?? null,
                        'payment_method'          => $status->payment_type ?? null,
                        'paid_at'                 => now(),
                    ]);
                }
            } catch (\Exception $e) {
                // Abaikan jika token belum diapa-apakan atau belum terdaftar di server Midtrans
            }
        }

        return view('customer.orders.show', compact('order'));
    }

    public function complete(\App\Models\Order $order)
    {
        // Keamanan tambahan: Pastikan pesanan ini memang benar milik customer yang sedang login
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Tindakan ilegal.');
        }

        // Pastikan status lamanya adalah shipped baru bisa diselesaikan
        if ($order->status === 'shipped') {
            $order->update([
                'status' => 'delivered'
            ]);

            return redirect()->back()->with('success', 'Terima kasih! Pesanan Anda telah selesai.');
        }

        return redirect()->back()->with('error', 'Status pesanan tidak valid.');
    }

    /**
     * FITUR REAL-TIME SINKRONISASI MIDTRANS GATEWAY (PULL SYSTEM)
     * Mengatasi kendala localhost (127.0.0.1) yang tidak bisa ditembak oleh webhook Midtrans luar.
     */
    public function syncMidtransStatus($id)
    {
        // Set konfigurasi rahasia Core API Server Sandbox Midtrans kamu
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        
        // Cari nota transaksi target
        $order = \App\Models\Order::findOrFail($id);

        try {
            // Tembak server API Midtrans secara langsung untuk verifikasi status invoice asli
            $status = \Midtrans\Transaction::status($order->order_code);
            $midtransStatus = $status->transaction_status;

            // Logika pencocokan status: Menyimpan data mutasi agar kolom internal DB tidak null lagi
            if (in_array($midtransStatus, ['expire', 'cancel', 'deny'])) {
                $order->update([
                    'payment_status'          => 'cancelled',
                    'status'                  => 'cancelled',
                    'midtrans_transaction_id' => $status->transaction_id ?? null,
                    'payment_method'          => $status->payment_type ?? null,
                ]);
            } elseif ($midtransStatus == 'settlement' || $midtransStatus == 'capture') {
                $order->update([
                    'payment_status'          => 'paid',
                    'status'                  => 'processing',
                    'midtrans_transaction_id' => $status->transaction_id ?? null,
                    'payment_method'          => $status->payment_type ?? null,
                    'paid_at'                 => now(),
                ]);
            }
        } catch (\Exception $e) {
            // Tangani jika invoice belum sempat dibuat atau token belum terekam di server Midtrans
        }

        //Kembalikan customer ke halaman detail tanda terima yang sudah ter-update otomatis
        return redirect()->route('customer.orders.show', $order->order_code);
    }
}
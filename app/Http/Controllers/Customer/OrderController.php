<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // ======================================================================
        // AUTOMATIC REAL-TIME SYNC FOR INDEX PAGE (MASS PULL SYSTEM)
        // Memeriksa dan memperbarui status pesanan yang masih 'unpaid' secara massal
        // ======================================================================
        foreach ($orders as $order) {
            if ($order->payment_status === 'unpaid' || $order->payment_status === 'pending') {
                try {
                    \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
                    \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);

                    $status = \Midtrans\Transaction::status($order->order_code);
                    $midtransStatus = $status->transaction_status;

                    if (in_array($midtransStatus, ['expire', 'cancel', 'deny'])) {
                        // KUNCI LOGISTIK: Kembalikan stok jika status berubah ke cancelled untuk pertama kali
                        if ($order->status !== 'cancelled') {
                            foreach ($order->items as $item) {
                                if ($item->variant_id) {
                                    \App\Models\ProductVariant::where('id', $item->variant_id)->increment('stock', $item->quantity);
                                }
                                \App\Models\Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                            }

                            // KEMBALIKAN VOUCHER KE DOMPET
                            if ($order->user_voucher_id) {
                                DB::table('user_vouchers')->where('id', $order->user_voucher_id)->update([
                                    'is_used' => false,
                                    'used_at' => null
                                ]);
                            }
                        }

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
                    // Abaikan jika order belum terbentuk di server sandbox Midtrans
                }
            }
        }

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
                    // KUNCI LOGISTIK: Kembalikan stok jika status berubah ke cancelled untuk pertama kali
                    if ($order->status !== 'cancelled') {
                        foreach ($order->items as $item) {
                            if ($item->variant_id) {
                                \App\Models\ProductVariant::where('id', $item->variant_id)->increment('stock', $item->quantity);
                            }
                            \App\Models\Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                        }

                        // KEMBALIKAN VOUCHER KE DOMPET
                        if ($order->user_voucher_id) {
                            DB::table('user_vouchers')->where('id', $order->user_voucher_id)->update([
                                'is_used' => false,
                                'used_at' => null
                            ]);
                        }
                    }

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

            // ==========================================
            // LOGIKA PENAMBAHAN POIN
            // Rumus: Total belanja / 10.000. (Contoh belanja 50.000 = 5 Poin)
            // ==========================================
            $earnedPoints = floor($order->total / 10000);

            if ($earnedPoints > 0) {
                $order->user->increment('points', $earnedPoints);
            }

            return redirect()->back()->with('success', "Terima kasih! Pesanan Anda telah selesai. Anda mendapatkan tambahan {$earnedPoints} Poin!");
        }

        return redirect()->back()->with('error', 'Status pesanan tidak valid.');
    }

    /**
     * FITUR PEMBATALAN TRANSAKSI MANUAL OLEH CUSTOMER
     * Menghubungkan tombol pembatalan langsung dengan server API Midtrans + Mengembalikan Stok Produk.
     */
    public function cancel(\App\Models\Order $order)
    {
        // Validasi kepemilikan data pesanan
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Tindakan ilegal.');
        }

        // Pembatalan hanya berlaku jika status pembayaran masih belum lunas / pending
        if (in_array($order->payment_status, ['unpaid', 'pending'])) {
            try {
                \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
                \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
                
                // Kirim instruksi pembatalan resmi ke server API Midtrans agar token Snap hangus
                \Midtrans\Transaction::cancel($order->order_code);
            } catch (\Exception $e) {
                // Tetap abaikan jika transaksi belum tergenerate sempurna di server Midtrans
            }

            // KUNCI LOGISTIK: Kembalikan persediaan produk ke database inventory karena batal dibeli
            if ($order->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->variant_id) {
                        \App\Models\ProductVariant::where('id', $item->variant_id)->increment('stock', $item->quantity);
                    }
                    \App\Models\Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }

                // KEMBALIKAN VOUCHER KE DOMPET
                if ($order->user_voucher_id) {
                    DB::table('user_vouchers')->where('id', $order->user_voucher_id)->update([
                        'is_used' => false,
                        'used_at' => null
                    ]);
                }
            }

            // Perbarui data lokal menjadi cancelled
            $order->update([
                'payment_status' => 'cancelled',
                'status'         => 'cancelled'
            ]);

            return redirect()->back()->with('success', 'Pesanan Anda telah berhasil dibatalkan dan Voucher telah dikembalikan ke dompet.');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan.');
    }

    /**
     * FITUR REAL-TIME SINKRONISASI MIDTRANS GATEWAY (PULL SYSTEM)
     */
    public function syncMidtransStatus($id)
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        
        $order = \App\Models\Order::findOrFail($id);

        try {
            $status = \Midtrans\Transaction::status($order->order_code);
            $midtransStatus = $status->transaction_status;

            if (in_array($midtransStatus, ['expire', 'cancel', 'deny'])) {
                // KUNCI LOGISTIK: Kembalikan stok jika status berubah ke cancelled untuk pertama kali
                if ($order->status !== 'cancelled') {
                    foreach ($order->items as $item) {
                        if ($item->variant_id) {
                            \App\Models\ProductVariant::where('id', $item->variant_id)->increment('stock', $item->quantity);
                        }
                        \App\Models\Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }

                    // KEMBALIKAN VOUCHER KE DOMPET
                    if ($order->user_voucher_id) {
                        DB::table('user_vouchers')->where('id', $order->user_voucher_id)->update([
                            'is_used' => false,
                            'used_at' => null
                        ]);
                    }
                }

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

        return redirect()->route('customer.orders.show', $order->order_code);
    }
}
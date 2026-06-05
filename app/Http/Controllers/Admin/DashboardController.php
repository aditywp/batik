<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// --- IMPORT MODEL ---
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
// --------------------

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard panel admin + Sinkronisasi Status Otomatis.
     */
    public function index()
    {
        // 1. Mengambil 5 data transaksi terbaru untuk tabel aktivitas beranda dashboard
        $recentOrders = Order::with(['user'])->latest()->take(5)->get();

        // ======================================================================
        // AUTOMATIC REAL-TIME SYNC FOR DASHBOARD HOME (PULL SYSTEM)
        // Memeriksa dan memperbarui status pesanan yang tampil di beranda secara otomatis
        // ======================================================================
        foreach ($recentOrders as $order) {
            if ($order->payment_status === 'unpaid' || $order->payment_status === 'pending') {
                try {
                    \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
                    \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);

                    // Ambil status paling mutakhir langsung dari core server Midtrans Sandbox
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
                    // Abaikan jika order belum terbentuk di server sandbox Midtrans
                }
            }
        }

        // 2. Mengambil data statistik untuk widget card setelah status diperbarui
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $totalOrders = Order::query()->count();
        $totalProducts = Product::query()->count();
        $totalCustomers = User::query()->where('role', 'customer')->count();

        // 3. Mengirim data ke view admin/dashboard/index.blade.php
        // (recentOrders dan totalRevenue ditambahkan ke compact untuk mendukung tampilan visual dashboard)
        return view('admin.dashboard.index', compact(
            'totalOrders', 
            'totalProducts', 
            'totalCustomers',
            'recentOrders',
            'totalRevenue'
        ));
    }
}
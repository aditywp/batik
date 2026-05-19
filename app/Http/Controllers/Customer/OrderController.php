<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ProductControlle;
class OrderController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        // 1. Mulai query dasar untuk pesanan milik user yang sedang login
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
        // Menambahkan 'items.variant' untuk detail pesanan yang lebih lengkap
        $order = Order::where('order_code', $order_code)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'items.variant']) // Tambahkan variant di sini
            ->firstOrFail();

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
}
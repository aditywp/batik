<?php
// app/Http/Controllers/Admin/OrderController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Daftar semua pesanan dengan filter & search
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])
            ->latest();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan status pembayaran
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search berdasarkan kode order atau nama customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                    ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // Hitung ringkasan untuk stat cards
        $summary = [
            'total'      => Order::query()->count('id'),
            'pending'    => Order::query()->where('status', 'pending')->count('id'),
            'shipped'    => Order::query()->where('status', 'shipped')->count('id'),
            'delivered'  => Order::query()->where('status', 'delivered')->count('id'),
        ];

        return view('admin.orders.index', compact('orders', 'summary'));
    }

    /**
     * Detail satu pesanan
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update status pesanan
     */
    public function updateStatus(Request $request, Order $order)
    {
        $allowed = $order->allowedNextStatuses();

        $request->validate([
            'status' => ['required', 'in:' . implode(',', $allowed)],
        ], [
            'status.in' => 'Status tidak valid untuk pesanan ini.',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        $order->update(['status' => $newStatus]);

        // Kalau order selesai, kembalikan stok jika dibatalkan
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity, []);
            }
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', "Status pesanan berhasil diubah ke \"{$order->statusLabel()}\".");
    }

    /**
     * Export daftar order ke CSV (bonus fitur laporan)
     */
    public function export(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        $filename = 'orders-' . now()->format('Ymd-His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $columns = ['Kode Order', 'Pelanggan', 'Email', 'Total', 'Status', 'Pembayaran', 'Tanggal'];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_code,
                    $order->user->name,
                    $order->user->email,
                    $order->total,
                    $order->statusLabel(),
                    $order->payment_status,
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
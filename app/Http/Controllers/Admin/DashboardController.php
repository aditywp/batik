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
     * Tampilkan halaman utama dashboard panel admin.
     */
    public function index()
    {
        // Mengambil data statistik untuk dashboard secara efisien langsung dari database
        $totalOrders = Order::query()->count();
        $totalProducts = Product::query()->count();
        $totalCustomers = User::query()->where('role', 'customer')->count();

        // Mengirim data ke view admin/dashboard/index.blade.php
        return view('admin.dashboard.index', compact(
            'totalOrders', 
            'totalProducts', 
            'totalCustomers'
        ));
    }
}
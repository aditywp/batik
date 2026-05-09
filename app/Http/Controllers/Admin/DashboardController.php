<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// --- TAMBAHKAN IMPORT MODEL DI BAWAH INI ---
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
// ------------------------------------------

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil data statistik untuk dashboard
        // Gunakan ::query() jika "where" masih merah di VS Code kamu
        $totalOrders = Order::all()->count();
        $totalProducts = Product::all()->count();
        $totalCustomers = User::query()->where('role', 'customer')->count();

        // Mengirim data ke view admin/dashboard/index.blade.php
        return view('admin.dashboard.index', compact(
            'totalOrders', 
            'totalProducts', 
            'totalCustomers'
        ));
    }
}
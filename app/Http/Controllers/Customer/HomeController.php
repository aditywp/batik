<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan Halaman Utama (welcome.blade.php)
     */
    public function landingPage()
    {
        // Mengambil 3 produk terbaru untuk section koleksi
        $products = Product::with(['category'])->latest()->take(3)->get();

        return view('welcome', compact('products'));
    }

    /**
     * Menampilkan Dashboard Customer (customer/home.blade.php)
     */
    public function index()
    {
        // PERBAIKAN: Mengambil data produk agar variabel $products tidak Undefined
        $products = Product::with(['category'])->latest()->get();

        return view('customer.home', compact('products'));
    }
}
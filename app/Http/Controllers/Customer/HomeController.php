<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
// Import RajaOngkirController yang baru
use App\Http\Controllers\RajaOngkirController; 

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
        // Mengambil data produk agar variabel $products tidak Undefined
        $products = Product::with(['category'])->latest()->get();

        // PERBAIKAN: Memanggil RajaOngkirController secara langsung 
        // karena Service sudah dihapus
        $rajaOngkir = new RajaOngkirController();
        $provinces = $rajaOngkir->getProvinces();

        // Kirim $products dan $provinces ke view
        return view('customer.home', compact('products', 'provinces'));
    }
}
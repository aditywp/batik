<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Menampilkan daftar produk dengan dukungan filter Koleksi.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants', 'images'])->latest();

        // Fitur Filter Koleksi (Women, Men, Kids, dll)
        // Dipicu saat user klik link seperti: /catalog?collection=women
        if ($request->has('collection')) {
            $query->where('collection', $request->collection);
        }

        $products = $query->paginate(12);

        // Mengirimkan variabel 'selectedCollection' untuk menandai judul di halaman index
        $selectedCollection = $request->collection;

        return view('catalog.index', compact('products', 'selectedCollection'));
    }

    /**
     * Menampilkan detail produk spesifik berdasarkan slug.
     */
    public function show($slug)
    {
        $product = Product::query()->where('slug', $slug)
            ->with(['category', 'variants', 'images'])
            ->firstOrFail();

        // Mengambil produk terkait berdasarkan kategori yang sama
        $relatedProducts = Product::query()->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('catalog.show', compact('product', 'relatedProducts'));
    }

    /**
     * Fitur pencarian produk (Opsional jika ingin diaktifkan kembali).
     */
    public function search(Request $request)
    {
        $searchQuery = $request->input('query');

        $products = Product::with(['category', 'variants', 'images'])
            ->where('name', 'LIKE', "%{$searchQuery}%")
            ->orWhere('description', 'LIKE', "%{$searchQuery}%")
            ->paginate(12);

        return view('catalog.index', compact('products'))->with('query', $searchQuery);
    }
}
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Menampilkan daftar produk dengan dukungan filter.
     * Stok akan selalu sinkron karena mengambil data terbaru dari database.
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi Query dengan Eager Loading dan HANYA TAMPILKAN PRODUK AKTIF
        $query = Product::with(['category', 'variants', 'images'])->where('is_active', true);

        // 2. Filter Pencarian (q) - Update: Lebih robust dengan strtolower
        if ($request->filled('q')) {
            $search = strtolower($request->q);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        // 3. Filter Koleksi (Women, Men, Kids, dll)
        if ($request->filled('collection')) {
            $query->where('collection', $request->collection);
        }

        // 4. Filter Kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 5. Filter Rentang Harga
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        /**
         * OPSIONAL: Sembunyikan produk jika stok benar-benar habis (0)
         * Hapus komentar di bawah jika ingin produk stok 0 tidak muncul di katalog
         */
        // $query->where('stock', '>', 0);

        // 6. Eksekusi Query dengan Pagination
        $products = $query->latest()->paginate(12)->withQueryString();

        // 7. Ambil semua kategori untuk sidebar
        $categories = Category::all();

        return view('catalog.index', compact('products', 'categories'));
    }

    /**
     * Menampilkan detail produk spesifik.
     */
    public function show(string $slug)
    {
        // Mengambil produk dengan relasi fresh untuk memastikan stok varian akurat
        // HANYA BISA DIAKSES JIKA PRODUK AKTIF (Mencegah akses lewat link langsung)
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true) 
            ->with(['category', 'variants', 'images'])
            ->firstOrFail();

        // Mengambil produk terkait (You May Also Like) yang masih memiliki stok DAN AKTIF
        $relatedProducts = Product::query()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true) 
            ->where('stock', '>', 0) // Hanya tampilkan yang ada stoknya
            ->take(4)
            ->get();

        return view('catalog.show', compact('product', 'relatedProducts'));
    }

    /**
     * API untuk mendapatkan data stok terbaru (jika diperlukan untuk pengecekan via JS)
     */
    public function getStockJson($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        return response()->json([
            'total_stock' => $product->stock,
            'variants' => $product->variants
        ]);
    }
}
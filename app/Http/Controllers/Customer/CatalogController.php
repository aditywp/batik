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
        // 1. Inisialisasi Query dengan Eager Loading agar data stok varian ikut terambil
        $query = Product::with(['category', 'variants', 'images']);

        // 2. Filter Pencarian (q)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
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
        $product = Product::query()->where('slug', $slug)
            ->with(['category', 'variants', 'images'])
            ->firstOrFail();

        // Mengambil produk terkait (You May Also Like) yang masih memiliki stok
        $relatedProducts = Product::query()->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
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
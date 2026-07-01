<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar produk dengan fitur Pencarian, Filter Kategori, Koleksi, Status Stok, dan Status Aktif.
     */
    public function index(Request $request)
    {
        // 1. Mulai Query dengan Eager Loading untuk efisiensi database
        $query = Product::with(['category', 'images', 'variants'])->latest();

        // 2. Logika Pencarian Nama Produk
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 3. Logika Filter Kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 4. Logika Filter Koleksi (Sinkron dengan Case-Sensitive database)
        if ($request->filled('collection')) {
            $query->where('collection', $request->collection);
        }

        // 5. Logika Filter Kondisi/Status Stok
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'empty') {
                $query->where('stock', '=', 0);
            } elseif ($request->stock_status === 'low') {
                $query->where('stock', '>', 0)->where('stock', '<=', 5);
            }
        }

        // 6. Logika Filter Status (Aktif / Draft)
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $categories = Category::all();

        // 7. Eksekusi Pagination (12 produk agar rapi di tampilan Grid kelipatan 4 kolom)
        $products = $query->paginate(12)->withQueryString(); 
            
        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Tampilkan form tambah produk.
     */
    public function create()
    {
        $categories = Category::all(); 
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                   => 'required|string|max:100',
            'description'            => 'required|string|max:255', 
            'price'                  => 'required|numeric|min:10000',
            'category_id'            => 'required|exists:categories,id',
            'collection'             => 'required|string|in:Women,Men,Kids,Craft,Family',
            'motifs'                 => 'required|array|min:1',
            'motifs.*.name'          => 'required|string|max:100', 
            'motifs.*.image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'motifs.*.sizes'         => 'required|array|min:1',
            'motifs.*.sizes.*.size'  => 'required|string', 
            'motifs.*.sizes.*.price' => 'nullable|numeric|min:10000',
            'motifs.*.sizes.*.stock' => 'required|integer|min:0|max:1000', 
            'images.*'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'price.min' => 'Harga utama minimal adalah Rp 10.000.',
            'motifs.*.sizes.*.price.min' => 'Harga khusus variasi minimal adalah Rp 10.000.',
            'name.max' => 'Nama produk tidak boleh lebih dari 100 karakter.',
        ]);

        try {
            DB::beginTransaction();

            $slug = Str::slug($request->name) . '-' . time();

            $totalStock = 0;
            foreach ($request->motifs as $motif) {
                foreach ($motif['sizes'] as $size) {
                    $totalStock += $size['stock'];
                }
            }

            $product = Product::create([
                'name'        => $request->name,
                'slug'        => $slug,
                'description' => $request->description,
                'price'       => $request->price,
                'category_id' => $request->category_id,
                'collection'  => $request->collection,
                'stock'       => $totalStock,
            ]);

            foreach ($request->motifs as $mKey => $motifData) {
                $motifImagePath = null;
                if ($request->hasFile("motifs.$mKey.image")) {
                    $motifImagePath = $request->file("motifs.$mKey.image")->store('products/variants', 'public');
                }

                foreach ($motifData['sizes'] as $sizeData) {
                    $product->variants()->create([
                        'motif'      => $motifData['name'],
                        'image_path' => $motifImagePath,
                        'size'       => $sizeData['size'],
                        'price'      => $sizeData['price'] ?? null,
                        'stock'      => $sizeData['stock'],
                    ]);
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products/gallery', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => false,
                        'sort_order' => $index
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Produk batik berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan form edit produk.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load(['images', 'variants']);
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update produk dan variasi.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'                   => 'required|string|max:100', 
            'description'            => 'required|string|max:1000',
            'price'                  => 'required|numeric|min:10000', 
            'category_id'            => 'required|exists:categories,id',
            'collection'             => 'required|string|in:Women,Men,Kids,Craft,Family',
            'motifs'                 => 'required|array|min:1',
            'motifs.*.name'          => 'required|string|max:100',
            'motifs.*.sizes'         => 'required|array|min:1',
            'motifs.*.sizes.*.size'  => 'required|string',
            'motifs.*.sizes.*.price' => 'nullable|numeric|min:10000',
            'motifs.*.sizes.*.stock' => 'required|integer|min:0|max:9999',
        ], [
            'price.min' => 'Harga utama minimal adalah Rp 10.000.',
            'motifs.*.sizes.*.price.min' => 'Harga khusus variasi minimal adalah Rp 10.000.',
        ]);

        try {
            DB::beginTransaction();

            if ($request->name !== $product->name) {
                $product->slug = Str::slug($request->name) . '-' . time();
            }

            $totalStock = 0;
            foreach ($request->motifs as $motif) {
                foreach ($motif['sizes'] as $size) {
                    $totalStock += $size['stock'];
                }
            }

            $product->update([
                'name'        => $request->name,
                'description' => $request->description,
                'price'       => $request->price,
                'category_id' => $request->category_id,
                'collection'  => $request->collection,
                'stock'       => $totalStock,
            ]);

            $oldVariants = $product->variants;
            $product->variants()->delete();

            foreach ($request->motifs as $mKey => $motifData) {
                $motifImagePath = null;

                if ($request->hasFile("motifs.$mKey.image")) {
                    $motifImagePath = $request->file("motifs.$mKey.image")->store('products/variants', 'public');
                } else {
                    $existing = $oldVariants->where('motif', $motifData['name'])->first();
                    $motifImagePath = $existing ? $existing->image_path : null;
                }

                foreach ($motifData['sizes'] as $sizeData) {
                    $product->variants()->create([
                        'motif'      => $motifData['name'],
                        'image_path' => $motifImagePath,
                        'size'       => $sizeData['size'],
                        'price'      => $sizeData['price'] ?? null,
                        'stock'      => $sizeData['stock'],
                    ]);
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products/gallery', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path
                    ]);
                }
            }

            DB::commit();

            if ($request->filled('redirect_to')) {
                return redirect($request->redirect_to)->with('success', 'Produk berhasil diperbarui!');
            }

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Hapus produk (Menggunakan fitur Hard Delete Permanen).
     */
    public function destroy($id)
    {
        // Menggunakan withTrashed() berjaga-jaga apabila produk sebelumnya sudah berstatus Soft Delete
        $product = Product::withTrashed()->findOrFail($id);
        
        // Hapus data varian & gambar dari database secara permanen (jika tidak ada sistem cascade)
        if (method_exists($product->variants(), 'forceDelete')) {
            $product->variants()->forceDelete();
        } else {
            $product->variants()->delete();
        }

        if (method_exists($product->images(), 'forceDelete')) {
            $product->images()->forceDelete();
        } else {
            $product->images()->delete();
        }

        // Hancurkan data produk secara permanen dari database
        $product->forceDelete();
        
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus permanen dari sistem!');
    }
    
    /**
     * Ubah status Aktif / Nonaktif (Draft) Produk.
     */
    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan (disembunyikan dari katalog)';
        
        return back()->with('success', "Status produk berhasil $status.");
    }

    /**
     * Sinkronisasi stok (Panggil saat pembayaran Lunas).
     */
    public static function reduceStock($productId, $variantId, $quantity)
    {
        DB::transaction(function () use ($productId, $variantId, $quantity) {
            $variant = ProductVariant::where('id', $variantId)->first();
            if ($variant) {
                $variant->decrement('stock', $quantity);
            }

            $product = Product::find($productId);
            if ($product) {
                $product->decrement('stock', $quantity);
            }
        });
    }

    /**
     * Sinkronisasi ulang total stok.
     */
    public function syncTotalStock(Product $product)
    {
        $newTotalStock = $product->variants()->sum('stock');
        $product->update(['stock' => $newTotalStock]);
        return $newTotalStock;
    }
}
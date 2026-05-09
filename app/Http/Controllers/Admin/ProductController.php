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
     * Tampilkan daftar produk.
     */
    public function index()
    {
        $products = Product::with(['category', 'images', 'variants'])->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
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
     * Simpan produk, variasi bertingkat, dan foto ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'collection'  => 'required|string|in:women,men,kids,craft,family', // Tambahan validasi collection
            'motifs'      => 'required|array|min:1',
            'motifs.*.name'         => 'required|string',
            'motifs.*.image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'motifs.*.sizes'        => 'required|array|min:1',
            'motifs.*.sizes.*.size' => 'nullable|string',
            'motifs.*.sizes.*.stock'=> 'required|integer|min:0',
            'images.*'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // 1. Generate Slug unik
            $slug = Str::slug($request->name) . '-' . time();

            // 2. Hitung Total Stok dari semua variasi motif & ukuran
            $totalStock = 0;
            foreach ($request->motifs as $motif) {
                foreach ($motif['sizes'] as $size) {
                    $totalStock += $size['stock'];
                }
            }

            // 3. Simpan Produk Utama (Ditambah kolom collection)
            $product = Product::create([
                'name'        => $request->name,
                'slug'        => $slug,
                'description' => $request->description,
                'price'       => $request->price,
                'category_id' => $request->category_id,
                'collection'  => $request->collection, // Simpan collection
                'stock'       => $totalStock,
            ]);

            // 4. Loop Motif dan Simpan Variasi
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

            // 5. Simpan Foto Galeri Tambahan
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
            return redirect()->route('admin.products.index')->with('success', 'Produk batik dan variasi berhasil disimpan!');

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
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'collection'  => 'required|string|in:women,men,kids,craft,family', // Tambahan validasi update
            'motifs'      => 'required|array|min:1',
            'motifs.*.name'         => 'required|string',
            'motifs.*.sizes'        => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update Slug jika nama berubah
            if ($request->name !== $product->name) {
                $product->slug = Str::slug($request->name) . '-' . time();
            }

            // 2. Hitung Total Stok Baru
            $totalStock = 0;
            foreach ($request->motifs as $motif) {
                foreach ($motif['sizes'] as $size) {
                    $totalStock += $size['stock'];
                }
            }

            // 3. Update Produk Utama (Ditambah kolom collection)
            $product->update([
                'name'        => $request->name,
                'description' => $request->description,
                'price'       => $request->price,
                'category_id' => $request->category_id,
                'collection'  => $request->collection, // Update collection
                'stock'       => $totalStock,
            ]);

            // 4. Update Variasi (Hapus lama, Simpan baru)
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

            // 5. Tambah Foto Galeri Baru (jika ada)
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
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Hapus produk dan file fisik fotonya.
     */
    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        foreach ($product->variants as $variant) {
            if ($variant->image_path) {
                Storage::disk('public')->delete($variant->image_path);
            }
        }

        $product->forceDelete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus permanen!');
    }
}
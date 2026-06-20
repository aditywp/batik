<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category; // Pastikan Model Category sudah ada
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori dengan fitur Pencarian
     */
    public function index(Request $request)
    {
        // Memulai query builder
        $query = Category::query()->latest();

        // Logika Pencarian berdasarkan nama kategori
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Menampilkan 10 data per halaman dan mempertahankan parameter pencarian di URL
        $categories = $query->paginate(10)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Menyimpan kategori baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input: Nama harus unik agar slug tidak bentrok
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        // Membuat kategori dengan slug otomatis
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Kategori batik berhasil ditambahkan!');
    }

    /**
     * Memperbarui kategori di database (EDIT)
     */
    public function update(Request $request, Category $category)
    {
        // Validasi input: mengecualikan ID kategori saat ini dari aturan unique
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        // Update kategori
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Kategori batik berhasil diperbarui!');
    }

    /**
     * Menghapus kategori
     */
    public function destroy(Category $category)
    {
        // Cek apakah kategori masih memiliki produk sebelum dihapus
        if ($category->products()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk!');
        }

        Category::destroy($category->id);

        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}
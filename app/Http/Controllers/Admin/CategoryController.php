<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category; // Pastikan Model Category sudah ada
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori
     */
    public function index()
    {
    // Menampilkan 10 data per halaman
    $categories = Category::query()->latest()->paginate(10);
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
     * Menghapus kategori
     */
    public function destroy(Category $category)
    {
        // Cek apakah kategori masih memiliki produk sebelum dihapus (Opsional untuk keamanan data)
        if ($category->products()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk!');
        }

        Category::destroy($category->id);

        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}
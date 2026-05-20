<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Menampilkan semua ulasan customer (Otomatis Tampil Publik)
     */
    public function index(Request $request)
    {
        // Mengambil semua data ulasan beserta relasinya secara eager loading
        $query = Review::with(['user', 'product', 'order'])->latest();

        // Filter berdasarkan rating bintang (1-5) jika admin ingin memilah
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Paginate hasil ulasan per 10 data dengan mempertahankan query string filter
        $reviews = $query->paginate(10)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Menghapus ulasan yang mengandung kata kasar, toksik, atau spam
     */
    public function destroy(Review $review)
    {
        $review->delete();
        
        return back()->with('success', 'Ulasan berhasil dihapus dari sistem!');
    }
}
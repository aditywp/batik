<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'required|string|min:5',
        ]);

        // 1. Cek apakah sudah pernah review produk ini di order ini
        $alreadyReviewed = Review::where('user_id', Auth::id())
            ->where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        // 2. Pastikan order memang milik user dan sudah selesai
        Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->where('status', 'delivered')
            ->firstOrFail();

        Review::create([
            'user_id'     => Auth::id(),
            'product_id'  => $request->product_id,
            'order_id'    => $request->order_id,
            'rating'      => $request->rating,
            'comment'     => $request->comment,
            'is_approved' => true,
        ]);

        return back()->with('success', 'Ulasan berhasil disimpan!');
    }
}
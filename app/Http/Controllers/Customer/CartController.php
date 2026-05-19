<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Menampilkan isi keranjang belanja user.
     */
    public function index()
    {
        // Mengambil item keranjang dengan relasi agar tidak query berulang (N+1 Problem)
        $cartItems = Auth::user()->cartItems()->with(['product.variants', 'variant'])->get();

        $subtotal = $cartItems->sum(function($item) {
            // Gunakan harga varian jika ada, jika tidak gunakan harga produk utama
            $price = $item->variant->price ?? $item->product->price;
            return $price * $item->quantity;
        });

        // PERBAIKAN: subtotal dikirim sebagai string 'subtotal' ke fungsi compact
        return view('customer.cart.index', compact('cartItems', 'subtotal'));
    }

    /**
     * Menambahkan produk ke dalam keranjang.
     * Mengatasi Duplicate Entry dengan mengecek Kombinasi User + Produk + Varian.
     */
    public function add(Request $request, int $productId)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $user = Auth::user();
        $qtyToAdd = $request->quantity ?? 1;

        // PENTING: Cek kecocokan User ID, Product ID, DAN Variant ID
        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            // Jika item sudah ada dengan varian yang sama, cukup tambahkan jumlahnya (UPDATE)
            $cartItem->increment('quantity', $qtyToAdd);
        } else {
            // Jika belum ada kombinasi unik ini, buat baris baru (INSERT)
            CartItem::create([
                'user_id'    => $user->id,
                'product_id' => $productId,
                'variant_id' => $request->variant_id,
                'quantity'   => $qtyToAdd,
            ]);
        }

        return redirect()->route('customer.cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Memperbarui jumlah (quantity) item di keranjang.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:increase,decrease,manual',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $cartItem = CartItem::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('variant')
            ->firstOrFail();

        $quantity = $cartItem->quantity;
        $maxStock = $cartItem->variant->stock;

        // 1. LOGIKA TAMBAH (INCREASE)
        if ($request->action === 'increase') {
            if ($quantity >= $maxStock) {
                return back()->with('error', 'Stok tidak mencukupi untuk menambah jumlah.');
            }
            $quantity++;
        }

        // 2. LOGIKA KURANG (DECREASE)
        if ($request->action === 'decrease') {
            if ($quantity > 1) {
                $quantity--;
            } else {
                return back()->with('error', 'Minimal pembelian adalah 1 unit. Gunakan tombol hapus untuk membuang item.');
            }
        }

        // 3. LOGIKA INPUT MANUAL
        if ($request->action === 'manual') {
            $manualQty = intval($request->quantity);

            if ($manualQty > $maxStock) {
                $cartItem->update(['quantity' => $maxStock]);
                return back()->with('error', 'Jumlah dibatasi sesuai stok maksimal (' . $maxStock . ').');
            }
            
            $quantity = max(1, $manualQty);
        }

        $cartItem->update(['quantity' => $quantity]);

        return back()->with('success', 'Jumlah belanja diperbarui.');
    }

    /**
     * Menghapus produk dari keranjang secara permanen.
     */
    public function remove($id)
    {
        CartItem::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
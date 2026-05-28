<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RajaOngkirController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\ProductVariant; 
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(
        private MidtransService $midtrans,
    ) {}

    /**
     * Halaman Utama Checkout / Informasi Pengiriman
     */
    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with(['product', 'variant'])->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Keranjang belanjamu masih kosong.');
        }

        return view('customer.checkout.index', compact('cartItems'));
    }
        
    /**
     * Endpoint AJAX untuk memproses Order & mendapatkan Snap Token Midtrans
     */
    public function process(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'courier'          => 'required|string|max:20',
            'courier_service'  => 'required|string|max:20',
            'shipping_cost'    => 'required|integer|min:0',
        ]);

        $cartItems = Auth::user()->cartItems()->with(['product', 'variant'])->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong.'], 422);
        }

        $subtotal = $cartItems->sum(fn($i) => ($i->variant->price ?? $i->product->price) * $i->quantity);
        $total    = $subtotal + $request->shipping_cost;

        // 1. Buat Header Order (Awal masuk DB status unpaid & data detail transid masih null)
        $order = Order::create([
            'user_id'          => Auth::id(),
            'order_code'       => 'BI-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
            'subtotal'         => $subtotal,
            'shipping_cost'    => $request->shipping_cost,
            'total'            => $total,
            'status'           => 'pending',
            'payment_status'   => 'unpaid',
            'shipping_address' => $request->shipping_address,
            'courier'          => $request->courier,
            'courier_service'  => $request->courier_service,
        ]);

        // 2. Simpan Detail Item & Potong Stok SINKRON
        foreach ($cartItems as $item) {
            $currentPrice = $item->variant->price ?? $item->product->price;

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'quantity'   => $item->quantity,
                'price'      => $currentPrice,
                'subtotal'   => $currentPrice * $item->quantity,
            ]);

            // A. POTONG STOK VARIAN (S/M/L) - Supaya detail produk jadi berkurang
            if ($item->variant_id) {
                ProductVariant::where('id', $item->variant_id)->decrement('stock', $item->quantity);
            }

            // B. POTONG STOK UTAMA - Supaya tabel dashboard utama admin ikut berkurang
            $item->product->decrement('stock', $item->quantity);
        }

        // 3. Integrasi Midtrans
        $order->load('items.product');
        $snapToken = $this->midtrans->createSnapToken($order);
        $order->update(['snap_token' => $snapToken]);

        // 4. Kosongkan Keranjang Belanja User
        Auth::user()->cartItems()->delete();

        return response()->json([
            'snap_token' => $snapToken,
            'order_code' => $order->order_code,
            'client_key' => config('services.midtrans.client_key'),
        ]);
    }

    /**
     * Halaman Finish Checkout / Nota Pembayaran Berhasil
     * PERBAIKAN: Menambahkan logic jabat tangan API otomatis untuk mengamankan data null di DB lokal lokal kamu
     */
    public function finish(Request $request)
    {
        if (! $request->filled('order_id')) {
            return redirect()->route('customer.home');
        }

        $order = Order::with('items.product')
            ->where('order_code', $request->order_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // INTEGRASI SINKRONISASI PULL DATA REKENING METODE BAYAR MIDTRANS
        try {
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);

            // Ambil status mutasi pembayaran resmi dari core API Midtrans Sandbox
            $statusJson = \Midtrans\Transaction::status($order->order_code);
            $transactionStatus = $statusJson->transaction_status;

            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                // JIKA LUNAS: Simpan data mutasi agar kolom tidak bernilai null lagi!
                $order->update([
                    'payment_status'          => 'paid',
                    'status'                  => 'processing',
                    'midtrans_transaction_id' => $statusJson->transaction_id ?? null,
                    'payment_method'          => $statusJson->payment_type ?? null,
                    'paid_at'                 => now(),
                ]);
            } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                // JIKA EXPIRED / CANCEL: Otomatis gagalkan invoice pesanan kain batik
                $order->update([
                    'payment_status'          => 'cancelled',
                    'status'                  => 'cancelled',
                    'midtrans_transaction_id' => $statusJson->transaction_id ?? null,
                    'payment_method'          => $statusJson->payment_type ?? null,
                ]);
            }
        } catch (\Exception $e) {
            // Abaikan jika transaksi belum diproses atau payment modal langsung ditutup paksa
        }

        return view('customer.checkout.finish', compact('order'));
    }
}
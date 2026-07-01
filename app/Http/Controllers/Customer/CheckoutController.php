<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant; 
use App\Models\UserVoucher; // Panggil model UserVoucher
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(
        private MidtransService $midtrans,
    ) {}

    public function index()
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()->with(['product', 'variant'])->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart.index')
                ->with('error', 'Keranjang belanjamu masih kosong.');
        }

        // Membaca daftar voucher aktif langsung dari model dompet
        $myActiveVouchers = UserVoucher::with('voucher')
            ->where('user_id', $user->id)
            ->where('is_used', false)
            ->get();

        return view('customer.checkout.index', compact('cartItems', 'myActiveVouchers'));
    }
        
    public function process(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'courier'          => 'required|string|max:20',
            'courier_service'  => 'required|string|max:20',
            'shipping_cost'    => 'required|integer|min:0',
            'user_voucher_id'  => 'nullable|integer', 
        ]);

        $user = Auth::user();
        $cartItems = $user->cartItems()->with(['product.images', 'product.variants', 'variant'])->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong.'], 422);
        }

        $subtotal = $cartItems->sum(fn($i) => ($i->variant->price ?? $i->product->price) * $i->quantity);
        
        $discount = 0;
        $voucherToUse = null;

        if ($request->filled('user_voucher_id')) {
            // Ambil dari dompet berdasarkan ID unik di tabel user_vouchers
            $voucherToUse = UserVoucher::where('id', $request->user_voucher_id)
                ->where('user_id', $user->id)
                ->where('is_used', false)
                ->first();

            if ($voucherToUse) {
                // Selalu utamakan nominal snapshot yang tersimpan di dompet
                $discount = $voucherToUse->discount_snapshot;
            }
        }

        $total = ($subtotal + $request->shipping_cost) - $discount;
        if ($total < 0) {
            $total = 0;
        }

        DB::transaction(function () use ($request, $cartItems, $subtotal, $total, $voucherToUse, &$order) {
            
            $paymentStatus = $total == 0 ? 'paid' : 'unpaid';
            $orderStatus   = $total == 0 ? 'processing' : 'pending';
            $paidAt        = $total == 0 ? now() : null;

            $order = Order::create([
                'user_id'          => Auth::id(),
                'order_code'       => 'BI-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'subtotal'         => $subtotal,
                'shipping_cost'    => $request->shipping_cost,
                'total'            => $total,
                'status'           => $orderStatus,       
                'payment_status'   => $paymentStatus,     
                'paid_at'          => $paidAt,            
                'shipping_address' => $request->shipping_address,
                'courier'          => $request->courier,
                'courier_service'  => $request->courier_service,
                'user_voucher_id'  => $voucherToUse ? $voucherToUse->id : null, // ID dari tabel user_vouchers
            ]);

            if ($voucherToUse) {
                $voucherToUse->update([
                    'is_used' => true,
                    'used_at' => now()
                ]);
            }

            foreach ($cartItems as $item) {
                $currentPrice = $item->variant->price ?? $item->product->price;

                $imagePathSnapshot = $item->variant?->image_path 
                                   ?? $item->product->variants->first()?->image_path 
                                   ?? $item->product->images->first()?->image_path 
                                   ?? null;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity'   => $item->quantity,
                    'price'      => $currentPrice,
                    'subtotal'   => $currentPrice * $item->quantity,
                    'product_name_snapshot' => $item->product->name,
                    'price_snapshot'        => $currentPrice,
                    'image_snapshot'        => $imagePathSnapshot,
                ]);

                if ($item->variant_id) {
                    ProductVariant::where('id', $item->variant_id)->decrement('stock', $item->quantity);
                }
                $item->product->decrement('stock', $item->quantity);
            }

            Auth::user()->cartItems()->delete();
        });

        $order->load('items.product');

        if ($total == 0) {
            return response()->json([
                'is_free'    => true, 
                'order_code' => $order->order_code,
                'message'    => 'Pesanan gratis karena terpotong voucher sepenuhnya.'
            ]);
        }

        $snapToken = $this->midtrans->createSnapToken($order);
        $order->update(['snap_token' => $snapToken]);

        return response()->json([
            'is_free'    => false,
            'snap_token' => $snapToken,
            'order_code' => $order->order_code,
            'client_key' => config('services.midtrans.client_key'),
        ]);
    }

    public function finish(Request $request)
    {
        if (! $request->filled('order_id')) {
            return redirect()->route('customer.home');
        }

        $order = Order::with('items.product')
            ->where('order_code', $request->order_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->total == 0 && $order->payment_status === 'paid') {
            return view('customer.checkout.finish', compact('order'));
        }

        try {
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);

            $statusJson = \Midtrans\Transaction::status($order->order_code);
            $transactionStatus = $statusJson->transaction_status;

            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $order->update([
                    'payment_status'          => 'paid',
                    'status'                  => 'processing',
                    'midtrans_transaction_id' => $statusJson->transaction_id ?? null,
                    'payment_method'          => $statusJson->payment_type ?? null,
                    'paid_at'                 => now(),
                ]);
            } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                if ($order->payment_status !== 'cancelled') {
                    foreach ($order->items as $item) {
                        if ($item->variant_id) {
                            ProductVariant::where('id', $item->variant_id)->increment('stock', $item->quantity);
                        }
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }
                    
                    if ($order->user_voucher_id) {
                        UserVoucher::where('id', $order->user_voucher_id)->update([
                            'is_used' => false,
                            'used_at' => null
                        ]);
                    }

                    $order->update(['payment_status' => 'cancelled', 'status' => 'cancelled']);
                }
            }
        } catch (\Exception $e) {}

        return view('customer.checkout.finish', compact('order'));
    }
}
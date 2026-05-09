<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\MidtransService;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(
        private MidtransService   $midtrans,
        private RajaOngkirService $rajaOngkir,
    ) {}

    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.catalog.index')
                ->with('error', 'Keranjang belanjamu kosong.');
        }

        $provinces = $this->rajaOngkir->getProvinces();

        return view('customer.checkout.index', compact('cartItems', 'provinces'));
    }

    public function getCities(Request $request)
    {
        $request->validate(['province_id' => 'required|integer']);

        $cities = $this->rajaOngkir->getCities($request->province_id);

        return response()->json($cities);
    }

    public function getShippingCost(Request $request)
    {
        $request->validate([
            'destination_city_id' => 'required|integer',
            'courier'             => 'required|in:jne,pos,jnt',
        ]);

        $cartItems = Auth::user()->cartItems()->with('product')->get();

        // ✅ BUG #1 FIXED — operator presedensi diperbaiki
        $totalWeight = $cartItems->sum(
            fn($item) => ($item->product->weight ?? 500) * $item->quantity
        );

        $costs = $this->rajaOngkir->getCost(
            $request->destination_city_id,
            max($totalWeight, 1000),
            $request->courier
        );

        return response()->json($costs);
    }

    public function processOrder(Request $request)
    {
        $request->validate([
            'shipping_address'    => 'required|string|max:500',
            'destination_city_id' => 'required|integer',
            'courier'             => 'required|string|max:20',
            'courier_service'     => 'required|string|max:20',
            'shipping_cost'       => 'required|integer|min:0',
        ]);

        $cartItems = Auth::user()->cartItems()->with('product')->get();

        // Guard: pastikan cart tidak kosong saat proses
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong.'], 422);
        }

        $subtotal = $cartItems->sum(fn($i) => $i->product->price * $i->quantity);
        $total    = $subtotal + $request->shipping_cost;

        // Buat order
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

        // Salin item cart ke order
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'price'      => $item->product->price,
                'subtotal'   => $item->product->price * $item->quantity,
            ]);

            $item->product->decrement('stock', $item->quantity);
        }

        // ✅ BUG #5 FIXED — load relasi sebelum kirim ke Midtrans
        $order->load('items.product');
        $snapToken = $this->midtrans->createSnapToken($order);
        $order->update(['snap_token' => $snapToken]);

        // Kosongkan cart
        Auth::user()->cartItems()->delete();

        return response()->json([
            'snap_token' => $snapToken,
            'order_code' => $order->order_code,
            'client_key' => config('services.midtrans.client_key'),
        ]);
    }

    public function finish(Request $request)
    {
        // Guard: pastikan parameter order_id ada
        if (! $request->filled('order_id')) {
            return redirect()->route('customer.home');
        }

        $order = Order::with('items.product')
            ->where('order_code', $request->order_id)
            ->where('user_id', Auth::id()) // pastikan order milik user ini
            ->firstOrFail();

        return view('customer.checkout.finish', compact('order'));
    }
}
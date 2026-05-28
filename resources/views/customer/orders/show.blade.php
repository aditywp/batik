@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-[#fafafa] pt-28 pb-20 font-sans">
    <div class="max-w-5xl mx-auto px-6">
        <div class="flex items-center justify-between mb-8">
            <a href="{{ route('customer.orders.index') }}" class="text-[10px] font-black uppercase tracking-[2px] text-gray-400 hover:text-black transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
                Back to my orders
            </a>
            
            <div class="text-right">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Order Date</p>
                <p class="text-xs font-bold text-black">{{ $order->created_at->format('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Bagian Kiri: Detail Produk --}}
            <div class="lg:col-span-8 space-y-6">
                
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start mb-10">
                        <div>
                            <h1 class="text-2xl font-black uppercase italic tracking-tighter text-black">Order Detail</h1>
                            <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest">Transaction ID: <span class="text-black italic">#{{ $order->order_code }}</span></p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="bg-black text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-black/10">
                                {{ strtoupper($order->status) }}
                            </span>
                            <span class="text-[9px] font-black uppercase tracking-widest @if($order->payment_status === 'paid') text-green-500 @elseif($order->payment_status === 'cancelled') text-red-500 @else text-orange-500 @endif">
                                Payment: {{ strtoupper($order->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-8">
                        @foreach($order->items as $item)
                            @php
                                $imagePath = $item->variant->image_path 
                                             ?? $item->product->variants->first()?->image_path 
                                             ?? $item->product->images->first()?->image_path 
                                             ?? null;
                                
                                $userReview = \App\Models\Review::where('order_id', $order->id)
                                                ->where('product_id', $item->product_id)
                                                ->where('user_id', Auth::id())
                                                ->first();
                            @endphp
                            <div class="flex flex-col pb-8 border-b border-gray-50 last:border-0 last:pb-0">
                                <div class="flex gap-6">
                                    <a href="{{ route('catalog.show', $item->product->slug) }}" class="w-24 h-32 flex-shrink-0 bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 shadow-sm block relative group/img">
                                        <img src="{{ $imagePath ? asset('storage/' . $imagePath) : asset('images/placeholder.jpg') }}" 
                                             class="w-full h-full object-cover shadow-inner transition-transform duration-500 group-hover/img:scale-105">
                                        <div class="absolute inset-0 bg-black/5 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                            <span class="text-white text-[9px] font-black uppercase tracking-widest bg-black/60 px-2 py-1 rounded-md">View</span>
                                        </div>
                                    </a>

                                    <div class="flex-grow py-2 flex flex-col justify-between">
                                        <div>
                                            <h4 class="text-base font-black uppercase tracking-tight text-black leading-tight hover:text-orange-600 transition-colors">
                                                <a href="{{ route('catalog.show', $item->product->slug) }}">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h4>
                                            <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-widest">{{ $item->product->collection ?? 'Batik Collection' }}</p>
                                            
                                            @if($item->variant)
                                                <div class="mt-3 flex gap-2">
                                                    <span class="px-2 py-1 bg-gray-100 rounded text-[9px] font-black uppercase text-gray-500 tracking-tighter">
                                                        Motif: {{ $item->variant->motif ?? 'Original' }} / Size: {{ $item->variant->size ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex justify-between items-end">
                                            <p class="text-xs font-bold text-gray-400 italic">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                            <p class="text-sm font-black text-black italic">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($order->status === 'delivered')
                                    @if($userReview)
                                        <div class="mt-4 p-5 bg-stone-50 rounded-2xl border border-stone-100/60">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-emerald-600 mb-2 italic flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                                                </svg>
                                                Ulasan Terbit Publik
                                            </p>
                                            <div class="flex text-amber-400 gap-0.5 mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span class="text-xs">{{ $i <= $userReview->rating ? '★' : '☆' }}</span>
                                                @endfor
                                            </div>
                                            <p class="text-xs text-stone-600 font-medium italic">"{{ $userReview->comment }}"</p>
                                        </div>
                                    @else
                                        <div class="mt-4 p-5 bg-white rounded-2xl border-2 border-dashed border-slate-200">
                                            <p class="text-[11px] font-black uppercase tracking-widest text-slate-800 mb-3 italic">Share your experience</p>
                                            <form action="{{ route('customer.reviews.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                
                                                <div class="flex flex-col gap-3">
                                                    <div class="relative">
                                                        <label class="text-[9px] font-bold text-slate-500 uppercase mb-1 block">Rating Produk</label>
                                                        <select name="rating" required class="w-full text-xs font-bold rounded-xl border-slate-300 text-black focus:ring-2 focus:ring-orange-500 focus:border-black bg-slate-50 py-3">
                                                            <option value="5">⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                                                            <option value="4">⭐⭐⭐⭐ (Puas)</option>
                                                            <option value="3">⭐⭐⭐ (Cukup)</option>
                                                            <option value="2">⭐⭐ (Buruk)</option>
                                                            <option value="1">⭐ (Sangat Buruk)</option>
                                                        </select>
                                                    </div>

                                                    <div class="relative">
                                                        <label class="text-[9px] font-bold text-slate-500 uppercase mb-1 block">Ulasan Anda</label>
                                                        <textarea name="comment" rows="2" required placeholder="Bagaimana kualitas batiknya?" 
                                                                  class="w-full text-xs font-medium rounded-xl border-slate-300 text-black placeholder:text-slate-400 focus:ring-2 focus:ring-orange-500 focus:border-black bg-slate-50 p-4"></textarea>
                                                    </div>
                                                    
                                                    <button type="submit" class="bg-black text-white text-[10px] font-black uppercase py-3.5 rounded-xl hover:bg-orange-600 transition-all shadow-md active:scale-95">
                                                        Kirim Ulasan Produk
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h2 class="text-[10px] font-black uppercase tracking-[3px] mb-6 text-gray-400 border-b border-gray-50 pb-4">Shipping Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-xs font-medium">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-black mb-3 tracking-widest">Destination Address</p>
                            <p class="font-black uppercase italic text-black text-sm mb-2">{{ Auth::user()->name }}</p>
                            <p class="text-gray-500 leading-relaxed italic">{{ $order->shipping_address }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-black mb-3 tracking-widest">Logistics Detail</p>
                            <div class="p-4 bg-gray-50 rounded-2xl inline-block w-full">
                                <p class="font-black uppercase italic text-black">{{ strtoupper($order->courier) }}</p>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mt-1 mb-3 border-b border-gray-200 pb-2">
                                    {{ $order->courier_service ?? 'Standard Delivery' }}
                                </p>
                                
                                <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest">Tracking Number (Resi)</p>
                                <p class="font-mono text-sm font-black text-orange-600 mt-1 uppercase tracking-wider">
                                    {{ $order->tracking_number ?? 'Resi belum tersedia' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Payment Summary --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-3xl p-8 sticky top-28 shadow-sm border border-gray-100 overflow-hidden relative">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-gray-50 rounded-full"></div>
                    
                    <h2 class="relative z-10 text-[10px] font-black uppercase tracking-[3px] mb-8 border-b border-gray-100 pb-4 text-gray-400">Payment Summary</h2>
                    
                    <div class="relative z-10 space-y-5 text-[11px] font-bold uppercase tracking-widest">
                        <div class="flex justify-between">
                            <span class="text-gray-400 italic">Subtotal</span>
                            <span class="text-black">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 italic">Shipping Cost</span>
                            <span class="text-black">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="pt-6 border-t border-gray-100 flex flex-col items-end">
                            <span class="text-[9px] font-black uppercase tracking-[4px] text-orange-500 mb-2">Total Transaction</span>
                            <span class="text-3xl font-black italic tracking-tighter text-black">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($order->payment_status == 'unpaid')
                        <button id="pay-button" class="relative z-10 w-full bg-black text-white mt-8 py-5 rounded-2xl font-black text-[10px] uppercase tracking-[3px] hover:bg-orange-500 transition-all shadow-xl active:scale-95">
                            Pay Now
                        </button>
                    @elseif($order->payment_status == 'cancelled')
                        <div class="mt-8 pt-4 border-t border-gray-50 text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-red-600 bg-red-50 px-4 py-2.5 rounded-xl block border border-red-100">
                                ✕ Transaction Expired
                            </span>
                        </div>
                    @else
                        <div class="mt-8 pt-4 border-t border-gray-50 text-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-4 py-2.5 rounded-xl block border border-emerald-100">
                                ✓ Transaction Completed
                            </span>
                        </div>
                    @endif
                </div>

                <div class="mt-6 px-4 py-6 border-2 border-dashed border-gray-100 rounded-3xl text-center">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-300 mb-2">Need Help?</p>
                    <a href="#" class="text-xs font-bold text-gray-500 hover:text-black transition-colors border-b border-gray-200">Contact Customer Support</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT MIDTRANS SNAP INTEGRATION ENGINE --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script type="text/javascript">
    const payButton = document.getElementById('pay-button');
    if (payButton) {
        payButton.onclick = function () {
            window.snap.pay('{{ $order->snap_token }}', {
                onSuccess: function(result) {
                    window.location.reload();
                },
                onPending: function(result) {
                    window.location.reload();
                },
                onError: function(result) {
                    window.location.reload();
                },
                onClose: function() {
                    // Muat ulang halaman untuk memicu pengecekan status otomatis di backend controller
                    window.location.reload();
                }
            });
        };
    }
</script>
@endsection
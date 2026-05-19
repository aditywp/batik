@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-gray-50 pt-32 pb-20 font-sans">
    <div class="max-w-6xl mx-auto px-6">

        <div class="mb-12">
            <h1 class="text-4xl font-black text-black mb-3 italic tracking-tighter uppercase">My Orders</h1>
            <p class="text-gray-500 uppercase text-[10px] font-black tracking-[3px]">
                Riwayat transaksi Anda di Batik Ifawati
            </p>
        </div>

        {{-- BAGIAN FILTER & PENCARIAN PESANAN --}}
        <div class="bg-white p-6 rounded-[24px] border border-gray-100 shadow-sm mb-10">
            <form action="{{ route('customer.orders.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                
                {{-- Input Pencarian Order Code --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Cari ID Pesanan</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: ORDER-12345..." 
                               class="w-full bg-gray-50 border-gray-100 rounded-xl text-xs font-bold focus:ring-black focus:border-black pl-10 h-12 text-black placeholder:text-gray-400">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Input Filter Tanggal --}}
                <div class="w-full md:w-[200px]">
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Tanggal Transaksi</label>
                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                           class="w-full bg-gray-50 border-gray-100 rounded-xl text-xs font-bold focus:ring-black focus:border-black h-12 text-black">
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit" class="flex-1 md:flex-none bg-black text-white px-6 py-3.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all h-12">
                        Cari
                    </button>
                    
                    @if(request()->anyFilled(['search', 'date']))
                        <a href="{{ route('customer.orders.index') }}" 
                           class="px-4 py-3.5 text-[10px] font-black uppercase tracking-widest text-red-500 bg-red-50 hover:bg-red-100 rounded-xl transition-colors flex items-center h-12">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if($orders->isEmpty())
            <div class="bg-white rounded-[40px] p-20 text-center border border-gray-100 shadow-sm">
                <div class="mb-8 flex justify-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-black text-black mb-4 italic uppercase">Belum Ada Pesanan</h2>
                <p class="text-gray-400 text-sm mb-10 max-w-xs mx-auto">Sepertinya data pesanan tidak ditemukan atau Anda belum melakukan transaksi apa pun.</p>
                <a href="{{ route('catalog.index') }}"
                    class="inline-block bg-black text-white px-12 py-5 rounded-2xl font-black text-[10px] uppercase tracking-[3px] hover:bg-orange-600 transition-all shadow-xl active:scale-95">
                    Start Shopping
                </a>
            </div>
        @else
            <div class="space-y-12">
                @foreach($orders as $order)
                    <div class="bg-white rounded-[32px] border border-gray-100 overflow-hidden shadow-sm hover:shadow-md transition-all duration-500 group">
                        
                        <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                            <div class="flex flex-wrap justify-between items-center gap-6">
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 mb-1 tracking-[3px] uppercase italic">Order Identifier</p>
                                    <h2 class="text-2xl font-black text-black italic uppercase tracking-tighter">
                                        #{{ $order->order_code }}
                                    </h2>
                                    <p class="text-[10px] font-bold text-gray-500 mt-2 uppercase tracking-widest">
                                        {{ $order->created_at->format('d M Y — H:i') }} WIB
                                    </p>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="text-right mr-4 hidden md:block">
                                        <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mb-1">Payment Status</p>
                                        <span class="text-[10px] font-black uppercase tracking-widest {{ $order->payment_status == 'paid' ? 'text-emerald-500' : 'text-orange-500' }}">
                                            {{ $order->payment_status }}
                                        </span>
                                    </div>
                                    <div class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[2px] bg-black text-white shadow-xl shadow-black/10">
                                        {{ $order->status }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 lg:p-10">
                            <div class="space-y-10">
                                @foreach($order->items as $item)
                                    @php
                                        // Ambil gambar spesifik dari variant milik item ini, jika tidak ada fallback ke gambar utama produk
                                        $imagePath = $item->variant->image_path 
                                                     ?? $item->product->variants->first()?->image_path 
                                                     ?? 'placeholder.jpg';
                                        
                                        $itemSubtotal = $item->price * $item->quantity;
                                    @endphp

                                    <div class="flex flex-wrap md:flex-nowrap justify-between items-center gap-8 pb-10 border-b border-gray-50 last:border-0 last:pb-0">
                                        
                                        <div class="flex items-center gap-8 flex-1">
                                            <div class="w-24 h-32 flex-shrink-0 rounded-[24px] overflow-hidden bg-gray-100 border border-gray-100 shadow-sm group-hover:shadow-md transition-all">
                                                <img src="{{ asset('storage/' . $imagePath) }}" 
                                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                                     onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}';">
                                            </div>

                                            <div class="flex-1">
                                                <h3 class="font-black text-black text-xl uppercase tracking-tighter leading-none mb-3">
                                                    {{ $item->product->name }}
                                                </h3>
                                                <p class="text-[10px] font-bold text-gray-300 uppercase tracking-[2px] mb-4">
                                                    {{ $item->product->category->name ?? 'Exquisite Collection' }}
                                                </p>
                                                
                                                <div class="flex flex-wrap gap-3">
                                                    <span class="px-3 py-1.5 bg-gray-50 rounded-lg text-[9px] font-black uppercase tracking-widest text-gray-500 border border-gray-100">
                                                        Qty: {{ $item->quantity }}
                                                    </span>
                                                    @if($item->variant)
                                                        <span class="px-3 py-1.5 bg-gray-50 rounded-lg text-[9px] font-black uppercase tracking-widest text-gray-500 border border-gray-100">
                                                            Size: {{ $item->variant->size ?? 'N/A' }}
                                                        </span>
                                                        <span class="px-3 py-1.5 bg-gray-50 rounded-lg text-[9px] font-black uppercase tracking-widest text-gray-500 border border-gray-100">
                                                            Motif: {{ $item->variant->motif ?? 'Original' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right min-w-[150px]">
                                            <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mb-1 italic">Item Value</p>
                                            <h3 class="text-2xl font-black text-black italic tracking-tighter">
                                                Rp{{ number_format($itemSubtotal, 0, ',', '.') }}
                                            </h3>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-8 lg:p-10 bg-gray-50/20 border-t border-gray-50 flex flex-wrap justify-between items-center gap-6">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[4px] mb-2 italic">Total Investment</p>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-[11px] font-black text-gray-300">IDR</span>
                                    <h3 class="text-5xl font-black text-black italic leading-none tracking-tighter">
                                        Rp{{ number_format($order->total, 0, ',', '.') }}
                                    </h3>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-3 items-center">
                                {{-- PERBAIKAN: Form menembak ke route complete khusus customer --}}
                                @if($order->status === 'shipped')
                                    <form action="{{ route('customer.orders.complete', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin pesanan batik ini sudah diterima dengan baik?')" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-8 py-4 bg-emerald-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-[2px] hover:bg-emerald-600 transition-all shadow-md active:scale-95">
                                            Pesanan Selesai / Diterima
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('customer.orders.show', $order->order_code) }}" 
                                   class="px-10 py-4 bg-white border border-gray-200 rounded-2xl text-[10px] font-black uppercase tracking-[2px] text-black hover:bg-black hover:text-white hover:border-black transition-all shadow-sm">
                                    Detailed Receipt
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach

                {{-- NAVIGASI PAGINATION --}}
                <div class="mt-12 customer-pagination">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif

    </div>
</div>

<style>
    /* Halus scrollbar untuk tampilan premium */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f9fafb; }
    ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #000; }

    /* Styling tambahan agar pagination Tailwind Laravel menyatu dengan tema minimalis */
    .customer-pagination nav flex justify-between {
        gap: 12px;
    }
    .customer-pagination svg {
        width: 1rem;
        height: 1rem;
    }
</style>
@endsection
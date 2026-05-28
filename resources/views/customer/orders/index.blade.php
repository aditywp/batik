@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-[#f8f9fa] pt-32 pb-20 font-sans">
    <div class="max-w-6xl mx-auto px-6">

        {{-- FLOATING NOTIFICATION BANNER (TOAST SYSTEM) --}}
        @if(session('success') || session('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 4000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2 md:translate-y-0 md:translate-x-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0 md:translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed bottom-5 right-5 z-50 max-w-sm w-full bg-white border rounded-2xl shadow-xl p-4 flex items-start gap-3 border-stone-150" x-cloak>
                
                @if(session('success'))
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-black uppercase tracking-wider text-emerald-800">Success</p>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">{{ session('success') }}</p>
                    </div>
                @else
                    <div class="p-2 bg-red-50 text-red-600 rounded-xl flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-black uppercase tracking-wider text-red-800">System Error</p>
                        <p class="text-[11px] text-stone-500 font-medium mt-0.5">{{ session('error') }}</p>
                    </div>
                @endif
                <button @click="show = false" class="text-stone-300 hover:text-stone-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        {{-- HEADER TITLE --}}
        <div class="mb-12">
            <h1 class="text-4xl font-black text-black mb-3 italic tracking-tighter uppercase">My Orders</h1>
            <p class="text-gray-500 uppercase text-[10px] font-black tracking-[3px]">
                Riwayat transaksi Anda di Batik Ifawati
            </p>
        </div>

        {{-- FILTER & PENCARIAN PESANAN --}}
        <div class="bg-white p-6 rounded-[24px] border border-gray-100 shadow-sm mb-10">
            <form action="{{ route('customer.orders.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Cari ID Pesanan</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: BI-XXXX..." 
                               class="w-full bg-gray-50 border-gray-100 rounded-xl text-xs font-bold focus:ring-black focus:border-black pl-10 h-12 text-black placeholder:text-gray-400">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="w-full md:w-[200px]">
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Tanggal Transaksi</label>
                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                           class="w-full bg-gray-50 border-gray-100 rounded-xl text-xs font-bold focus:ring-black focus:border-black h-12 text-black">
                </div>

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
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-black text-black mb-4 italic uppercase">No Orders Found</h2>
                <p class="text-gray-400 text-sm mb-10 max-w-xs mx-auto">Data riwayat transaksi pesanan batik tidak ditemukan pada kriteria pencarian ini.</p>
                <a href="{{ route('catalog.index') }}"
                    class="inline-block bg-black text-white px-12 py-5 rounded-2xl font-black text-[10px] uppercase tracking-[3px] hover:bg-orange-600 transition-all shadow-xl">
                    Start Shopping
                </a>
            </div>
        @else
            <div class="space-y-8">
                @foreach($orders as $order)
                    <div class="bg-white rounded-[2rem] border border-gray-100 overflow-hidden shadow-sm hover:shadow-md transition-all duration-500">
                        
                        {{-- SUB-HEADER CARD PESANAN --}}
                        <div class="p-6 md:p-8 border-b border-gray-50 bg-gray-50/40">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 mb-0.5 tracking-wider uppercase">Invoice Code</p>
                                    <h2 class="text-xl font-black text-black font-mono tracking-tight hover:text-orange-500 transition-colors">
                                        <a href="{{ route('customer.orders.show', $order->order_code) }}">#{{ $order->order_code }}</a>
                                    </h2>
                                    <p class="text-[10px] font-semibold text-gray-400 mt-1 uppercase tracking-wider">
                                        {{ $order->created_at->format('d M Y — H:i') }} WIB
                                    </p>
                                </div>

                                <div class="flex items-center gap-3 self-stretch sm:self-auto justify-between sm:justify-end">
                                    <div class="text-left sm:text-right">
                                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Payment Status</p>
                                        {{-- SINKRONISASI KONDISI WARNA STATUS --}}
                                        <span class="text-[10px] font-black uppercase tracking-wide @if($order->payment_status === 'paid') text-emerald-500 @elseif($order->payment_status === 'cancelled') text-red-500 @else text-orange-500 @endif">
                                            ● {{ $order->payment_status }}
                                        </span>
                                    </div>
                                    <div class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-black text-white border border-gray-200/10 shadow-sm">
                                        {{ $order->status }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ITEM LOOP DI DALAM NOTA --}}
                        <div class="p-6 md:p-8 divide-y divide-gray-50">
                            @foreach($order->items as $item)
                                @php
                                    $imagePath = $item->variant->image_path ?? ($item->product->variants->first()?->image_path ?? null);
                                    $itemSubtotal = $item->price * $item->quantity;
                                @endphp

                                <div class="flex items-center justify-between gap-6 py-4 first:pt-0 last:pb-0">
                                    <div class="flex items-center gap-5 flex-1 min-w-0">
                                        <a href="{{ route('customer.orders.show', $order->order_code) }}" class="w-16 h-20 flex-shrink-0 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 shadow-inner group/img block relative hover:scale-105 transition-transform duration-500">
                                            <img src="{{ $imagePath ? asset('storage/' . $imagePath) : asset('images/placeholder.jpg') }}" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                                <span class="text-white text-[8px] font-black uppercase tracking-widest bg-black/60 px-1.5 py-0.5 rounded">Receipt</span>
                                            </div>
                                        </a>

                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-black text-black text-sm uppercase tracking-tight truncate hover:text-orange-500 transition-colors">
                                                <a href="{{ route('customer.orders.show', $order->order_code) }}">{{ $item->product->name }}</a>
                                            </h4>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">{{ $item->product->collection ?? 'Exquisite Collection' }}</p>
                                            
                                            <p class="text-[10px] font-medium text-stone-500 mt-2">
                                                Qty: <span class="font-black text-black">{{ $item->quantity }}</span> 
                                                @if($item->variant)
                                                    · <span class="text-stone-400">Motif: {{ $item->variant->motif ?? 'Standard' }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="text-right flex-shrink-0">
                                        <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mb-0.5">Subtotal</p>
                                        <p class="text-sm font-black text-stone-900 italic">Rp{{ number_format($itemSubtotal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- TOTALAN CARD & AKSI UTAMA INVOICE --}}
                        <div class="p-6 md:p-8 bg-stone-50/30 border-t border-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 italic">Total Order Value</p>
                                <h3 class="text-2xl font-black text-[#1a1a2e] italic tracking-tight">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </h3>
                            </div>
                            
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                @if($order->status === 'shipped')
                                    <form id="complete-form-{{ $order->id }}" action="{{ route('customer.orders.complete', $order->id) }}" method="POST" class="w-full sm:w-auto">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" onclick="triggerComplete({{ $order->id }})" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-wider hover:bg-emerald-700 transition-all shadow-sm">
                                            Diterima
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('customer.orders.show', $order->order_code) }}" 
                                   class="w-full sm:w-auto px-6 py-3 bg-white border border-gray-200 text-center rounded-xl text-[10px] font-black uppercase tracking-wider text-stone-600 hover:bg-[#1a1a2e] hover:text-[#e8c9a0] hover:border-[#1a1a2e] transition-all shadow-sm hover:scale-105">
                                    Detailed Receipt
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach

                {{-- PAGINATION PRESTIGE BERTEMA LUXURY MINIMALIS --}}
                @if($orders->hasPages())
                <div class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm mt-10">
                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                        Page <span class="text-black font-black">{{ $orders->currentPage() }}</span> of <span class="text-black font-black">{{ $orders->lastPage() }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        {{-- Prev Button --}}
                        @if ($orders->onFirstPage())
                            <span class="px-3 py-2 bg-gray-50 text-gray-300 text-[10px] font-black rounded-xl uppercase tracking-wider cursor-not-allowed border border-gray-100">Prev</span>
                        @else
                            <a href="{{ $orders->appends(request()->query())->previousPageUrl() }}" class="px-3 py-2 bg-white border border-gray-200 text-black hover:bg-black hover:text-white text-[10px] font-black rounded-xl uppercase tracking-wider transition-all shadow-sm hover:scale-105">Prev</a>
                        @endif

                        {{-- Halaman Angka --}}
                        <div class="hidden sm:flex items-center gap-1">
                            @foreach ($orders->getUrlRange(max(1, $orders->currentPage() - 2), min($orders->lastPage(), $orders->currentPage() + 2)) as $page => $url)
                                <a href="{{ $url . '&' . http_build_query(request()->except('page')) }}" 
                                   class="w-8 h-8 flex items-center justify-center rounded-xl text-xs font-bold transition-all border {{ $page == $orders->currentPage() ? 'bg-black text-white border-black font-black shadow-sm' : 'bg-white text-gray-500 border-gray-100 hover:border-gray-400 hover:scale-110' }}">
                                    {{ $page }}
                                </a>
                            @endforeach
                        </div>

                        {{-- Next Button --}}
                        @if ($orders->hasMorePages())
                            <a href="{{ $orders->appends(request()->query())->nextPageUrl() }}" class="px-3 py-2 bg-white border border-gray-200 text-black hover:bg-black hover:text-white text-[10px] font-black rounded-xl uppercase tracking-wider transition-all shadow-sm hover:scale-105">Next</a>
                        @else
                            <span class="px-3 py-2 bg-gray-100 text-gray-300 text-[10px] font-black rounded-xl uppercase tracking-wider cursor-not-allowed border border-gray-100">Next</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        @endif

    </div>
</div>

{{-- SCRIPT JAVASCRIPT SWEETALERT2 INTEGRATION ENGINE --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function triggerComplete(orderId) {
        Swal.fire({
            title: 'Pesanan Sudah Diterima?',
            text: "Pastikan Pesanan Anda telah diperiksa dan dalam kondisi baik sebelum mengonfirmasi!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: '<span style="color: #ffffff; font-weight: 800; text-transform: uppercase; font-size: 11px; tracking-wide: 1px;">Ya, Sudah Diterima</span>',
            cancelButtonText: '<span style="color: #4b5563; font-weight: 800; text-transform: uppercase; font-size: 11px; tracking-wide: 1px;">Batal</span>',
            customClass: {
                popup: 'rounded-[24px]',
                title: 'font-sans font-black text-[#1a1a2e]'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('complete-form-' + orderId).submit();
            }
        });
    }
</script>

<style>
    /* Styling scrollbar */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #000; }
</style>
@endsection
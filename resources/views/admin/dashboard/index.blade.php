@extends('layouts.admin')

@section('content')
<div class="p-8 bg-gray-50/50 min-h-screen font-sans">
    
    {{-- HEADER OVERVIEW & QUICK ACTIONS --}}
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-[#1a1a2e] tracking-tight uppercase italic">Dashboard Overview</h1>
            <p class="text-gray-500 text-sm mt-1">Sistem kendali operasional, manajemen inventori, dan pemantauan penjualan Batik Ifawati.</p>
        </div>
        
        {{-- TOMBOL NAVIGATION SHORTCUT EKSTRAPOLASI --}}
        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <a href="{{ route('admin.orders.report') }}" class="bg-white border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-gray-50 hover:text-[#1a1a2e] transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4 text-[#1a1a2e]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/>
                </svg>
                Buka Analytics
            </a>
            <div class="text-xs font-black text-[#1a1a2e] bg-[#e8c9a0]/20 border border-[#e8c9a0]/30 px-4 py-3 rounded-xl shadow-sm uppercase tracking-widest whitespace-nowrap">
                📅 {{ date('d F Y') }}
            </div>
        </div>
    </div>

    {{-- ALERT PANTAUAN STOK REAL-TIME --}}
    @php
        $lowStockCount = \App\Models\Product::where('stock', '<=', 5)->count();
    @endphp
    @if($lowStockCount > 0)
        <div class="mb-8 bg-red-50/60 border border-red-100 rounded-2xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="bg-red-600 text-white p-2.5 rounded-xl shadow-md shadow-red-200 animate-bounce">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-red-900 uppercase tracking-wide">Peringatan Kritis Logistik Stok</h4>
                    <p class="text-xs text-red-700 font-medium mt-0.5">Terdapat <span class="font-extrabold">{{ $lowStockCount }} variasi kain batik</span> yang berada di batas minimum ambang batas (≤ 5 pcs) atau telah habis.</p>
                </div>
            </div>
            <a href="{{ route('admin.products.index', ['stock_status' => 'low']) }}" class="bg-red-950 text-[#e8c9a0] text-xs font-black px-4 py-2.5 rounded-xl uppercase tracking-wider hover:bg-black transition shadow-sm whitespace-nowrap w-full sm:w-auto text-center">
                Restok Barang
            </a>
        </div>
    @endif

    {{-- INTERACTIVE STATS GRID LENGKAP --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        {{-- Card Omset --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:translate-y-[-4px] transition-all duration-300">
            <div class="bg-emerald-50 text-emerald-600 p-4 rounded-2xl border border-emerald-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Revenue</p>
                <h2 class="text-xl font-black text-[#1a1a2e] mt-0.5 italic tracking-tight">
                    Rp {{ number_format(\App\Models\Order::where('payment_status', 'paid')->sum('total'), 0, ',', '.') }}
                </h2>
            </div>
        </div>

        {{-- Card Jumlah Pesanan --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:translate-y-[-4px] transition-all duration-300">
            <div class="bg-blue-50 text-blue-600 p-4 rounded-2xl border border-blue-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Orders</p>
                <h2 class="text-2xl font-black text-[#1a1a2e] mt-0.5 italic tracking-tight">
                    {{ $totalOrders }} <span class="text-xs font-normal text-gray-400 not-italic">Nota</span>
                </h2>
            </div>
        </div>

        {{-- Card Katalog --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:translate-y-[-4px] transition-all duration-300">
            <div class="bg-amber-50 text-amber-600 p-4 rounded-2xl border border-amber-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Katalog Produk</p>
                <h2 class="text-2xl font-black text-[#1a1a2e] mt-0.5 italic tracking-tight">
                    {{ $totalProducts }} <span class="text-xs font-normal text-gray-400 not-italic">Desain</span>
                </h2>
            </div>
        </div>

        {{-- Card Pelanggan --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:translate-y-[-4px] transition-all duration-300">
            <div class="bg-purple-50 text-purple-600 p-4 rounded-2xl border border-purple-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Registered Clients</p>
                <h2 class="text-2xl font-black text-[#1a1a2e] mt-0.5 italic tracking-tight">
                    {{ $totalCustomers }} <span class="text-xs font-normal text-gray-400 not-italic">Akun</span>
                </h2>
            </div>
        </div>
    </div>

    {{-- DATA INTERAKTIF SECTION: AKTIVITAS PESANAN TERBARU --}}
    @php
        $latestOrders = \App\Models\Order::with('user')->latest()->take(5)->get();
    @endphp
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-gray-50/20">
            <div>
                <h3 class="text-sm font-black text-[#1a1a2e] uppercase tracking-wider italic">Real-Time Transaction Activity</h3>
                <p class="text-xs text-gray-400 font-medium">Memantau 5 riwayat invoice pembayaran terakhir masuk ke sistem.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-black text-blue-600 uppercase tracking-widest hover:text-blue-800 transition-colors">
                Lihat Semua Log Transaksi →
            </a>
        </div>

        @if($latestOrders->isEmpty())
            <div class="p-16 text-center">
                <p class="text-gray-400 italic text-sm">Belum ada aktivitas pendaftaran pesanan baru di dalam sistem database.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/40">
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID Nota</th>
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Pelanggan</th>
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal Order</th>
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Nominal</th>
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status Operasional</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach($latestOrders as $lOrder)
                            <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $lOrder->id) }}'">
                                <td class="p-5 font-black text-[#1a1a2e] font-mono text-xs">
                                    #{{ $lOrder->order_code }}
                                </td>
                                <td class="p-5">
                                    <p class="font-black text-gray-800 leading-tight">{{ $lOrder->user->name ?? 'Pembeli Umum' }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $lOrder->user->email ?? '-' }}</p>
                                </td>
                                <td class="p-5 text-gray-500 font-medium">
                                    {{ $lOrder->created_at->format('d M Y — H:i') }} WIB
                                </td>
                                <td class="p-5 font-extrabold text-[#1a1a2e]">
                                    Rp {{ number_format($lOrder->total, 0, ',', '.') }}
                                </td>
                                <td class="p-5 text-center" onclick="event.stopPropagation();">
                                    @if($lOrder->status === 'delivered')
                                        <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-xl text-[10px] font-black uppercase tracking-wider">Selesai</span>
                                    @elseif($lOrder->status === 'shipped')
                                        <span class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-xl text-[10px] font-black uppercase tracking-wider">Dikirim</span>
                                    @elseif($lOrder->status === 'processing')
                                        <span class="px-3 py-1.5 bg-amber-50 text-amber-700 rounded-xl text-[10px] font-black uppercase tracking-wider">Diproses</span>
                                    @elseif($lOrder->status === 'cancelled')
                                        <span class="px-3 py-1.5 bg-red-50 text-red-700 rounded-xl text-[10px] font-black uppercase tracking-wider">Batal</span>
                                    @else
                                        <span class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-wider">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- COMPREHENSIVE LAYOUT BOTTOM: PRODUK TERLARIS & STATS LOGISTIK --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @php
            $topProducts = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_sold'))
                ->whereHas('order', function($q) {
                    $q->where('payment_status', 'paid');
                })
                ->groupBy('product_id')
                ->orderByDesc('total_sold')
                ->take(4)
                ->with('product')
                ->get();
        @endphp
        {{-- Widget Produk Laris Berfoto --}}
        <div class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-black text-[#1a1a2e] uppercase tracking-wider italic mb-4">🔥 Produk Terlaris (Top Selling Items)</h3>
                @if($topProducts->isEmpty())
                    <div class="py-8 text-center text-gray-400 italic text-xs">Belum ada data penjualan kain batik yang tercatat.</div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($topProducts as $index => $item)
                            @if($item->product)
                                <div class="flex items-center gap-3 p-3 bg-gray-50/50 rounded-2xl border border-gray-100">
                                    <div class="w-12 h-12 bg-white rounded-xl overflow-hidden flex-shrink-0 border border-gray-200">
                                        <img src="{{ asset('storage/' . ($item->product->variants->first()->image_path ?? 'placeholder.jpg')) }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-xs font-black text-[#1a1a2e] truncate">{{ $item->product->name }}</h4>
                                        <p class="text-[10px] font-bold text-emerald-600">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                                        <p class="text-[9px] text-gray-400 font-semibold uppercase mt-0.5 tracking-wider">{{ $item->product->collection }}</p>
                                    </div>
                                    <span class="text-[10px] font-black bg-[#1a1a2e] text-[#e8c9a0] px-2.5 py-1 rounded-lg">
                                        {{ $item->total_sold }} pcs
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
            <p class="text-[9px] text-gray-400 mt-4 pt-2 border-t border-gray-50">Kalkulasi omset disaring berdasarkan status gerbang pembayaran Midtrans lunas.</p>
        </div>

        {{-- Widget Logistik Status --}}
        <div class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-black text-[#1a1a2e] uppercase tracking-wider italic mb-4">Logistik Status</h3>
                <div class="space-y-3 text-xs font-bold">
                    <div class="flex justify-between items-center p-3 bg-gray-50/60 rounded-2xl border border-gray-100">
                        <span class="text-gray-500 uppercase tracking-wider font-black text-[9px]">Menunggu Pembayaran</span>
                        <span class="text-[#1a1a2e] font-black text-xs bg-white px-2.5 py-1 rounded-xl border shadow-sm">
                            {{ \App\Models\Order::where('status', 'pending')->count() }} <span class="text-[9px] text-gray-400 font-medium">Order</span>
                        </span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-amber-50/50 rounded-2xl border border-amber-100">
                        <span class="text-amber-800 uppercase tracking-wider font-black text-[9px]">Perlu Dikemas & Proses</span>
                        <span class="text-amber-700 font-black text-xs bg-white px-2.5 py-1 rounded-xl border shadow-sm">
                            {{ \App\Models\Order::where('status', 'processing')->count() }} <span class="text-[9px] text-gray-400 font-medium">Order</span>
                        </span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-blue-50/50 rounded-2xl border border-blue-100">
                        <span class="text-blue-800 uppercase tracking-wider font-black text-[9px]">Sedang Di Kurir Jalan</span>
                        <span class="text-blue-700 font-black text-xs bg-white px-2.5 py-1 rounded-xl border shadow-sm">
                            {{ \App\Models\Order::where('status', 'shipped')->count() }} <span class="text-[9px] text-gray-400 font-medium">Order</span>
                        </span>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-[10px] text-center font-black uppercase text-stone-600 bg-gray-100 hover:bg-[#1a1a2e] hover:text-[#e8c9a0] py-3 rounded-2xl transition-all mt-4 tracking-wider">
                Proses Logistik Pengiriman
            </a>
        </div>
    </div>
</div>
@endsection
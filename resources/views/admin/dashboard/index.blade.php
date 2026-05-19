@extends('layouts.admin')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen font-sans">
    
    {{-- HEADER OVERVIEW --}}
    <div class="mb-10 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-[#1a1a2e] tracking-tight uppercase italic">Dashboard Overview</h1>
            <p class="text-gray-500 text-sm mt-1">Data analitik dan manajemen operasional Batik Ifawati.</p>
        </div>
        <div class="text-sm font-bold text-gray-400 bg-white border border-gray-100 px-4 py-2.5 rounded-xl shadow-sm">
            📅 {{ date('d F Y') }}
        </div>
    </div>

    {{-- ALERT PANTAUAN STOK REAL-TIME (Deteksi Otomatis Kain Menipis/Habis) --}}
    @php
        // Mengambil jumlah produk yang stoknya menipis (<= 5) atau habis (0) langsung dari koleksi database
        $lowStockCount = \App\Models\Product::where('stock', '<=', 5)->count();
    @endphp
    @if($lowStockCount > 0)
        <div class="mb-8 bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-center justify-between shadow-sm animate-pulse">
            <div class="flex items-center gap-4">
                <div class="bg-amber-500 text-white p-2 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-amber-900 uppercase">Perhatian Sistem Inventori</h4>
                    <p class="text-xs text-amber-700 font-medium mt-0.5">Terdapat <span class="font-extrabold">{{ $lowStockCount }} produk batik</span> yang status stoknya menipis atau telah habis terjual.</p>
                </div>
            </div>
            <a href="{{ route('admin.products.index', ['stock_status' => 'low']) }}" class="bg-amber-900 text-white text-xs font-black px-4 py-2 rounded-xl uppercase tracking-wider hover:bg-black transition-all">
                Kelola Stok
            </a>
        </div>
    @endif

    {{-- INTERACTIVE STATS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-all duration-300">
            <div class="bg-emerald-100 text-emerald-600 p-4 rounded-2xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Revenue</p>
                <h2 class="text-2xl font-black text-[#1a1a2e] mt-1 italic tracking-tight">
                    Rp {{ number_format(\App\Models\Order::where('payment_status', 'paid')->sum('total'), 0, ',', '.') }}
                </h2>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-all duration-300">
            <div class="bg-blue-100 text-blue-600 p-4 rounded-2xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Orders</p>
                <h2 class="text-3xl font-black text-[#1a1a2e] mt-1 italic tracking-tight">{{ $totalOrders }} <span class="text-xs font-normal text-gray-400">nota</span></h2>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-all duration-300">
            <div class="bg-amber-100 text-amber-600 p-4 rounded-2xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Batik Items</p>
                <h2 class="text-3xl font-black text-[#1a1a2e] mt-1 italic tracking-tight">{{ $totalProducts }} <span class="text-xs font-normal text-gray-400">katalog</span></h2>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition-all duration-300">
            <div class="bg-purple-100 text-purple-600 p-4 rounded-2xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Active Clients</p>
                <h2 class="text-3xl font-black text-[#1a1a2e] mt-1 italic tracking-tight">{{ $totalCustomers }} <span class="text-xs font-normal text-gray-400">user</span></h2>
            </div>
        </div>
    </div>

    {{-- DATA INTERAKTIF SECTION: AKTIVITAS PESANAN TERBARU --}}
    @php
        // Mengambil 5 data transaksi teranyar untuk di-preview di halaman utama dashboard admin
        $latestOrders = \App\Models\Order::with('user')->latest()->take(5)->get();
    @endphp
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 lg:p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
            <h3 class="text-lg font-black text-[#1a1a2e] uppercase tracking-wider italic">Aktivitas Transaksi Terakhir</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-black text-blue-500 uppercase tracking-widest hover:text-blue-700 transition-colors">
                Lihat Semua Pesanan →
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
                        <tr class="border-b border-gray-100 bg-gray-50/10">
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID Nota</th>
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Pelanggan</th>
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal Order</th>
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Nominal</th>
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach($latestOrders as $lOrder)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-5 font-black text-[#1a1a2e]">
                                    <a href="{{ route('admin.orders.show', $lOrder->id) }}" class="hover:text-blue-600 transition-colors">
                                        #{{ $lOrder->order_code }}
                                    </a>
                                </td>
                                <td class="p-5 font-bold text-gray-700">
                                    {{ $lOrder->user->name ?? 'Pembeli Umum' }}
                                </td>
                                <td class="p-5 text-gray-500 font-medium">
                                    {{ $lOrder->created_at->format('d M Y — H:i') }} WIB
                                </td>
                                <td class="p-5 font-extrabold text-[#1a1a2e]">
                                    Rp {{ number_format($lOrder->total, 0, ',', '.') }}
                                </td>
                                <td class="p-5 text-center">
                                    {{-- Render dinamis badge warna premium berdasarkan status orderan --}}
                                    @if($lOrder->status === 'delivered')
                                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-wider">Selesai</span>
                                    @elseif($lOrder->status === 'shipped')
                                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[10px] font-black uppercase tracking-wider">Dikirim</span>
                                    @elseif($lOrder->status === 'processing')
                                        <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] font-black uppercase tracking-wider">Diproses</span>
                                    @elseif($lOrder->status === 'cancelled')
                                        <span class="px-3 py-1 bg-red-50 text-red-700 rounded-full text-[10px] font-black uppercase tracking-wider">Batal</span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-black uppercase tracking-wider">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- LAYOUT BARU: WIDGET TAMBAHAN (PRODUK TERLARIS & STATS OPERASIONAL) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-12">
        
        @php
            // Mendapatkan data produk terlaris dengan mengagregasikan kuantitas tabel order_items yang sukses (paid)
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
        <div class="bg-white rounded-[32px] p-6 lg:p-8 border border-gray-100 shadow-sm lg:col-span-2">
            <h3 class="text-sm font-black text-[#1a1a2e] uppercase tracking-wider italic mb-6">🔥 Produk Terlaris (Top Selling Items)</h3>
            
            @if($topProducts->isEmpty())
                <div class="py-8 text-center text-gray-400 italic text-xs">Belum ada data penjualan kain batik yang tercatat.</div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($topProducts as $index => $item)
                        @if($item->product)
                            <div class="flex items-center justify-between py-3.5 first:pt-0 last:pb-0">
                                <div class="flex items-center gap-4">
                                    <span class="w-6 h-6 bg-slate-900 text-[#e8c9a0] text-xs font-black flex items-center justify-center rounded-full shadow-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <p class="font-extrabold text-[#1a1a2e] text-sm">{{ $item->product->name }}</p>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mt-0.5">{{ $item->product->collection }} Collection</p>
                                    </div>
                                </div>
                                <span class="text-xs font-black bg-emerald-50 text-emerald-700 px-3 py-1 rounded-xl">
                                    {{ $item->total_sold }} Terjual
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-[32px] p-6 lg:p-8 border border-gray-100 shadow-sm">
            <h3 class="text-sm font-black text-[#1a1a2e] uppercase tracking-wider italic mb-6">Logistik Status</h3>
            <div class="space-y-4 text-xs font-bold">
                
                <div class="flex justify-between items-center p-3.5 bg-gray-50 rounded-xl border border-gray-100/50">
                    <span class="text-gray-500 uppercase tracking-wider font-black text-[10px]">Menunggu Pembayaran</span>
                    <span class="text-[#1a1a2e] font-black text-sm bg-white px-2.5 py-1 rounded-lg border shadow-sm">
                        {{ \App\Models\Order::where('status', 'pending')->count() }} <span class="text-[10px] text-gray-400 font-medium">Order</span>
                    </span>
                </div>

                <div class="flex justify-between items-center p-3.5 bg-amber-50/70 rounded-xl border border-amber-100/50">
                    <span class="text-amber-800 uppercase tracking-wider font-black text-[10px]">Perlu Dikemas & Proses</span>
                    <span class="text-amber-700 font-black text-sm bg-white px-2.5 py-1 rounded-lg border shadow-sm">
                        {{ \App\Models\Order::where('status', 'processing')->count() }} <span class="text-[10px] text-gray-400 font-medium">Order</span>
                    </span>
                </div>

                <div class="flex justify-between items-center p-3.5 bg-blue-50/70 rounded-xl border border-blue-100/50">
                    <span class="text-blue-800 uppercase tracking-wider font-black text-[10px]">Sedang Di Kurir Jalan</span>
                    <span class="text-blue-700 font-black text-sm bg-white px-2.5 py-1 rounded-lg border shadow-sm">
                        {{ \App\Models\Order::where('status', 'shipped')->count() }} <span class="text-[10px] text-gray-400 font-medium">Order</span>
                    </span>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
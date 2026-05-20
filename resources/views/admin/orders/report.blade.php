@extends('layouts.admin')

@section('content')
{{-- Style khusus untuk kenyamanan UI/UX dashboard premium --}}
<style>
    [x-cloak] { display: none !important; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="container mx-auto p-6 font-sans" x-data="{ showFilters: true, isExporting: false }">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-4xl font-black text-[#1a1a2e] tracking-tighter uppercase italic">Analytics Center</h1>
            <p class="text-slate-400 text-sm font-medium">Laporan performa penjualan komprehensif & rekapitulasi finansial toko.</p>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            {{-- Tombol Toggle Panel Filter Modern --}}
            <button @click="showFilters = !showFilters" class="bg-white border border-slate-200 text-slate-700 px-5 py-3 rounded-2xl font-bold hover:bg-slate-50 transition flex items-center gap-2 shadow-sm text-sm whitespace-nowrap">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Saring Periode
            </button>
            
            {{-- Tombol Export Excel Premium dengan Loading Feedback --}}
            <a href="{{ route('admin.orders.exportExcel', request()->query()) }}" 
               @click="isExporting = true; setTimeout(() => isExporting = false, 4000)"
               class="bg-[#1a1a2e] text-[#e8c9a0] px-6 py-3 rounded-2xl font-bold hover:opacity-90 transition flex items-center justify-center gap-2 shadow-xl shadow-slate-200 text-sm w-full md:w-auto">
                <template x-if="!isExporting">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#e8c9a0]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Export Excel Premium</span>
                    </div>
                </template>
                <template x-if="isExporting">
                    <div class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-[#e8c9a0]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Menyiapkan Berkas...</span>
                    </div>
                </template>
            </a>
        </div>
    </div>

    {{-- Filter Panel Berbasis Dropdown Multi-Periode Profesional --}}
    <div x-show="showFilters" x-collapse class="mb-8 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm" x-cloak>
        <form action="{{ route('admin.orders.report') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tahun Analisis</label>
                <select name="year" class="w-full bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-[#e8c9a0] text-sm font-bold py-3 text-slate-700">
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Bulan (Agregasi Data)</label>
                <select name="month" class="w-full bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-[#e8c9a0] text-sm font-bold py-3 text-slate-700">
                    <option value="">-- Semua Bulan (Rekap Setahun) --</option>
                    @php
                        $namaBulan = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
                    @foreach($namaBulan as $num => $name)
                        <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-[#e8c9a0] text-[#1a1a2e] py-3.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#d4b78d] transition shadow-sm">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- KELOMPOK BARU: 3 CARD STATISTIK UTAMA LEBIH LENGKAP --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Card Pendapatan Finansial --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Gross Revenue</span>
                    <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">{{ $totalTransactions }} Trx</span>
                </div>
                <h3 class="text-2xl font-black text-[#1a1a2e]">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
            <div class="mt-4 border-t border-slate-50 pt-3">
                @php
                    $grandTotalAllTime = \App\Models\Order::where('payment_status', 'paid')->sum('total');
                    $percentageContribution = $grandTotalAllTime > 0 ? ($totalRevenue / $grandTotalAllTime) * 100 : 0;
                @endphp
                <p class="text-[11px] text-slate-400 font-semibold">
                    Kontribusi periode ini: <span class="text-emerald-600 font-black">↑ {{ number_format($percentageContribution, 1) }}%</span> dari seumur hidup toko.
                </p>
            </div>
        </div>

        {{-- CARD BARU: Estimasi Profit Bersih (Asumsi Margin Bersih 75% Setelah Biaya Kain) --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Net Profit Estimation</span>
                    <span class="text-[10px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Margin 75%</span>
                </div>
                <h3 class="text-2xl font-black text-emerald-600">Rp {{ number_format($totalRevenue * 0.75, 0, ',', '.') }}</h3>
            </div>
            <div class="mt-4 border-t border-slate-50 pt-3">
                <p class="text-[11px] text-slate-400 font-semibold">
                    Estimasi Harga Pokok Penjualan (HPP): <span class="text-red-500 font-bold">Rp {{ number_format($totalRevenue * 0.25, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>

        {{-- CARD BARU: Kontribusi Berdasarkan Kategori Koleksi Terlaris --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-3">Top Collection Category</span>
                <div class="space-y-2">
                    @php
                        // Mengambil performa koleksi terlaris langsung dari DB pembungkus item order
                        $collectionStats = \App\Models\OrderItem::selectRaw('products.collection, COUNT(*) as count')
                            ->join('products', 'order_items.product_id', '=', 'products.id')
                            ->join('orders', 'order_items.order_id', '=', 'orders.id')
                            ->where('orders.payment_status', 'paid')
                            ->groupBy('products.collection')
                            ->orderByDesc('count')
                            ->take(2)
                            ->get();
                    @endphp
                    @forelse($collectionStats as $stat)
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-700">{{ $stat->collection ?? 'Uncategorized' }}</span>
                            <span class="font-mono text-slate-400 bg-slate-50 px-2 py-0.5 rounded">{{ $stat->count }} item terjual</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada data barang terjual</p>
                    @endforelse
                </div>
            </div>
            <div class="text-[10px] text-slate-400 font-medium border-t border-slate-50 pt-2 mt-2">
                Diperbarui secara instan sesuai payment gateway.
            </div>
        </div>
    </div>

    {{-- KELOMPOK TENGAH: GRAFIK & PRODUK TERLARIS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        {{-- Grafik Utama Interaktif (Chart.js) --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col justify-between">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-4">{{ $chartTitle }}</p>
            <div class="h-[210px]">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- SEKSI BARU: TABEL MINI TOP SELLING PRODUCTS --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-black text-[#1a1a2e] uppercase tracking-wider mb-4">Top 3 Selling Items</h3>
                <div class="divide-y divide-slate-50">
                    @php
                        $topProducts = \App\Models\OrderItem::selectRaw('product_id, SUM(quantity) as total_qty')
                            ->join('orders', 'order_items.order_id', '=', 'orders.id')
                            ->where('orders.payment_status', 'paid')
                            ->groupBy('product_id')
                            ->orderByDesc('total_qty')
                            ->take(3)
                            ->with('product')
                            ->get();
                    @endphp
                    @forelse($topProducts as $tp)
                    <div class="flex items-center gap-3 py-2.5 first:pt-0 last:pb-0">
                        <div class="w-10 h-10 bg-slate-50 rounded-xl overflow-hidden flex-shrink-0 border border-slate-100">
                            <img src="{{ asset('storage/' . ($tp->product->variants->first()->image_path ?? 'placeholder.jpg')) }}" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-black text-[#1a1a2e] truncate">{{ $tp->product->name ?? 'Produk Dihapus' }}</h4>
                            <p class="text-[10px] text-emerald-600 font-bold">Rp {{ number_format($tp->product->price ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-black text-slate-700 bg-amber-50 text-amber-800 px-2 py-1 rounded-lg">{{ $tp->total_qty }} pcs</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 italic py-4 text-center">Belum ada komparasi data penjualan produk.</p>
                    @endforelse
                </div>
            </div>
            <p class="text-[9px] text-slate-400 mt-4 pt-2 border-t border-slate-50">Mengabaikan pesanan batal/belum dibayar.</p>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h3 class="font-black text-[#1a1a2e] uppercase tracking-widest text-xs">Transaction Logs</h3>
            <span class="text-[10px] bg-slate-100 text-slate-500 px-3 py-1 rounded-full font-bold">Total Baris: {{ $orders->total() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">ID</th>
                        <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer Details</th>
                        <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Amount</th>
                        <th class="p-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="p-6 font-mono text-xs font-bold text-slate-400 group-hover:text-[#1a1a2e]">#{{ $order->order_code }}</td>
                        <td class="p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black text-[#1a1a2e]">
                                    {{ strtoupper(substr($order->user->name ?? 'U', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-black text-sm text-slate-800 tracking-tight">{{ $order->user->name ?? 'Pembeli Umum' }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $order->user->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-6 text-center font-black text-sm italic text-slate-800">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </td>
                        <td class="p-6 text-center">
                            <span class="text-[10px] font-bold text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                                {{ $order->created_at->format('M d, Y') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-16 text-center text-slate-400 italic text-sm">
                            Tidak ada data transaksi lunas pada periode filter ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- UI Pagination Premium Kustomisasi --}}
        @if($orders->hasPages())
        <div class="px-8 py-5 border-t border-slate-50 bg-slate-50/30 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                Menampilkan <span class="text-[#1a1a2e] font-black">{{ $orders->firstItem() }}</span> - <span class="text-[#1a1a2e] font-black">{{ $orders->lastItem() }}</span> dari <span class="text-[#1a1a2e] font-black">{{ $orders->total() }}</span> Log Transaksi
            </div>
            <div class="flex items-center gap-1">
                @if ($orders->onFirstPage())
                    <span class="px-3 py-2 bg-gray-100 text-gray-300 text-xs font-black rounded-xl uppercase tracking-widest cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $orders->appends(request()->query())->previousPageUrl() }}" class="px-3 py-2 bg-white border border-gray-200 text-gray-600 hover:border-[#1a1a2e] hover:text-[#1a1a2e] text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-sm">Prev</a>
                @endif

                <div class="hidden sm:flex items-center gap-1">
                    @foreach ($orders->getUrlRange(max(1, $orders->currentPage() - 2), min($orders->lastPage(), $orders->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url . '&' . http_build_query(request()->except('page')) }}" 
                           class="w-9 h-9 flex items-center justify-center rounded-xl text-xs font-bold transition-all border {{ $page == $orders->currentPage() ? 'bg-[#1a1a2e] text-[#e8c9a0] border-[#1a1a2e] font-black shadow-md shadow-[#1a1a2e]/10' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-400' }}">
                            {{ $page }}
                        </a>
                    @endforeach
                </div>

                @if ($orders->hasMorePages())
                    <a href="{{ $orders->appends(request()->query())->nextPageUrl() }}" class="px-3 py-2 bg-white border border-gray-200 text-gray-600 hover:border-[#1a1a2e] hover:text-[#1a1a2e] text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-sm">Next</a>
                @else
                    <span class="px-3 py-2 bg-gray-100 text-gray-300 text-xs font-black rounded-xl uppercase tracking-widest cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- RUNTIME GRAPHIC ENGINE JAVASCRIPT (CHART.JS) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        const labels = {!! json_encode($chartLabels ?? []) !!};
        const dataValues = {!! json_encode($chartData ?? []) !!};
        const isMonthlyMode = {!! json_encode($selectedMonth ? true : false) !!};

        new Chart(ctx, {
            type: isMonthlyMode ? 'line' : 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Omset Penjualan',
                    data: dataValues,
                    borderColor: '#1a1a2e',
                    backgroundColor: isMonthlyMode ? 'rgba(26, 26, 46, 0.04)' : '#e8c9a0',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.38,
                    pointRadius: 4,
                    pointBackgroundColor: '#e8c9a0',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 15,
                        right: 10
                    }
                },
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, family: 'Plus Jakarta Sans' } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f8fafc' },
                        ticks: { 
                            font: { size: 10, family: 'Plus Jakarta Sans' },
                            padding: 8, 
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
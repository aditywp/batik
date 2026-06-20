@extends('layouts.admin')

@section('content')
{{-- Style khusus untuk kenyamanan UI/UX dashboard premium --}}
<style>
    [x-cloak] { display: none !important; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    
    /* Custom select style for modern look */
    .modern-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
    }
</style>

<div class="container mx-auto p-4 md:p-8 font-sans" x-data="{ isExporting: false }">
    
    {{-- 1. HEADER & INLINE FILTER SECTION --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end mb-8 gap-6">
        <div>
            <h1 class="text-3xl font-black text-[#1a1a2e] tracking-tight mb-1">Analytics Center</h1>
            <p class="text-slate-500 text-sm font-medium">Pantau performa penjualan dan tren transaksi toko Anda.</p>
        </div>
        
        {{-- Kontrol Panel (Filter & Export) --}}
        <div class="w-full xl:w-auto flex flex-col md:flex-row items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-slate-200">
            
            {{-- Form Filter Inline --}}
            <form action="{{ route('admin.orders.report') }}" method="GET" class="flex flex-col md:flex-row w-full md:w-auto items-center gap-2">
                <div class="relative w-full md:w-36">
                    <select name="year" onchange="this.form.submit()" class="modern-select w-full bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-[#1a1a2e] text-xs font-bold py-2.5 pl-4 pr-8 text-slate-700 cursor-pointer transition-all hover:bg-slate-100">
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="relative w-full md:w-48">
                    <select name="month" onchange="this.form.submit()" class="modern-select w-full bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-[#1a1a2e] text-xs font-bold py-2.5 pl-4 pr-8 text-slate-700 cursor-pointer transition-all hover:bg-slate-100">
                        <option value="">Semua Bulan (Setahun)</option>
                        @php
                            $namaBulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                        @endphp
                        @foreach($namaBulan as $num => $name)
                            <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="hidden md:block w-px h-8 bg-slate-200 mx-1"></div>

            {{-- Tombol Export --}}
            <a href="{{ route('admin.orders.exportExcel', request()->query()) }}" 
               @click="isExporting = true; setTimeout(() => isExporting = false, 4000)"
               class="w-full md:w-auto bg-[#1a1a2e] text-white px-5 py-2.5 rounded-xl font-bold hover:bg-black transition-all flex items-center justify-center gap-2 text-xs shadow-md">
                <template x-if="!isExporting">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#e8c9a0]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh Excel</span>
                    </div>
                </template>
                <template x-if="isExporting">
                    <div class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-[#e8c9a0]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Menyiapkan...</span>
                    </div>
                </template>
            </a>
        </div>
    </div>

    {{-- 2. HERO DASHBOARD (REVENUE & CHART) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        {{-- Hero Card: Gross Revenue --}}
        <div class="lg:col-span-1 bg-[#1a1a2e] p-8 rounded-[2rem] shadow-xl relative overflow-hidden flex flex-col justify-between text-white">
            <div class="absolute -right-16 -top-16 opacity-5 pointer-events-none">
                <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
            </div>
            
            <div class="relative z-10">
                <h3 class="text-xs font-black text-[#e8c9a0] uppercase tracking-widest mb-2 opacity-90">Total Pendapatan</h3>
                <div class="text-4xl sm:text-5xl font-black tracking-tight mb-2">
                    <span class="text-xl font-medium text-slate-400 mr-1">Rp</span>{{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
                
                @php
                    $grandTotalAllTime = \App\Models\Order::where('payment_status', 'paid')->sum('total');
                    $percentageContribution = $grandTotalAllTime > 0 ? ($totalRevenue / $grandTotalAllTime) * 100 : 0;
                @endphp
                
                <div class="inline-flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-lg mt-2 backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    <span class="text-[11px] font-bold text-white">{{ number_format($percentageContribution, 1) }}% dari total selamanya</span>
                </div>
            </div>

            <div class="relative z-10 mt-8 pt-6 border-t border-white/10 flex justify-between items-end">
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mb-1">Transaksi Lunas</p>
                    <p class="text-2xl font-black">{{ $totalTransactions }} <span class="text-sm font-medium text-slate-400">Trx</span></p>
                </div>
                <div class="w-12 h-12 rounded-full bg-[#e8c9a0]/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#e8c9a0]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
        </div>

        {{-- Grafik Penjualan (Chart) --}}
        <div class="lg:col-span-2 bg-white p-6 md:p-8 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-black text-slate-800 tracking-tight">{{ $chartTitle }}</h3>
                <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full">Real-time</span>
            </div>
            <div class="h-[240px] w-full">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 3. BOTTOM SECTION (TOP PRODUCTS & LOGS) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Tabel Mini Top Products --}}
        <div class="lg:col-span-1 bg-white p-6 md:p-8 rounded-[2rem] border border-slate-100 shadow-sm h-fit">
            <h3 class="text-sm font-black text-[#1a1a2e] mb-6">Produk Paling Laris</h3>
            <div class="space-y-4">
                @php
                    $topProducts = \App\Models\OrderItem::selectRaw('product_id, SUM(quantity) as total_qty')
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->where('orders.payment_status', 'paid')
                        ->groupBy('product_id')
                        ->orderByDesc('total_qty')
                        ->take(4)
                        ->with('product')
                        ->get();
                @endphp
                
                @forelse($topProducts as $tp)
                <div class="flex items-center gap-4 group">
                    <div class="w-12 h-12 bg-slate-50 rounded-xl overflow-hidden flex-shrink-0 border border-slate-100 relative">
                        <img src="{{ asset('storage/' . ($tp->product->variants->first()->image_path ?? 'placeholder.jpg')) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-bold text-slate-800 truncate mb-0.5">{{ $tp->product->name ?? 'Produk Dihapus' }}</h4>
                        <p class="text-[11px] text-slate-500 font-medium">Rp {{ number_format($tp->product->price ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-sm font-black text-[#1a1a2e] bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                            {{ $tp->total_qty }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center flex flex-col items-center justify-center">
                    <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">Belum ada data penjualan produk.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Main Table Transaction Logs --}}
        <div class="lg:col-span-2 bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-6 md:p-8 border-b border-slate-50 flex justify-between items-center bg-white">
                <h3 class="font-black text-[#1a1a2e] text-sm">Riwayat Transaksi Lunas</h3>
                <span class="text-[10px] bg-slate-50 text-slate-500 px-3 py-1 rounded-lg font-bold border border-slate-100">Total: {{ $orders->total() }}</span>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="p-5 text-[10px] font-black text-slate-400 uppercase tracking-widest w-1/4">Order ID</th>
                            <th class="p-5 text-[10px] font-black text-slate-400 uppercase tracking-widest w-2/4">Detail Pelanggan</th>
                            <th class="p-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right w-1/4">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="p-5">
                                <span class="font-mono text-xs font-bold text-slate-500 group-hover:text-[#1a1a2e] transition-colors">
                                    #{{ $order->order_code }}
                                </span>
                                <div class="text-[9px] text-slate-400 mt-1 font-medium">{{ $order->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[#1a1a2e]/5 border border-[#1a1a2e]/10 flex items-center justify-center text-[10px] font-black text-[#1a1a2e] flex-shrink-0">
                                        {{ strtoupper(substr($order->user->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-xs text-slate-800 truncate">{{ $order->user->name ?? 'Pembeli Umum' }}</p>
                                        <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ $order->user->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-5 text-right">
                                <span class="font-black text-sm text-[#1a1a2e]">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-16 text-center text-slate-400 text-sm font-medium">
                                Tidak ada riwayat transaksi pada periode yang dipilih.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Custom Pagination Bawah --}}
            @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-slate-50 bg-slate-50/30 flex items-center justify-between mt-auto">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hidden sm:block">
                    Hal {{ $orders->currentPage() }} dari {{ $orders->lastPage() }}
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                    @if ($orders->onFirstPage())
                        <span class="px-4 py-2 bg-slate-100 text-slate-300 text-xs font-black rounded-xl uppercase tracking-widest cursor-not-allowed">Prev</span>
                    @else
                        <a href="{{ $orders->appends(request()->query())->previousPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:border-[#1a1a2e] hover:text-[#1a1a2e] text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-sm">Prev</a>
                    @endif

                    @if ($orders->hasMorePages())
                        <a href="{{ $orders->appends(request()->query())->nextPageUrl() }}" class="px-4 py-2 bg-[#1a1a2e] text-[#e8c9a0] text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-sm hover:opacity-90">Next</a>
                    @else
                        <span class="px-4 py-2 bg-slate-100 text-slate-300 text-xs font-black rounded-xl uppercase tracking-widest cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
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

        // Buat Gradient untuk Line Chart agar lebih elegan
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(232, 201, 160, 0.4)');
        gradient.addColorStop(1, 'rgba(232, 201, 160, 0.0)');

        new Chart(ctx, {
            type: isMonthlyMode ? 'line' : 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: dataValues,
                    borderColor: '#1a1a2e',
                    backgroundColor: isMonthlyMode ? gradient : '#1a1a2e',
                    borderWidth: isMonthlyMode ? 3 : 0,
                    fill: isMonthlyMode,
                    tension: 0.4, // Kurva yang lebih mulus (smooth)
                    pointRadius: isMonthlyMode ? 4 : 0,
                    pointBackgroundColor: '#e8c9a0',
                    pointBorderColor: '#1a1a2e',
                    pointBorderWidth: 2,
                    borderRadius: isMonthlyMode ? 0 : 6,
                    barPercentage: 0.6 // Lebar batang yang proporsional
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1a2e',
                        titleColor: '#e8c9a0',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: 'Plus Jakarta Sans', weight: '600' }, color: '#94a3b8' }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: '#f1f5f9', drawTicks: false },
                        ticks: { 
                            font: { size: 10, family: 'Plus Jakarta Sans', weight: '500' },
                            color: '#94a3b8',
                            padding: 12,
                            callback: function(value) {
                                if(value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                                if(value >= 1000) return 'Rp ' + (value / 1000) + ' Rb';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
@extends('layouts.admin')

@section('content')
{{-- Style khusus untuk transisi halus UI Alpine.js --}}
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="container mx-auto p-6 font-sans" x-data="{ showFilters: true }">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-4xl font-black text-[#1a1a2e] tracking-tighter">Analytics Center</h1>
            <p class="text-slate-400 text-sm font-medium">Monitoring performa bisnis Batik Ifawati secara real-time.</p>
        </div>
        
        <div class="flex items-center gap-3">
            {{-- Tombol Toggle Panel Filter Modern --}}
            <button @click="showFilters = !showFilters" class="bg-white border border-slate-200 text-slate-700 px-5 py-3 rounded-2xl font-bold hover:bg-slate-50 transition flex items-center gap-2 shadow-sm text-sm">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filter Dropdown
            </button>
            
            {{-- Tombol Export Excel Premium Interaktif --}}
            <a href="{{ route('admin.orders.exportExcel', request()->query()) }}" class="bg-[#1a1a2e] text-[#e8c9a0] px-6 py-3 rounded-2xl font-bold hover:opacity-90 transition flex items-center gap-2 shadow-xl shadow-slate-200 text-sm">
                <svg class="w-4 h-4 text-[#e8c9a0]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export Excel Premium
            </a>
        </div>
    </div>

    {{-- Filter Panel Berbasis Dropdown Multi-Periode Profesional --}}
    <div x-show="showFilters" x-collapse class="mb-8 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm" x-cloak>
        <form action="{{ route('admin.orders.report') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            {{-- Dropdown Pilihan Tahun (Dinamis dari DB) --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih Tahun Analisis</label>
                <select name="year" class="w-full bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-[#e8c9a0] text-sm font-bold py-3 text-slate-700">
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Pilihan Bulan (Agregasi Data) --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih Bulan (Agregasi Data)</label>
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

            {{-- Tombol Eksekusi Submit Filter --}}
            <div class="flex items-end">
                <button type="submit" class="w-full bg-[#e8c9a0] text-[#1a1a2e] py-3.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#d4b78d] transition shadow-sm">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        {{-- Card Pendapatan Finansial dengan Kalkulasi Tren Kontribusi Nyata --}}
        <div class="bg-white p-8 rounded-[2rem] border border-slate-50 shadow-sm relative overflow-hidden group flex flex-col justify-between">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                <svg class="w-20 h-20 text-[#1a1a2e]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3">Revenue Projection</p>
                <h3 class="text-3xl font-black text-[#1a1a2e]">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
            <div class="mt-4 flex flex-col gap-1">
                <div class="flex items-center gap-1.5">
                    @php
                        // Menghitung persentase kontribusi omset periode terpilih terhadap total semua omset di DB
                        $grandTotalAllTime = \App\Models\Order::where('payment_status', 'paid')->sum('total');
                        $percentageContribution = $grandTotalAllTime > 0 ? ($totalRevenue / $grandTotalAllTime) * 100 : 0;
                    @endphp
                    <span class="text-emerald-600 text-xs font-black bg-emerald-50 px-2 py-0.5 rounded-md">
                        ↑ {{ number_format($percentageContribution, 1) }}%
                    </span>
                    <span class="text-slate-400 text-[10px] font-semibold">Kontribusi Omset Terhadap Semua Transaksi</span>
                </div>
                <p class="text-[11px] text-slate-500 font-bold mt-2">Volume: <span class="text-[#1a1a2e]">{{ $totalTransactions }} Pesanan Terbayar</span></p>
            </div>
        </div>

        {{-- Grafik Utama Interaktif (Chart.js) dengan Pendekatan Smart Reflect --}}
        <div class="md:col-span-2 bg-white p-8 rounded-[2rem] border border-slate-50 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6 tracking-wider">{{ $chartTitle }}</p>
            <div class="h-[160px]">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-50 overflow-hidden">
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

        {{-- UI Pagination 10 Baris Clean Look --}}
        @if($orders->hasPages())
        <div class="px-8 py-5 border-t border-slate-50 bg-slate-50/30">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>

{{-- RUNTIME GRAPHIC ENGINE JAVASCRIPT (CHART.JS) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Parsing data dari Laravel Controller aman
        const labels = {!! json_encode($chartLabels ?? []) !!};
        const dataValues = {!! json_encode($chartData ?? []) !!};
        const isMonthlyMode = {!! json_encode($selectedMonth ? true : false) !!};

        new Chart(ctx, {
            // Auto Swap: Jika filter bulan aktif pakai LINE, jika rekap tahunan pakai BAR
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
                
                // PERBAIKAN UI/UX: Tambahkan padding kiri agar teks "Rp" tidak terpotong browser
                layout: {
                    padding: {
                        left: 15,  // Memberi ruang aman 15 pixel di sisi kiri sumbu Y
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
                            // Memastikan ada space/jarak antara teks Rupiah dengan garis sumbu Y
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
@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')

@section('content')

{{-- Style khusus untuk transisi halus UI Alpine.js --}}
<style>
    [x-cloak] { display: none !important; }
</style>

{{-- Stat Cards Lengkap Sesuai Struktur Database ENUM Status --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-stone-200 p-4 shadow-sm">
        <p class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">Total Pesanan</p>
        <p class="text-2xl font-black text-stone-900 italic">{{ $summary['total'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-stone-200 p-4 shadow-sm">
        <p class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">⏳ Pending</p>
        <p class="text-2xl font-black text-stone-500 italic">{{ $summary['pending'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-stone-200 p-4 shadow-sm">
        <p class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">📦 Diproses</p>
        <p class="text-2xl font-black text-amber-600 italic">{{ $summary['processing'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl border border-stone-200 p-4 shadow-sm">
        <p class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">🚚 Dikirim</p>
        <p class="text-2xl font-black text-purple-600 italic">{{ $summary['shipped'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-stone-200 p-4 shadow-sm">
        <p class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">✅ Selesai</p>
        <p class="text-2xl font-black text-green-700 italic">{{ $summary['delivered'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-stone-200 p-4 shadow-sm">
        <p class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-1">❌ Batal</p>
        <p class="text-2xl font-black text-red-600 italic">{{ $summary['cancelled'] ?? 0 }}</p>
    </div>
</div>

{{-- Filter & Search --}}
<div class="bg-white rounded-xl border border-stone-200 overflow-hidden shadow-sm">

    <div class="flex flex-wrap items-center gap-4 p-4 border-b border-stone-100 bg-stone-50/50">

        {{-- Filter Tab Status --}}
        <div class="flex flex-wrap gap-2">
            @php
                $statuses = ['', 'pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                $labels   = ['Semua', 'Pending', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
            @endphp

            @foreach($statuses as $i => $s)
                <a href="{{ route('admin.orders.index', array_merge(request()->except(['page', 'status']), $s ? ['status' => $s] : [])) }}"
                   class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all
                          {{ request('status', '') === $s
                             ? 'bg-stone-900 text-amber-200 shadow-sm'
                             : 'bg-white text-stone-500 border border-stone-200 hover:bg-stone-100' }}">
                    {{ $labels[$i] }}
                </a>
            @endforeach
        </div>

        {{-- Search Form --}}
        <form action="{{ route('admin.orders.index') }}" method="GET" class="ml-auto flex gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Kode order atau nama pelanggan..."
                   class="rounded-lg border border-stone-200 px-3 py-1.5 text-sm
                          outline-none focus:border-stone-400 w-64 bg-white text-stone-800 font-medium placeholder:text-stone-300" />
            <button type="submit"
                class="px-4 py-1.5 rounded-lg bg-stone-900 text-amber-200 text-xs font-black uppercase tracking-wider hover:bg-black transition-colors">
                Cari
            </button>
        </form>

        {{-- Reset Filter Button --}}
        <a href="{{ route('admin.orders.index') }}"
           class="flex items-center gap-1.5 px-4 py-1.5 rounded-lg border border-stone-200 bg-white
                  text-xs font-black text-stone-600 uppercase tracking-wider hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Reset
        </a>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 text-xs text-stone-400 font-bold uppercase tracking-wider border-b border-stone-100">
                <tr>
                    <th class="text-left px-5 py-3.5">Kode Order</th>
                    <th class="text-left px-5 py-3.5">Pelanggan</th>
                    <th class="text-left px-5 py-3.5">Total</th>
                    <th class="text-left px-5 py-3.5">Pembayaran</th>
                    <th class="text-left px-5 py-3.5">Status</th>
                    <th class="text-left px-5 py-3.5">Tanggal</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100 text-stone-700">

                @forelse($orders as $order)
                <tr class="hover:bg-stone-50/50 transition-colors cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                    <td class="px-5 py-4">
                        <span class="font-mono text-xs font-black text-stone-900">
                            {{ $order->order_code }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <p class="font-bold text-stone-900 leading-tight">{{ $order->user->name ?? 'Pembeli Umum' }}</p>
                        <p class="text-xs text-stone-400 mt-0.5">{{ $order->user->email ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 font-extrabold text-stone-900">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4" onclick="event.stopPropagation();">
                        <x-status-badge
                            :status="$order->payment_status"
                            :label="$order->payment_status === 'paid' ? 'Lunas' : ($order->payment_status === 'refunded' ? 'Refund' : 'Belum bayar')"
                        />
                    </td>
                    <td class="px-5 py-4" onclick="event.stopPropagation();">
                        <x-status-badge
                            :status="$order->status"
                            :label="$order->statusLabel()"
                        />
                    </td>
                    <td class="px-5 py-4 text-xs text-stone-400 font-medium leading-relaxed">
                        {{ $order->created_at->format('d M Y') }}<br>
                        {{ $order->created_at->format('H:i') }} WIB
                    </td>
                    <td class="px-5 py-4 text-right" onclick="event.stopPropagation();">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg border border-stone-200 bg-white
                                  text-stone-600 hover:bg-stone-900 hover:text-white hover:border-stone-900 transition-all shadow-sm">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <p class="text-stone-400 italic text-sm">Data transaksi pesanan tidak ditemukan.</p>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- UI Pagination Premium Kustomisasi Sempurna --}}
    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-stone-100 bg-stone-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-xs font-semibold text-stone-400 uppercase tracking-wider">
            Menampilkan <span class="text-[#1a1a2e] font-black">{{ $orders->firstItem() }}</span> - <span class="text-[#1a1a2e] font-black">{{ $orders->lastItem() }}</span> dari <span class="text-[#1a1a2e] font-black">{{ $orders->total() }}</span> Pesanan
        </div>
        <div class="flex items-center gap-1">
            {{-- Tombol Previous --}}
            @if ($orders->onFirstPage())
                <span class="px-3 py-2 bg-gray-100 text-gray-300 text-xs font-black rounded-xl uppercase tracking-widest cursor-not-allowed">Prev</span>
            @else
                <a href="{{ $orders->appends(request()->query())->previousPageUrl() }}" class="px-3 py-2 bg-white border border-gray-200 text-gray-600 hover:border-[#1a1a2e] hover:text-[#1a1a2e] text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-sm">Prev</a>
            @endif

            {{-- Nomor Halaman Dinamis --}}
            <div class="hidden sm:flex items-center gap-1">
                @foreach ($orders->getUrlRange(max(1, $orders->currentPage() - 2), min($orders->lastPage(), $orders->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url . '&' . http_build_query(request()->except('page')) }}" 
                       class="w-9 h-9 flex items-center justify-center rounded-xl text-xs font-bold transition-all border {{ $page == $orders->currentPage() ? 'bg-[#1a1a2e] text-[#e8c9a0] border-[#1a1a2e] font-black shadow-md shadow-[#1a1a2e]/10' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-400' }}">
                        {{ $page }}
                    </a>
                @endforeach
            </div>

            {{-- Tombol Next --}}
            @if ($orders->hasMorePages())
                <a href="{{ $orders->appends(request()->query())->nextPageUrl() }}" class="px-3 py-2 bg-white border border-gray-200 text-gray-600 hover:border-[#1a1a2e] hover:text-[#1a1a2e] text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-sm">Next</a>
            @else
                <span class="px-3 py-2 bg-gray-100 text-gray-300 text-xs font-black rounded-xl uppercase tracking-widest cursor-not-allowed">Next</span>
            @endif
        </div>
    </div>
    @endif

</div>

@endsection
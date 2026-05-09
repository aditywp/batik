@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-stone-200 p-4">
        <p class="text-xs text-stone-400 mb-1">Total Pesanan</p>
        <p class="text-2xl font-semibold text-stone-900">{{ $summary['total'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-stone-200 p-4">
        <p class="text-xs text-stone-400 mb-1">Menunggu</p>
        <p class="text-2xl font-semibold text-amber-600">{{ $summary['pending'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-stone-200 p-4">
        <p class="text-xs text-stone-400 mb-1">Dikirim</p>
        <p class="text-2xl font-semibold text-purple-600">{{ $summary['shipped'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-stone-200 p-4">
        <p class="text-xs text-stone-400 mb-1">Selesai</p>
        <p class="text-2xl font-semibold text-green-700">{{ $summary['delivered'] }}</p>
    </div>
</div>

{{-- Filter & Search --}}
<div class="bg-white rounded-xl border border-stone-200 overflow-hidden">

    <div class="flex flex-wrap items-center gap-3 p-4 border-b border-stone-100">

        {{-- Filter Tab Status --}}
        <div class="flex flex-wrap gap-2">
            @php
                $statuses = ['', 'pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                $labels   = ['Semua', 'Pending', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
            @endphp

            @foreach($statuses as $i => $s)
                <a href="{{ route('admin.orders.index', array_merge(request()->query(), ['status' => $s])) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-medium transition-colors
                          {{ request('status', '') === $s
                             ? 'bg-stone-900 text-amber-200'
                             : 'bg-stone-50 text-stone-500 hover:bg-stone-100' }}">
                    {{ $labels[$i] }}
                </a>
            @endforeach
        </div>

        {{-- Search --}}
        <form action="{{ route('admin.orders.index') }}" method="GET" class="ml-auto flex gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Kode order atau nama pelanggan..."
                   class="rounded-lg border border-stone-200 px-3 py-1.5 text-sm
                          outline-none focus:border-stone-400 w-64" />
            <button type="submit"
                class="px-3 py-1.5 rounded-lg bg-stone-900 text-amber-200 text-sm">
                Cari
            </button>
        </form>

        {{-- Export CSV --}}
        <a href="{{ route('admin.orders.export', request()->query()) }}"
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-stone-200
                  text-sm text-stone-600 hover:bg-stone-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export CSV
        </a>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 text-xs text-stone-400 font-medium">
                <tr>
                    <th class="text-left px-4 py-3">Kode Order</th>
                    <th class="text-left px-4 py-3">Pelanggan</th>
                    <th class="text-left px-4 py-3">Total</th>
                    <th class="text-left px-4 py-3">Pembayaran</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">

                @forelse($orders as $order)
                <tr class="hover:bg-stone-50 transition-colors">
                    <td class="px-4 py-3">
                        <span class="font-mono text-xs font-semibold text-stone-800">
                            {{ $order->order_code }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-stone-800">{{ $order->user->name }}</p>
                        <p class="text-xs text-stone-400">{{ $order->user->email }}</p>
                    </td>
                    <td class="px-4 py-3 font-medium">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3">
                        <x-status-badge
                            :status="$order->payment_status"
                            :label="$order->payment_status === 'paid' ? 'Lunas' : ($order->payment_status === 'refunded' ? 'Refund' : 'Belum bayar')"
                        />
                    </td>
                    <td class="px-4 py-3">
                        <x-status-badge
                            :status="$order->status"
                            :label="$order->statusLabel()"
                        />
                    </td>
                    <td class="px-4 py-3 text-xs text-stone-400">
                        {{ $order->created_at->format('d M Y') }}<br>
                        {{ $order->created_at->format('H:i') }} WIB
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="text-xs px-3 py-1.5 rounded-lg border border-stone-200
                                  text-stone-600 hover:bg-stone-50 transition-colors">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-16 text-center">
                        <p class="text-stone-400 text-sm">Belum ada pesanan.</p>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($orders->hasPages())
    <div class="px-4 py-3 border-t border-stone-100">
        {{ $orders->links() }}
    </div>
    @endif

</div>

@endsection
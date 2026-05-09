@extends('layouts.admin')

@section('title', 'Detail Pesanan ' . $order->order_code)

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-semibold text-stone-900 font-mono">
                    {{ $order->order_code }}
                </h2>
                <x-status-badge :status="$order->status" :label="$order->statusLabel()" />
                <x-status-badge
                    :status="$order->payment_status"
                    :label="$order->payment_status === 'paid' ? 'Lunas' : 'Belum bayar'"
                />
            </div>
            <p class="text-sm text-stone-400 mt-1">
                {{ $order->created_at->format('d F Y, H:i') }} WIB
            </p>
        </div>
        <a href="{{ route('admin.orders.index') }}"
           class="flex items-center gap-1.5 text-sm text-stone-500 hover:text-stone-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KIRI: Info Pesanan + Update Status --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Info Pelanggan --}}
            <div class="bg-white rounded-xl border border-stone-200 p-5">
                <h3 class="text-sm font-semibold text-stone-700 mb-4">Pelanggan</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-stone-100 flex items-center justify-center
                                text-stone-700 text-sm font-semibold flex-shrink-0">
                        {{ substr($order->user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium text-stone-800 text-sm">{{ $order->user->name }}</p>
                        <p class="text-xs text-stone-400">{{ $order->user->email }}</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-stone-400">Alamat</span>
                        <span class="text-stone-700 text-right max-w-[180px]">{{ $order->shipping_address }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-400">Kurir</span>
                        <span class="text-stone-700 font-medium uppercase">{{ $order->courier }} {{ $order->courier_service }}</span>
                    </div>
                    @if($order->paid_at)
                    <div class="flex justify-between">
                        <span class="text-stone-400">Dibayar</span>
                        <span class="text-stone-700">{{ $order->paid_at->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Update Status --}}
            @php $allowed = $order->allowedNextStatuses(); @endphp

            <div class="bg-white rounded-xl border border-stone-200 p-5">
                <h3 class="text-sm font-semibold text-stone-700 mb-1">Update Status</h3>

                @if(count($allowed) > 0)
                    <p class="text-xs text-stone-400 mb-4">
                        Status saat ini: <strong class="text-stone-700">{{ $order->statusLabel() }}</strong>
                    </p>

                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-2 mb-4">
                            @foreach($allowed as $nextStatus)
                                @php
                                    $info = \App\Models\Order::statusList()[$nextStatus];
                                @endphp
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-stone-200
                                              cursor-pointer hover:bg-stone-50 transition-colors
                                              has-[:checked]:border-stone-800 has-[:checked]:bg-stone-50">
                                    <input type="radio" name="status" value="{{ $nextStatus }}" class="accent-stone-900">
                                    <span class="text-sm text-stone-700">{{ $info['label'] }}</span>
                                </label>
                            @endforeach
                        </div>

                        <button type="submit"
                            class="w-full py-2.5 rounded-lg bg-stone-900 text-amber-200 text-sm
                                   font-medium hover:bg-stone-800 transition-colors">
                            Simpan Status
                        </button>
                    </form>
                @else
                    <div class="mt-3 p-3 rounded-lg bg-stone-50 text-sm text-stone-400 text-center">
                        Pesanan ini sudah final — tidak bisa diubah lagi.
                    </div>
                @endif
            </div>

        </div>

        {{-- KANAN: Item Pesanan + Ringkasan Harga --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-stone-100">
                    <h3 class="text-sm font-semibold text-stone-700">
                        Item Pesanan ({{ $order->items->count() }} produk)
                    </h3>
                </div>

                <div class="divide-y divide-stone-100">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-4 px-5 py-4">
                        {{-- Gambar produk --}}
                        <div class="w-14 h-14 rounded-lg bg-stone-100 border border-stone-200
                                    flex-shrink-0 overflow-hidden">
                            @if($item->product && $item->product->image)
                                <img src="{{ Storage::url($item->product->image) }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover" />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-stone-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-stone-800 text-sm truncate">
                                {{ $item->product->name ?? 'Produk dihapus' }}
                            </p>
                            <p class="text-xs text-stone-400 mt-0.5">
                                {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="text-sm font-semibold text-stone-800 flex-shrink-0">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Ringkasan Harga --}}
                <div class="border-t border-stone-100 px-5 py-4 space-y-2">
                    <div class="flex justify-between text-sm text-stone-500">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-stone-500">
                        <span>Ongkir ({{ strtoupper($order->courier ?? '') }} {{ $order->courier_service }})</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-base font-semibold text-stone-900
                                pt-3 mt-1 border-t border-stone-100">
                        <span>Total</span>
                        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Timeline Status --}}
            <div class="mt-4 bg-white rounded-xl border border-stone-200 p-5">
                <h3 class="text-sm font-semibold text-stone-700 mb-4">Alur Status</h3>
                @php
                    $timeline = ['pending', 'processing', 'shipped', 'delivered'];
                    $currentIdx = array_search($order->status, $timeline);
                    $isCancelled = $order->status === 'cancelled';
                @endphp

                @if($isCancelled)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-red-50 border border-red-100">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <p class="text-sm text-red-700 font-medium">Pesanan Dibatalkan</p>
                    </div>
                @else
                    <div class="flex items-center">
                        @foreach($timeline as $idx => $step)
                            @php
                                $isDone   = $currentIdx !== false && $idx <= $currentIdx;
                                $isCurrent = $idx === $currentIdx;
                                $info = \App\Models\Order::statusList()[$step];
                            @endphp

                            <div class="flex flex-col items-center flex-1">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold
                                            {{ $isDone ? 'bg-stone-900 text-amber-200' : 'bg-stone-100 text-stone-400' }}">
                                    {{ $idx + 1 }}
                                </div>
                                <p class="text-xs mt-1.5 font-medium
                                          {{ $isCurrent ? 'text-stone-800' : ($isDone ? 'text-stone-500' : 'text-stone-300') }}">
                                    {{ $info['label'] }}
                                </p>
                            </div>

                            @if($idx < count($timeline) - 1)
                                <div class="h-0.5 flex-1 mb-5
                                            {{ $currentIdx !== false && $idx < $currentIdx
                                               ? 'bg-stone-900' : 'bg-stone-100' }}">
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
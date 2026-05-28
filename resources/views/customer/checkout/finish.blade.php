@extends('layouts.customer')

@section('content')

<div class="min-h-screen bg-[#f8f9fa] flex items-center justify-center px-6 py-16 font-sans">

    <div class="max-w-2xl w-full bg-white rounded-[2.5rem] shadow-xl shadow-stone-100 p-8 md:p-12 border border-stone-100">

        {{-- LOGIKA DETEKSI STATUS PEMBAYARAN PREMIUM --}}
        @if($order->payment_status === 'paid')
            {{-- ISI 1: KONDISI PEMBAYARAN BERHASIL (LUNAS) --}}
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <div class="text-center mb-10">
                <span class="text-[10px] font-black uppercase bg-emerald-50 text-emerald-700 px-3 py-1 rounded-md tracking-widest border border-emerald-100">Transaction Completed</span>
                <h1 class="text-3xl font-black tracking-tight text-[#1a1a2e] mt-4 uppercase italic">Pembayaran Berhasil</h1>
                <p class="text-gray-400 text-sm mt-1">Terima kasih, pembayaran Anda telah kami terima. Pesanan akan segera diproses oleh tim Batik Ifawati.</p>
            </div>

        @elseif($order->payment_status === 'pending')
            {{-- ISI 2: KONDISI PEMBAYARAN TERTUNDA (MENUNGGU TRANSFER BANK/VA) --}}
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 animate-pulse">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-center mb-10">
                <span class="text-[10px] font-black uppercase bg-amber-50 text-amber-700 px-3 py-1 rounded-md tracking-widest border border-amber-100">Awaiting Payment</span>
                <h1 class="text-3xl font-black tracking-tight text-[#1a1a2e] mt-4 uppercase italic">Menunggu Pembayaran</h1>
                <p class="text-gray-400 text-sm mt-1">Selesaikan transfer Anda sesuai instruksi Midtrans sebelum batas waktu berakhir agar pesanan tidak batal otomatis.</p>
            </div>

        @else
            {{-- ISI 3: KONDISI PEMBAYARAN GAGAL / KEDALUWARSA / BATAL --}}
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-red-50 border border-red-100 flex items-center justify-center text-red-600">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
            <div class="text-center mb-10">
                <span class="text-[10px] font-black uppercase bg-red-50 text-red-700 px-3 py-1 rounded-md tracking-widest border border-red-100">Payment Failed</span>
                <h1 class="text-3xl font-black tracking-tight text-[#1a1a2e] mt-4 uppercase italic">Transaksi Gagal</h1>
                <p class="text-gray-400 text-sm mt-1">Sistem mendeteksi adanya kendala otentikasi pembayaran atau transaksi telah dibatalkan.</p>
            </div>
        @endif

        {{-- RINCIAN INVOICE NOTA --}}
        <div class="border border-stone-100 rounded-2xl p-6 bg-stone-50/40 space-y-4">
            <div class="flex justify-between items-center text-xs">
                <span class="text-gray-400 font-bold uppercase tracking-wider">Kode Transaksi</span>
                <span class="font-mono font-black text-[#1a1a2e]">#{{ $order->order_code }}</span>
            </div>

            <div class="flex justify-between items-center text-xs">
                <span class="text-gray-400 font-bold uppercase tracking-wider">Status Logistik</span>
                @if($order->status === 'cancelled')
                    <span class="bg-red-50 text-red-700 px-3 py-0.5 rounded-full font-extrabold uppercase tracking-wide border border-red-100">Dibatalkan</span>
                @elseif($order->status === 'delivered')
                    <span class="bg-emerald-50 text-emerald-700 px-3 py-0.5 rounded-full font-extrabold uppercase tracking-wide border border-emerald-100">Selesai</span>
                @elseif($order->status === 'processing' || $order->status === 'shipped')
                    <span class="bg-blue-50 text-blue-700 px-3 py-0.5 rounded-full font-extrabold uppercase tracking-wide border border-blue-100">{{ $order->statusLabel() }}</span>
                @else
                    <span class="bg-amber-50 text-amber-700 px-3 py-0.5 rounded-full font-extrabold uppercase tracking-wide border border-amber-100">Pending</span>
                @endif
            </div>

            <div class="flex justify-between items-center text-xs">
                <span class="text-gray-400 font-bold uppercase tracking-wider">Metode Pembayaran</span>
                <span class="font-extrabold text-[#1a1a2e]">{{ $order->payment_method ?? 'Midtrans Gateway' }}</span>
            </div>

            <div class="border-t border-dashed border-stone-200 pt-4 flex justify-between items-center">
                <span class="text-base font-black text-[#1a1a2e] uppercase tracking-wider">Total Pembayaran</span>
                <span class="text-2xl font-black text-[#1a1a2e] italic">
                    Rp {{ number_format($order->total, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- DAFTAR BARANG YANG DIBELI --}}
        <div class="mt-8">
            <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Daftar Belanjaan</h2>
            <div class="space-y-3 max-h-[220px] overflow-y-auto pr-1 scrollbar-hide">
                @foreach($order->items as $item)
                    <div class="flex justify-between items-center bg-white p-3 rounded-xl border border-stone-100">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-lg overflow-hidden border border-stone-200 bg-stone-50 flex-shrink-0">
                                <img src="{{ asset('storage/' . ($item->variant->image_path ?? 'placeholder.jpg')) }}" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-extrabold text-xs text-[#1a1a2e] truncate max-w-[220px]">{{ $item->product->name }}</h3>
                                <p class="text-[10px] text-gray-400 font-medium mt-0.5">
                                    Varian: {{ $item->variant->name ?? 'Default' }} · Qty: <span class="text-[#1a1a2e] font-bold">{{ $item->quantity }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="font-mono text-xs font-bold text-[#1a1a2e]">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- TOMBOL AKSI DINAMIS BERDASARKAN STATUS USER EXPERIENCE --}}
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-3">
            @if($order->payment_status === 'pending' && $order->payment_url)
                {{-- Jika pending, beri tombol untuk membuka ulang invoice Midtrans bayar --}}
                <a href="{{ $order->payment_url }}" target="_blank" class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 text-white px-8 py-3.5 rounded-xl font-black text-xs uppercase tracking-widest text-center shadow-lg shadow-amber-500/10 transition-all">
                    Bayar Sekarang ↗
                </a>
            @endif

            @if($order->payment_status === 'paid')
                {{-- Jika sukses lunas, tawarkan tombol untuk langsung memantau progres pesanan --}}
                <a href="{{ route('customer.orders.show', $order->order_code) }}" class="w-full sm:w-auto bg-[#1a1a2e] hover:bg-black text-[#e8c9a0] px-8 py-3.5 rounded-xl font-black text-xs uppercase tracking-widest text-center shadow-lg shadow-[#1a1a2e]/10 transition-all">
                    Pantau Pesanan Saya
                </a>
            @endif

            <a href="{{ route('customer.home') }}" class="w-full sm:w-auto bg-stone-100 hover:bg-stone-200 text-stone-600 px-8 py-3.5 rounded-xl font-black text-xs uppercase tracking-widest text-center transition-all">
                Kembali ke Beranda
            </a>
        </div>

    </div>
</div>

@endsection
@extends('layouts.customer')

@section('title', 'Shopping Cart — Batik Ifawati')

@section('content')
<div class="min-h-screen bg-[#f8f8f8] pt-32 pb-20 font-sans text-black">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <div class="flex items-end justify-between mb-12">
            <div>
                <h1 class="text-4xl font-black text-black italic tracking-tighter uppercase">Your Cart</h1>
                <p class="text-gray-400 text-[10px] uppercase tracking-[3px] mt-2 font-bold italic">Review your exquisite selection</p>
            </div>

            <a href="{{ route('catalog.index') }}"
                class="text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-black transition-all border-b border-transparent hover:border-black pb-1">
                Continue Shopping
            </a>
        </div>

        {{-- Menampilkan Pesan Success/Error dari Controller --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-widest rounded-2xl border border-emerald-100">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 text-red-700 text-xs font-bold uppercase tracking-widest rounded-2xl border border-red-100">
                {{ session('error') }}
            </div>
        @endif

        @if($cartItems->isEmpty())
            <div class="bg-white rounded-[40px] py-24 text-center border border-gray-100 shadow-sm">
                <p class="text-gray-400 text-xs uppercase tracking-widest mb-8 italic">Your cart is currently empty.</p>
                <a href="{{ route('catalog.index') }}"
                    class="inline-block bg-black text-white px-12 py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] hover:bg-orange-500 transition-all shadow-xl">
                    Explore Collection
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                {{-- DAFTAR ITEM KERANJANG --}}
                <div class="lg:col-span-8 space-y-6">
                    @foreach($cartItems as $item)
                        @php
                            $price = $item->variant->price ?? $item->product->price;
                            $subtotalItem = $price * $item->quantity;
                            // Memastikan path gambar aman ter-fallback
                            $imagePath = $item->variant->image_path ?? ($item->product->variants->first()->image_path ?? null);
                        @endphp

                        <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm flex gap-8 items-start transition-all hover:shadow-md relative group overflow-hidden">

                            {{-- LINK IMAGES: Klik Foto Untuk Menuju Produk --}}
                            <a href="{{ route('catalog.show', $item->product->slug) }}" 
                               class="w-28 h-36 bg-[#0a0a0a] flex-shrink-0 overflow-hidden rounded-2xl border border-gray-50 shadow-inner block relative group/img">
                                <img
                                    src="{{ $imagePath ? asset('storage/' . $imagePath) : asset('images/placeholder.jpg') }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover/img:scale-110"
                                    alt="{{ $item->product->name }}"
                                >
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-white text-[9px] font-black uppercase tracking-widest bg-black/60 px-2 py-1 rounded-md">View</span>
                                </div>
                            </a>

                            <div class="flex-grow flex flex-col justify-between self-stretch py-1">
                                <div class="flex justify-between items-start gap-4">
                                    <div>
                                        {{-- LINK TEXT: Klik Nama Produk Untuk Menuju Halaman Katalog --}}
                                        <h3 class="text-base font-black uppercase tracking-tight text-black mb-2 leading-tight max-w-md">
                                            <a href="{{ route('catalog.show', $item->product->slug) }}" class="hover:text-orange-500 hover:underline transition-colors block">
                                                {{ $item->product->name }}
                                            </a>
                                        </h3>
                                        
                                        <div class="flex gap-5 mb-4">
                                            <p class="text-[10px] text-orange-600 font-black uppercase tracking-widest italic">
                                                Motif: {{ $item->variant->motif ?? 'Standard' }}
                                            </p>
                                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic">
                                                Size: {{ $item->variant->size ?? '-' }}
                                            </p>
                                        </div>

                                        {{-- AKSI PENGATUR QUANTITY & REMOVE --}}
                                        <div class="flex items-center gap-6 mt-2">
                                            <div class="flex items-center bg-gray-50 rounded-xl p-1 border border-gray-100 inline-flex">
                                                <form action="{{ route('customer.cart.update', $item->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="action" value="decrease">
                                                    <button type="submit" 
                                                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-black hover:bg-white rounded-lg transition-all font-bold text-sm @if($item->quantity <= 1) opacity-20 cursor-not-allowed @endif"
                                                        @if($item->quantity <= 1) disabled @endif>-</button>
                                                </form>

                                                <form action="{{ route('customer.cart.update', $item->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="action" value="manual">
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" 
                                                        class="w-12 bg-transparent text-center text-xs font-black text-black focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                        onchange="this.form.submit()">
                                                </form>

                                                <form action="{{ route('customer.cart.update', $item->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="action" value="increase">
                                                    <button type="submit" class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-black hover:bg-white rounded-lg transition-all font-bold text-sm">+</button>
                                                </form>
                                            </div>

                                            <form action="{{ route('customer.cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item dari keranjang?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="flex items-center gap-1.5 text-red-600 font-black uppercase tracking-widest transition-opacity hover:opacity-70">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    <span class="text-[9px]">Remove</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="text-right flex-shrink-0">
                                        <p class="text-[10px] text-gray-300 font-black uppercase tracking-widest mb-1 italic">Price</p>
                                        <p class="text-sm font-black text-black italic">
                                            Rp {{ number_format($price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex justify-end items-end mt-auto">
                                    <p class="font-black text-2xl text-black italic tracking-tighter leading-none">
                                        Rp {{ number_format($subtotalItem, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- RINGKASAN ORDER / CHECKOUT CARD --}}
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-[40px] p-8 border border-gray-100 shadow-xl sticky top-32">
                        <h2 class="text-[10px] font-black uppercase tracking-[4px] text-orange-500 mb-10 italic">Order Summary</h2>
                        
                        <div class="flex justify-between items-end mb-10">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 italic">Total Payment</span>
                            <span class="text-3xl font-black text-black italic tracking-tighter">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <a href="{{ route('customer.checkout.index') }}" 
                           class="block w-full bg-black text-white py-5 rounded-2xl text-center text-[10px] font-black uppercase tracking-[0.3em] hover:bg-orange-600 transition-all shadow-xl active:scale-95">
                            Proceed to Checkout
                        </a>

                        <div class="mt-8 text-center">
                            <p class="text-[9px] text-gray-400 uppercase tracking-widest leading-relaxed">
                                Shipping and taxes calculated at checkout.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>
</div>

<style>
    /* Styling for Luxury Feel */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #000; }
</style>
@endsection
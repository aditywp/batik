@extends('layouts.customer') {{-- Pastikan buat layout khusus customer --}}

@section('content')
<div class="min-h-screen bg-black text-white pt-32 pb-20">
    <div class="max-w-7xl mx-auto px-10 grid grid-cols-1 lg:grid-cols-2 gap-20">
        {{-- Galeri Foto --}}
        <div x-data="{ activeImg: '{{ asset('storage/' . $product->variants->first()->image_path) }}' }">
            <img :src="activeImg" class="w-full aspect-[3/4] object-cover border border-white/10">
            <div class="flex gap-4 mt-4">
                @foreach($product->variants as $variant)
                    <img src="{{ asset('storage/' . $variant->image_path) }}" 
                         @click="activeImg = $el.src"
                         class="w-20 h-20 object-cover cursor-pointer border border-white/5 hover:border-white transition">
                @endforeach
            </div>
        </div>

        {{-- Info Produk --}}
        <div class="flex flex-col justify-center">
            <p class="text-amber-500 text-[10px] uppercase tracking-[0.3em] mb-4">{{ $product->category->name }}</p>
            <h1 class="text-4xl font-playfair mb-6">{{ $product->name }}</h1>
            <p class="text-2xl mb-8">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            
            <form action="{{ route('customer.cart.add', $product->id) }}" method="POST">
                @csrf
                <div class="mb-8">
                    <p class="text-[10px] uppercase tracking-widest text-gray-500 mb-4">Select Size</p>
                    <div class="flex gap-3">
                        @foreach($product->variants->unique('size') as $v)
                            <label class="cursor-pointer">
                                <input type="radio" name="variant_id" value="{{ $v->id }}" class="hidden peer" required>
                                <span class="w-12 h-12 flex items-center justify-center border border-white/20 peer-checked:border-white peer-checked:bg-white peer-checked:text-black transition uppercase text-xs">
                                    {{ $v->size }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                @auth
                    <button type="submit" class="w-full bg-white text-black py-5 uppercase text-[11px] font-bold tracking-[0.2em] hover:bg-amber-500 transition">
                        Add to Bag
                    </button>
                @else
                    <a href="{{ route('login') }}" class="block w-full bg-transparent border border-white text-white py-5 text-center uppercase text-[11px] font-bold tracking-[0.2em] hover:bg-white hover:text-black transition">
                        Login to Purchase
                    </a>
                @endauth
            </form>

            <div class="mt-12 pt-12 border-t border-white/10">
                <p class="text-[10px] uppercase tracking-widest text-gray-500 mb-4">Description</p>
                <p class="text-sm text-gray-400 leading-relaxed italic">{{ $product->description }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.customer')

@section('title', isset($query) ? 'Search: ' . $query . ' — Batik Ifawati' : 'Catalog — Batik Ifawati')

@section('content')
<div class="min-h-screen bg-black pt-32 pb-20">
    <div class="max-w-7xl mx-auto px-10">
        {{-- Header Katalog --}}
        <div class="mb-16 fade-in">
            <p class="text-[10px] uppercase tracking-[0.5em] text-amber-500 mb-4">
                {{ isset($query) ? 'Search Results For' : 'The Collections' }}
            </p>
            <h1 class="text-4xl lg:text-5xl font-playfair italic">
                {{ isset($query) ? '"' . $query . '"' : 'All Artifacts' }}
            </h1>
            <p class="text-gray-500 text-xs mt-4 uppercase tracking-widest">
                {{ $products->total() }} Products {{ isset($query) ? 'Found' : 'Available' }}
            </p>
        </div>

        {{-- Grid Produk --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-x-8 gap-y-16">
            @forelse($products as $product)
            <div class="group cursor-pointer fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s">
                <a href="{{ route('catalog.show', $product->slug) }}">
                    <div class="relative overflow-hidden aspect-[3/4] bg-[#0a0a0a] border border-white/5">
                        {{-- Logika pengambilan gambar: Varian -> Galeri -> Placeholder --}}
                        @php
                            $displayImage = 'placeholder.jpg';
                            if($product->variants->first() && $product->variants->first()->image_path) {
                                $displayImage = $product->variants->first()->image_path;
                            } elseif($product->images->first()) {
                                $displayImage = $product->images->first()->image_path;
                            }
                        @endphp
                        
                        <img src="{{ asset('storage/' . $displayImage) }}" 
                             class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition duration-1000 group-hover:scale-110">
                        
                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                            <span class="text-[9px] uppercase tracking-[0.3em] border border-white px-6 py-3 font-bold">View Details</span>
                        </div>
                    </div>
                </a>

                <div class="mt-6 space-y-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-[11px] uppercase tracking-[0.2em] font-bold text-white group-hover:text-amber-500 transition">
                                <a href="{{ route('catalog.show', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                            <p class="text-[9px] text-gray-400 uppercase tracking-widest mt-1">{{ $product->category->name }}</p>
                        </div>
                        <p class="text-[11px] font-medium text-white">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-32 text-center border border-dashed border-white/10">
                <p class="text-gray-500 uppercase tracking-[0.4em] text-[10px]">No artifacts found matching your request.</p>
                <a href="{{ route('catalog.index') }}" class="inline-block mt-8 text-[10px] uppercase tracking-widest border-b border-amber-500 pb-1 text-white">Back to Full Catalog</a>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-24 border-t border-white/5 pt-12">
            {{-- Tambahkan append query agar saat pindah halaman search tetap aktif --}}
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<style>
    /* Styling pagination agar match dengan tema Gelap */
    .pagination { display: flex; justify-content: center; gap: 15px; }
    .page-link { background: transparent; border: 1px solid #222; color: #555; padding: 10px 18px; font-size: 10px; text-transform: uppercase; tracking-widest; transition: 0.3s; }
    .page-link:hover { border-color: #fff; color: #fff; }
    .page-item.active .page-link { border-color: #fff; color: #fff; background: transparent; }
    .page-item.disabled .page-link { border-color: #111; color: #222; }
</style>
@endsection
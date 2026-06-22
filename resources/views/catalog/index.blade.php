@extends('layouts.customer')

@section('title', 'Katalog — Batik Ifawati')

@section('content')
<div class="bg-white min-h-screen pt-32 pb-20 text-black">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
            <div>
                <p class="text-[11px] text-gray-400 uppercase tracking-widest mb-2">Showing {{ $products->total() }} products</p>
                <h1 class="text-2xl font-playfair italic">All Artifacts</h1>
            </div>
            
            <form action="{{ route('catalog.index') }}" method="GET" class="w-full md:w-80">
                {{-- Hidden inputs agar filter lain tidak hilang saat search --}}
                @foreach(request()->except('q', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <div class="relative border-b border-black/10 focus-within:border-black transition">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="SEARCH PRODUCTS..." 
                           class="w-full bg-transparent py-3 text-[10px] tracking-[0.2em] outline-none border-none uppercase">
                    <button type="submit" class="absolute right-0 top-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="flex flex-col lg:flex-row gap-16">
            <aside class="w-full lg:w-64 flex-shrink-0">
                <div class="space-y-10">
                    
                    <div x-data="{ open: true }">
                        <button @click="open = !open" class="flex justify-between items-center w-full mb-6 uppercase tracking-[0.2em] text-[10px] font-black">
                            Category <span x-text="open ? '—' : '+'"></span>
                        </button>
                        <div x-show="open" class="space-y-3">
                            @foreach($categories as $cat)
                            <a href="{{ route('catalog.index', array_merge(request()->query(), ['category' => $cat->id])) }}" 
                               class="flex items-center gap-3 group cursor-pointer">
                                <div class="w-3 h-3 border border-black flex items-center justify-center">
                                    @if(request('category') == $cat->id) <div class="w-1.5 h-1.5 bg-black"></div> @endif
                                </div>
                                <span class="text-[10px] uppercase tracking-widest {{ request('category') == $cat->id ? 'text-black font-bold' : 'text-gray-400' }} group-hover:text-black transition">
                                    {{ $cat->name }}
                                </span>
                            </a>
                            @endforeach
                            @if(request('category'))
                                <a href="{{ route('catalog.index', request()->except('category')) }}" class="block text-[9px] text-amber-600 underline tracking-widest mt-4 uppercase font-bold">Clear Category</a>
                            @endif
                        </div>
                    </div>

                    <div x-data="{ 
                        open: true, 
                        min: {{ request('min_price', 0) }}, 
                        max: {{ request('max_price', 5000000) }} 
                    }">
                        <button @click="open = !open" class="flex justify-between items-center w-full mb-6 uppercase tracking-[0.2em] text-[10px] font-black">
                            Price Range <span x-text="open ? '—' : '+'"></span>
                        </button>
                        <div x-show="open" class="space-y-6">
                            <div class="space-y-2">
                                <input type="range" min="0" max="5000000" step="100000" x-model="max" 
                                       class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-black">
                                <div class="flex justify-between text-[10px] tracking-widest text-gray-500">
                                    <span>Rp 0</span>
                                    <span class="text-black font-bold">Max: Rp <span x-text="parseInt(max).toLocaleString('id-ID')"></span></span>
                                </div>
                            </div>
                            <form action="{{ route('catalog.index') }}" method="GET">
                                @foreach(request()->except('min_price', 'max_price', 'page') as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <input type="hidden" name="min_price" :value="min">
                                <input type="hidden" name="max_price" :value="max">
                                <button type="submit" class="w-full py-3 bg-black text-white text-[9px] font-black tracking-[0.3em] uppercase hover:bg-gray-800 transition">Apply Price</button>
                            </form>
                            @if(request('max_price'))
                                <a href="{{ route('catalog.index', request()->except('min_price', 'max_price')) }}" class="block text-[9px] text-amber-600 underline tracking-widest uppercase font-bold text-center">Clear Price</a>
                            @endif
                        </div>
                    </div>

                    <div x-data="{ open: true }">
                        <button @click="open = !open" class="flex justify-between items-center w-full mb-6 uppercase tracking-[0.2em] text-[10px] font-black">
                            Collections <span x-text="open ? '—' : '+'"></span>
                        </button>
                        <div x-show="open" class="space-y-3">
                            @foreach(['women', 'men', 'craft', 'family'] as $col)
                            <a href="{{ route('catalog.index', array_merge(request()->query(), ['collection' => $col])) }}" 
                               class="flex items-center gap-3 group cursor-pointer">
                                <div class="w-3 h-3 border border-black flex items-center justify-center">
                                    @if(request('collection') == $col) <div class="w-1.5 h-1.5 bg-black"></div> @endif
                                </div>
                                <span class="text-[10px] uppercase tracking-widest {{ request('collection') == $col ? 'text-black font-bold' : 'text-gray-400' }} group-hover:text-black transition">
                                    {{ $col }}
                                </span>
                            </a>
                            @endforeach
                            @if(request('collection'))
                                <a href="{{ route('catalog.index', request()->except('collection')) }}" class="block text-[9px] text-amber-600 underline tracking-widest mt-4 uppercase font-bold">Clear Collections</a>
                            @endif
                        </div>
                    </div>

                </div>
            </aside>

            <main class="flex-grow">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-16">
                    @forelse($products as $product)
                        <div class="group">
                            <a href="{{ route('catalog.show', $product->slug) }}" class="block aspect-[3/4] overflow-hidden bg-[#f9f9f9] mb-6">
                                <img src="{{ asset('storage/' . ($product->variants->first()->image_path ?? 'placeholder.jpg')) }}" 
                                     class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                            </a>
                            <div class="space-y-1">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                <h3 class="text-[11px] font-black uppercase tracking-widest">
                                    <a href="{{ route('catalog.show', $product->slug) }}">{{ $product->name }}</a>
                                </h3>
                                <p class="text-[11px] tracking-widest font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-40 text-center border-y border-gray-100">
                            <p class="text-[10px] uppercase tracking-[0.3em] text-gray-400">No products found.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-20 pt-6">
                    @if ($products->hasPages())
                        <div class="flex flex-col md:flex-row items-center justify-between gap-6 py-4 px-6 bg-white border border-gray-100 rounded-xl shadow-sm">
                            
                            <p class="text-[12px] font-medium text-gray-400 tracking-wider uppercase">
                                Menampilkan <span class="text-gray-900 font-bold">{{ $products->firstItem() }}</span> - 
                                <span class="text-gray-900 font-bold">{{ $products->lastItem() }}</span> 
                                dari <span class="text-gray-900 font-bold">{{ $products->total() }}</span> produk
                            </p>

                            <div class="flex items-center gap-2">
                                {{-- Tombol Previous --}}
                                @if ($products->onFirstPage())
                                    <span class="px-4 py-2 text-[12px] font-bold text-gray-300 bg-gray-100 rounded-lg cursor-not-allowed uppercase tracking-wider">
                                        Prev
                                    </span>
                                @else
                                    <a href="{{ $products->previousPageUrl() }}" class="px-4 py-2 text-[12px] font-bold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition duration-200 uppercase tracking-wider">
                                        Prev
                                    </a>
                                @endif

                                {{-- Logika untuk menampilkan maksimal 3 angka halaman saja --}}
                                @php
                                    $start = max(1, $products->currentPage() - 1);
                                    $end = min($products->lastPage(), $start + 2);
                                    
                                    // Penyesuaian jika user berada di halaman-halaman terakhir agar tetap tampil 3 tombol (jika halamannya cukup)
                                    if ($end - $start < 2) {
                                        $start = max(1, $end - 2);
                                    }
                                @endphp

                                @for ($i = $start; $i <= $end; $i++)
                                    @if ($i == $products->currentPage())
                                        {{-- Halaman Aktif (Gelap) --}}
                                        <span class="px-4 py-2 text-[13px] font-bold text-white bg-[#1a1a2e] rounded-lg shadow-md">
                                            {{ $i }}
                                        </span>
                                    @else
                                        {{-- Halaman Tidak Aktif (Outline) --}}
                                        <a href="{{ $products->url($i) }}" class="px-4 py-2 text-[13px] font-bold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition duration-200">
                                            {{ $i }}
                                        </a>
                                    @endif
                                @endfor

                                {{-- Tombol Next --}}
                                @if ($products->hasMorePages())
                                    <a href="{{ $products->nextPageUrl() }}" class="px-4 py-2 text-[12px] font-bold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition duration-200 uppercase tracking-wider">
                                        Next
                                    </a>
                                @else
                                    <span class="px-4 py-2 text-[12px] font-bold text-gray-300 bg-gray-100 rounded-lg cursor-not-allowed uppercase tracking-wider">
                                        Next
                                    </span>
                                @endif
                            </div>

                        </div>
                    @endif
                </div>

            </main>
        </div>
    </div>
</div>
@endsection
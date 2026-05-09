@extends('layouts.customer')

@section('content')
    <section class="min-h-screen flex flex-col lg:flex-row items-center pt-20">
        <div class="w-full lg:w-1/2 relative flex justify-center items-center p-10 fade-in">
            <div class="absolute z-0 opacity-20">
                <img src="https://www.transparentpng.com/download/flower/pink-flower-png-transparent-12.png" class="w-[500px] rotate-12 blur-sm" alt="decor">
            </div>
            
            <div class="relative z-10">
                @if(isset($products) && $products->count() > 0)
                    @php $heroProduct = $products->first(); @endphp
                    <img src="{{ asset('storage/' . ($heroProduct->variants->first()->image_path ?? 'placeholder.jpg')) }}" 
                         class="w-[320px] h-[450px] object-cover rounded-sm shadow-2xl border border-white/10" 
                         style="mask-image: linear-gradient(to bottom, black 85%, transparent 100%);">
                    
                    <div class="absolute bottom-10 -right-6 lg:-right-10 bg-black/60 backdrop-blur-lg border border-white/10 p-5 text-left">
                        <p class="text-[9px] uppercase tracking-[0.3em] text-amber-500 mb-1">Highlight</p>
                        <h4 class="text-sm font-semibold tracking-wider text-white">{{ $heroProduct->name }}</h4>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase">{{ $heroProduct->category->name }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center px-10 lg:px-20 text-center lg:text-left fade-in" style="animation-delay: 0.3s">
            <div class="mb-8 flex justify-center lg:justify-start">
                <div class="w-24 h-24 rounded-full border border-white/5 flex items-center justify-center relative">
                    <div class="absolute inset-0 sun-burst opacity-20 animate-pulse"></div>
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                </div>
            </div>
            
            <h1 class="text-5xl lg:text-6xl font-playfair italic mb-8 leading-tight text-white">
                Start to be <br> 
                <span class="not-italic">Remembered</span>
            </h1>
            
            <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                <a href="{{ route('catalog.index') }}" class="text-[10px] uppercase tracking-[0.2em] bg-white text-black px-10 py-5 hover:bg-amber-500 transition duration-500 font-bold">
                    Shop Collections
                </a>
                <a href="#featured" class="text-[10px] uppercase tracking-[0.2em] border border-white/30 px-10 py-5 hover:border-white transition duration-500">
                    Featured Art
                </a>
            </div>
        </div>
    </section>

    <section id="featured" class="py-32 bg-hmns-dark">
        <div class="max-w-7xl mx-auto px-10">
            <div class="flex justify-between items-end mb-16">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.4em] text-amber-500 mb-2">Curated Selection</p>
                    <h2 class="text-3xl font-playfair text-white text-white">Featured Collections</h2>
                </div>
                <a href="{{ route('catalog.index') }}" class="text-[10px] uppercase tracking-[0.2em] border-b border-white/20 pb-1 hover:border-white transition">View All</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                @if(isset($products))
                    @foreach($products as $product)
                    <div class="group cursor-pointer">
                        <div class="relative overflow-hidden aspect-[3/4] bg-black">
                            <img src="{{ asset('storage/' . ($product->variants->first()->image_path ?? 'placeholder.jpg')) }}" 
                                 class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition duration-1000 group-hover:scale-105">
                            
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                <div class="flex flex-col gap-3 w-full px-10">
                                    <a href="{{ route('catalog.show', $product->slug) }}" class="text-center text-[10px] uppercase tracking-widest border border-white py-3 hover:bg-white hover:text-black transition font-bold text-white">
                                        Details
                                    </a>
                                    @auth
                                        <form action="{{ route('customer.cart.add', $product->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="variant_id" value="{{ $product->variants->first()->id }}">
                                            <button type="submit" class="w-full text-[10px] uppercase tracking-widest bg-white text-black py-3 hover:bg-amber-500 transition font-bold">
                                                Quick Add
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="text-center text-[10px] uppercase tracking-widest bg-white text-black py-3 hover:bg-amber-500 transition font-bold">
                                            Login to Bag
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-[11px] uppercase tracking-[0.2em] font-bold mb-1 text-white">{{ $product->name }}</h3>
                                    <p class="text-[9px] text-gray-500 uppercase tracking-widest">{{ $product->category->name }}</p>
                                </div>
                                <p class="text-[11px] font-medium text-white">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <section class="py-32 bg-black border-t border-white/5">
        <div class="max-w-7xl mx-auto px-10 flex flex-col lg:flex-row items-center gap-20">
            <div class="lg:w-1/2">
                <h2 class="text-3xl font-playfair italic mb-8 leading-snug text-white">
                    "Batik is an art fused with soul. <br>
                    In Ifawati, we believe that each pattern 
                    is there to make you adored, loved and remembered."
                </h2>
                <a href="{{ route('philosophy') }}" class="inline-block text-[10px] uppercase tracking-[0.3em] border-b border-white/40 pb-2 hover:border-white transition">
                    See our stories
                </a>
            </div>
            <div class="lg:w-1/2 grid grid-cols-2 gap-6 text-white">
                <div class="overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1615484477778-ca3b77940c25?auto=format&fit=crop&q=80" class="w-full h-80 object-cover grayscale hover:grayscale-0 transition duration-700 hover:scale-110">
                </div>
                <div class="overflow-hidden mt-12">
                    <img src="https://images.unsplash.com/photo-1544441893-675973e31985?auto=format&fit=crop&q=80" class="w-full h-80 object-cover grayscale hover:grayscale-0 transition duration-700 hover:scale-110">
                </div>
            </div>
        </div>
    </section>
@endsection
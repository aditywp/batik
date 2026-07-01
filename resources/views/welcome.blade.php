@extends('layouts.customer')

@section('content')
<div class="overflow-x-hidden w-full">
    {{-- HERO SECTION --}}
    <section class="min-h-screen flex flex-col lg:flex-row items-center pt-20">
        <div class="w-full lg:w-1/2 relative flex justify-center items-center p-4 md:p-10 fade-in">
            <div class="absolute z-0 opacity-20">
                <img src="https://www.transparentpng.com/download/flower/pink-flower-png-transparent-12.png" class="w-[300px] md:w-[500px] rotate-12 blur-sm" alt="decor">
            </div>
            
            <div class="relative z-10">
                @if(isset($products) && $products->count() > 0)
                    @php $heroProduct = $products->first(); @endphp
                    <a href="{{ route('catalog.show', $heroProduct->slug) }}" class="block relative group cursor-pointer">
                        <img src="{{ asset('storage/' . ($heroProduct->variants->first()->image_path ?? 'placeholder.jpg')) }}" 
                             class="w-[280px] md:w-[320px] h-[400px] md:h-[450px] object-cover rounded-sm shadow-2xl border border-white/10 group-hover:scale-[1.02] transition duration-500" 
                             style="mask-image: linear-gradient(to bottom, black 85%, transparent 100%);">
                        
                        <div class="absolute bottom-10 -right-4 lg:-right-10 bg-black/60 backdrop-blur-lg border border-white/10 p-5 text-left group-hover:bg-black/80 transition duration-500">
                            <p class="text-[9px] uppercase tracking-[0.3em] text-amber-500 mb-1">Highlight</p>
                            <h4 class="text-sm font-semibold tracking-wider text-white">{{ $heroProduct->name }}</h4>
                            <p class="text-[10px] text-gray-400 mt-1 uppercase">{{ $heroProduct->category->name }}</p>
                        </div>
                    </a>
                @endif
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 md:px-10 lg:px-20 text-center lg:text-left fade-in" style="animation-delay: 0.3s">      
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-playfair italic mb-8 leading-tight text-white">
                Start to be <br> 
                <span class="not-italic">Remembered</span>
            </h1>
            
            <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                <a href="{{ route('catalog.index') }}" class="text-[10px] uppercase tracking-[0.2em] bg-white text-black px-8 py-4 md:px-10 md:py-5 hover:bg-amber-500 transition duration-500 font-bold">
                    Shop Collections
                </a>
                <a href="#featured" class="text-[10px] uppercase tracking-[0.2em] border border-white/30 px-8 py-4 md:px-10 md:py-5 hover:border-white transition duration-500">
                    Featured Art
                </a>
            </div>
        </div>
    </section>

    {{-- FEATURED COLLECTIONS SECTION --}}
    <section id="featured" class="py-20 md:py-32 bg-black">
        <div class="max-w-7xl mx-auto px-6 md:px-10">
            <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-12 md:mb-16 gap-6">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.4em] text-amber-500 mb-2">Curated Selection</p>
                    <h2 class="text-2xl md:text-3xl font-playfair text-white">Featured Collections</h2>
                </div>
                <a href="{{ route('catalog.index') }}" class="text-[10px] uppercase tracking-[0.2em] border-b border-white/20 pb-1 hover:border-white transition w-max">View All</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 md:gap-12">
                @if(isset($products) && $products->count() > 0)
                    @foreach($products->take(3) as $product)
                    <div class="group cursor-pointer">
                        <div class="relative overflow-hidden aspect-[3/4] bg-black">
                            <img src="{{ asset('storage/' . ($product->variants->first()->image_path ?? 'placeholder.jpg')) }}" 
                                 class="w-full h-full object-cover transition duration-1000 group-hover:scale-105 border border-white/5">
                            
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                <div class="flex flex-col gap-3 w-full px-6 md:px-10">
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

    {{-- PHILOSOPHY SECTION --}}
    <section class="py-20 md:py-32 bg-black border-t border-white/5">
        <div class="max-w-4xl mx-auto px-6 md:px-10 flex flex-col items-center justify-center text-center">
            <div class="w-full">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-playfair italic mb-8 leading-relaxed text-white">
                    "Batik is an art fused with soul. <br class="hidden md:block">
                    In Ifawati, we believe that each pattern 
                    is there to make you adored, loved and remembered."
                </h2>
                <a href="{{ route('philosophy') }}" class="inline-block text-[10px] uppercase tracking-[0.3em] border-b border-white/40 pb-2 hover:border-white transition">
                    See our stories
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
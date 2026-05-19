@extends('layouts.customer')

@section('title', $product->name . ' — Batik Ifawati')

@section('content')
<div class="bg-white min-h-screen pt-32 pb-20 text-black" 
    x-data="{ 
        selectedMotif: '{{ $product->variants->first()->motif ?? '' }}',
        selectedVariantId: {{ $product->variants->first()->id ?? 'null' }},
        selectedPrice: {{ $product->variants->first()->price ?? $product->price }},
        mainImage: '{{ asset('storage/' . ($product->variants->first()->image_path ?? 'placeholder.jpg')) }}',
        variants: {{ $product->variants->toJson() }},
        activeMenu: 'desc',
        isLoadingStock: true,

        async syncStock() {
            try {
                const response = await fetch('/admin/products/{{ $product->id }}/json');
                const freshData = await response.json();
                this.variants = freshData.variants;
                
                let current = this.variants.find(v => v.id === this.selectedVariantId);
                if(current && current.stock <= 0) {
                    this.selectedVariantId = null;
                }
            } catch (e) {
                console.error('Gagal sinkronisasi stok terbaru.');
            } finally {
                this.isLoadingStock = false;
            }
        }
    }" x-init="syncStock()">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        
        <nav class="flex mb-12 text-[10px] uppercase tracking-[0.2em] text-gray-400">
            <a href="{{ route('catalog.index') }}" class="hover:text-black transition">Catalog</a>
            <span class="mx-3">/</span>
            <span class="text-black font-bold">{{ $product->category->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
            
            {{-- Image Gallery --}}
            <div class="lg:col-span-7">
                <div class="aspect-square bg-[#f9f9f9] rounded-3xl overflow-hidden mb-6 shadow-sm border border-slate-100">
                    <img :src="mainImage" class="w-full h-full object-cover transition duration-700" alt="{{ $product->name }}">
                </div>
                
                <div class="grid grid-cols-4 gap-4">
                    @php
                        $allGallery = collect();
                        foreach($product->variants as $v) {
                            if($v->image_path) $allGallery->push(['path' => $v->image_path, 'motif' => $v->motif]);
                        }
                        foreach($product->images as $img) {
                            if($img->image_path) $allGallery->push(['path' => $img->image_path, 'motif' => null]);
                        }
                        $uniqueGallery = $allGallery->unique('path');
                    @endphp

                    @foreach($uniqueGallery as $thumb)
                        <button @click="mainImage = '{{ asset('storage/' . $thumb['path']) }}'; @if($thumb['motif']) selectedMotif = '{{ $thumb['motif'] }}' @endif" 
                                class="aspect-square rounded-2xl bg-[#f9f9f9] border-2 transition-all overflow-hidden"
                                :class="mainImage === '{{ asset('storage/' . $thumb['path']) }}' ? 'border-slate-900 shadow-md' : 'border-transparent'">
                            <img src="{{ asset('storage/' . $thumb['path']) }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Product Info --}}
            <div class="lg:col-span-5 space-y-8">
                <section>
                    <span class="inline-block bg-amber-50 text-amber-600 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest mb-4">
                        {{ $product->category->name }}
                    </span>
                    <h1 class="text-4xl font-bold leading-tight mb-6 text-slate-900">{{ $product->name }}</h1>
                    
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <p class="text-gray-400 text-[10px] uppercase tracking-widest mb-1">Price</p>
                        <p class="text-3xl font-black text-slate-900 italic">
                            Rp <span x-text="parseInt(selectedPrice).toLocaleString('id-ID')"></span>
                        </p>
                    </div>
                </section>

                {{-- Motif Selection --}}
                <section>
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Select Motif</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($product->variants->unique('motif') as $v)
                        <button @click="selectedMotif = '{{ $v->motif }}'; mainImage = '{{ asset('storage/' . $v->image_path) }}'; selectedVariantId = null;"
                                :class="selectedMotif === '{{ $v->motif }}' ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-600 border-slate-200'"
                                class="px-6 py-3 border rounded-xl text-[11px] font-bold transition-all hover:border-slate-900">
                            {{ $v->motif }}
                        </button>
                        @endforeach
                    </div>
                </section>

                {{-- Size Selection --}}
                <section>
                    <div class="flex justify-between items-end mb-4">
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">Select Size</h3>
                        <div x-show="isLoadingStock" class="animate-pulse text-[9px] text-emerald-600 font-bold uppercase tracking-tighter">Updating Stock...</div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3">
                        <template x-for="v in variants.filter(v => v.motif === selectedMotif)" :key="v.id">
                            <button @click="selectedVariantId = v.id; selectedPrice = v.price || {{ $product->price }}"
                                    :class="selectedVariantId === v.id ? 'border-slate-900 bg-slate-50' : 'border-slate-100 bg-white'"
                                    :disabled="v.stock <= 0"
                                    class="flex flex-col items-center justify-center py-4 border-2 rounded-2xl transition-all disabled:opacity-30 disabled:grayscale relative overflow-hidden group">
                                
                                <span class="text-sm font-black" x-text="v.size"></span>
                                <span class="text-[9px] font-bold mt-1" 
                                      :class="v.stock > 0 ? 'text-emerald-600' : 'text-red-500'" 
                                      x-text="v.stock > 0 ? v.stock + ' in stock' : 'Sold Out'"></span>

                                <template x-if="v.stock <= 0">
                                    <div class="absolute inset-0 bg-white/60 flex items-center justify-center pointer-events-none">
                                        <div class="w-full h-[1px] bg-red-400 rotate-45"></div>
                                    </div>
                                </template>
                            </button>
                        </template>
                    </div>
                </section>

                <form action="{{ route('customer.cart.add', $product->id) }}" method="POST" class="pt-4">
                    @csrf
                    <input type="hidden" name="variant_id" :value="selectedVariantId">
                    <button type="submit" 
                            :disabled="!selectedVariantId"
                            class="w-full bg-slate-900 text-white py-6 rounded-2xl text-[11px] font-black uppercase tracking-[0.3em] hover:bg-black transition-all shadow-2xl disabled:bg-slate-200 disabled:text-slate-400">
                        <span x-text="selectedVariantId ? 'ADD TO BAG' : 'SELECT SIZE & MOTIF'"></span>
                    </button>
                </form>

                {{-- Accordion Menus --}}
                <div class="space-y-4 pt-8 border-t border-slate-100">
                    {{-- Description --}}
                    <div>
                        <button @click="activeMenu = (activeMenu === 'desc' ? '' : 'desc')" class="flex justify-between items-center w-full py-2">
                            <h3 class="text-[11px] font-bold text-slate-900 uppercase tracking-[0.2em]">Description</h3>
                            <svg class="w-3 h-3 transition-transform" :class="activeMenu === 'desc' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeMenu === 'desc'" x-collapse class="mt-4 text-xs leading-relaxed text-slate-500 tracking-wider text-justify">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>

                    {{-- Reviews Section --}}
                    <div class="pt-4 border-t border-slate-100">
                        <button @click="activeMenu = (activeMenu === 'reviews' ? '' : 'reviews')" class="flex justify-between items-center w-full py-2">
                            <div class="flex items-center gap-3">
                                <h3 class="text-[11px] font-bold text-slate-900 uppercase tracking-[0.2em]">Customer Reviews</h3>
                                <span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded-full font-bold text-slate-500">{{ $product->reviews->count() }}</span>
                            </div>
                            <svg class="w-3 h-3 transition-transform" :class="activeMenu === 'reviews' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="activeMenu === 'reviews'" x-collapse class="mt-6">
                            @if($product->reviews->count() > 0)
                                {{-- Rating Summary --}}
                                <div class="flex items-center gap-6 mb-10 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                                    <div class="text-center">
                                        <h4 class="text-4xl font-black text-slate-900">{{ number_format($product->reviews()->avg('rating'), 1) }}</h4>
                                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1">Average Rating</p>
                                    </div>
                                    <div class="flex-1 border-l border-slate-200 pl-6 space-y-1.5">
                                        @foreach(range(5, 1) as $star)
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center gap-1 w-6">
                                                    <span class="text-[10px] font-bold text-slate-600">{{ $star }}</span>
                                                    <svg class="w-2 h-2 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                </div>
                                                <div class="flex-1 h-1 bg-slate-200 rounded-full overflow-hidden">
                                                    @php 
                                                        $count = $product->reviews()->where('rating', $star)->count();
                                                        $percent = ($count / $product->reviews->count()) * 100;
                                                    @endphp
                                                    <div class="h-full bg-slate-900 rounded-full" style="width: {{ $percent }}%"></div>
                                                </div>
                                                <span class="text-[9px] font-bold text-slate-400 w-4">{{ $count }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Scrollable Reviews List --}}
                                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach($product->reviews()->where('is_approved', true)->latest()->get() as $review)
                                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm transition-hover hover:border-slate-300">
                                            <div class="flex justify-between items-start mb-3">
                                                <div>
                                                    <p class="text-[11px] font-black text-slate-900 uppercase tracking-tighter">{{ $review->user->name }}</p>
                                                    <div class="flex text-amber-400 mt-1">
                                                        @for($i=1; $i<=5; $i++)
                                                            <svg class="w-2.5 h-2.5 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $review->created_at->format('d M Y') }}</span>
                                            </div>
                                            <p class="text-xs leading-relaxed text-slate-600 italic">"{{ $review->comment }}"</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10 bg-slate-50 rounded-3xl border border-dashed border-slate-200">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">No reviews yet for this product</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        <div class="mt-32 pt-20 border-t border-slate-100">
            <h2 class="text-2xl font-bold mb-12 text-slate-900 uppercase tracking-tighter">You May Also Like</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                @foreach($relatedProducts->take(5) as $related)
                <div class="group">
                    <a href="{{ route('catalog.show', $related->slug) }}" class="block aspect-[3/4] bg-[#f9f9f9] rounded-2xl overflow-hidden mb-4 border border-slate-50">
                        <img src="{{ asset('storage/' . ($related->variants->first()->image_path ?? 'placeholder.jpg')) }}" 
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    </a>
                    <div class="px-1">
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $related->category->name }}</p>
                        <h3 class="text-[11px] font-bold text-slate-800 truncate mb-1 uppercase">
                            <a href="{{ route('catalog.show', $related->slug) }}">{{ $related->name }}</a>
                        </h3>
                        <p class="text-[11px] font-black text-slate-900 italic">Rp {{ number_format($related->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar styling untuk area review agar tetap estetik */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #000;
        border-radius: 10px;
    }
</style>
@endsection
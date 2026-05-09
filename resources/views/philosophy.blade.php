@extends('layouts.customer')

@section('title', 'Philosophy — Batik Ifawati')

@section('content')
<div class="min-h-screen bg-black text-white pt-32 pb-20 overflow-hidden">
    {{-- Header Section --}}
    <div class="max-w-7xl mx-auto px-10 mb-24">
        <div class="max-w-3xl fade-in">
            <p class="text-[10px] uppercase tracking-[0.5em] text-amber-500 mb-6">Our Essence</p>
            <h1 class="text-5xl lg:text-7xl font-playfair italic leading-tight mb-10">
                Beyond Patterns, <br>
                <span class="not-italic">A Legacy of Soul.</span>
            </h1>
            <p class="text-gray-400 text-xs uppercase tracking-[0.2em] leading-loose">
                Di Batik Ifawati, kami percaya bahwa sehelai kain bukan sekadar pakaian. Ia adalah kanvas kehidupan, di mana setiap goresan malam (lilin) dan celupan warna membawa doa, harapan, dan identitas budaya yang mendalam.
            </p>
        </div>
    </div>

    {{-- Story Section 1 --}}
    <section class="py-20 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-10 flex flex-col lg:flex-row items-center gap-20">
            <div class="w-full lg:w-1/2 fade-in">
                <div class="relative">
                    <div class="absolute -inset-4 border border-white/10 translate-x-4 translate-y-4"></div>
                    <img src="https://images.unsplash.com/photo-1615484477778-ca3b77940c25?auto=format&fit=crop&q=80" 
                         class="relative z-10 w-full h-[500px] object-cover grayscale">
                </div>
            </div>
            <div class="w-full lg:w-1/2 space-y-8 fade-in" style="animation-delay: 0.3s">
                <h2 class="text-2xl font-playfair italic">The Artisanal Touch</h2>
                <p class="text-gray-500 text-sm leading-[2] uppercase tracking-widest">
                    Setiap motif Arkanza, Parang, hingga Kawung yang kami hadirkan diproses dengan ketelitian tangan para pengrajin lokal. Kami mempertahankan teknik tradisional untuk menjaga kemurnian seni batik tulis dan cap, memastikan setiap produk memiliki karakter unik yang tidak bisa direplikasi oleh mesin.
                </p>
                <div class="h-px w-20 bg-amber-500"></div>
            </div>
        </div>
    </section>

    {{-- Core Values Section --}}
    <section class="py-32 bg-hmns-dark">
        <div class="max-w-7xl mx-auto px-10 grid grid-cols-1 md:grid-cols-3 gap-16 text-center">
            <div class="space-y-6 fade-in">
                <p class="text-amber-500 text-[10px] tracking-widest font-bold">01</p>
                <h3 class="text-xs uppercase tracking-[0.3em] font-bold text-white">Authenticity</h3>
                <p class="text-gray-500 text-[10px] leading-relaxed tracking-widest uppercase">Kami hanya menggunakan material terbaik, memastikan kenyamanan tanpa meninggalkan nilai tradisional.</p>
            </div>
            <div class="space-y-6 fade-in" style="animation-delay: 0.2s">
                <p class="text-amber-500 text-[10px] tracking-widest font-bold">02</p>
                <h3 class="text-xs uppercase tracking-[0.3em] font-bold text-white">Mindful Design</h3>
                <p class="text-gray-500 text-[10px] leading-relaxed tracking-widest uppercase">Setiap pola dirancang untuk memberikan rasa percaya diri dan keanggunan bagi pemakainya.</p>
            </div>
            <div class="space-y-6 fade-in" style="animation-delay: 0.4s">
                <p class="text-amber-500 text-[10px] tracking-widest font-bold">03</p>
                <h3 class="text-xs uppercase tracking-[0.3em] font-bold text-white">Everlasting</h3>
                <p class="text-gray-500 text-[10px] leading-relaxed tracking-widest uppercase">Produk kami dibuat untuk bertahan melampaui tren, menjadi warisan yang bisa Anda kenakan selamanya.</p>
            </div>
        </div>
    </section>

    {{-- Final Quote --}}
    <section class="py-40 text-center relative">
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] select-none pointer-events-none">
            <h2 class="text-[15vw] font-playfair italic">Remembered</h2>
        </div>
        <div class="relative z-10 max-w-2xl mx-auto px-10">
            <h2 class="text-3xl font-playfair italic leading-relaxed">
                "We don't just sell batik. We weave memories that make you adored and loved."
            </h2>
            <div class="mt-12">
                <a href="{{ route('catalog.index') }}" class="text-[10px] uppercase tracking-[0.4em] border border-white/20 px-10 py-5 hover:bg-white hover:text-black transition">
                    Start Your Journey
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
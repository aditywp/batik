@extends('layouts.customer')

@section('title', 'Philosophy — Batik Ifawati')

@section('content')
<div class="min-h-screen bg-black text-white pt-36 pb-20 overflow-hidden">
    {{-- Header Section --}}
    <div class="max-w-7xl mx-auto px-6 sm:px-10 mb-32">
        <div class="max-w-3xl fade-in">
            <p class="text-[10px] uppercase tracking-[0.6em] text-amber-500 font-bold mb-6">Our Eternal Essence</p>
            <h1 class="text-5xl sm:text-6xl lg:text-8xl font-playfair italic leading-[1.1] mb-12 tracking-tight">
                Beyond Patterns, <br>
                <span class="not-italic text-stone-100">A Legacy of Soul.</span>
            </h1>
            <p class="text-stone-400 text-xs uppercase tracking-[0.25em] leading-[2.2] font-medium max-w-2xl">
                Di Batik Ifawati, kami percaya bahwa sehelai kain bukan sekadar komoditas sandang. Ia adalah kanvas sakral kehidupan, tempat setiap goresan canting malam dan celupan warna membawa doa, harapan, serta manifestasi identitas budaya Nusantara yang mendalam.
            </p>
        </div>
    </div>

    {{-- Story Section 1: Artisanal Touch --}}
    <section class="py-24 border-t border-white/5 bg-gradient-to-b from-black to-stone-950/40">
        <div class="max-w-7xl mx-auto px-6 sm:px-10 flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
            {{-- Frame Foto Dengan Konstruksi Grid UI/UX Premium --}}
            <div class="w-full lg:w-1/2 fade-in">
                <div class="relative w-full aspect-[4/3] lg:aspect-[3/4] overflow-hidden rounded-sm group shadow-2xl">
                    <div class="absolute -inset-4 border border-white/10 translate-x-4 translate-y-4 pointer-events-none transition-transform duration-500 group-hover:translate-x-2 group-hover:translate-y-2 z-10"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent z-20 pointer-events-none"></div>
                    <img src="{{ asset('storage/photos/Gemini_Generated_Image_w77ckow77ckow77c.png') }}" 
                         alt="Craftsmanship" 
                         class="w-full h-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105">
                </div>
            </div>
            
            <div class="w-full lg:w-1/2 space-y-8 fade-in text-left">
                <p class="text-[9px] uppercase tracking-[0.4em] text-amber-500/80 font-bold">The Craftsmanship</p>
                <h2 class="text-3xl sm:text-4xl font-playfair italic text-white tracking-wide">The Artisanal Touch</h2>
                <p class="text-stone-400 text-xs leading-[2.2] uppercase tracking-[0.18em] font-medium">
                    Setiap jengkal motif Parang hingga lilitan Kawung yang kami hadirkan diproses murni melalui ketelitian tangan para pengrajin lokal. Kami berkomitmen mempertahankan teknik patron tradisional guna menjaga kemurnian seni batik tulis asli, memastikan setiap helai benang melahirkan karakter personal unik yang tidak akan pernah bisa direplikasi oleh dinginnya mesin komparasi industri.
                </p>
                <div class="pt-4">
                    <div class="h-px w-24 bg-amber-500"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section Tambahan: Kategori Preservasi Mahakarya (Batik Tulis vs Cap) --}}
    <section class="py-28 border-t border-b border-white/5 bg-stone-950/30">
        <div class="max-w-7xl mx-auto px-6 sm:px-10">
            <div class="mb-20 text-left">
                <p class="text-[9px] uppercase tracking-[0.4em] text-amber-500/80 font-bold mb-3">Preservation of Art</p>
                <h2 class="text-3xl font-playfair italic text-white">Dua Metode, Satu Jiwa Tradisi</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 lg:gap-24">
                <div class="space-y-4 border-l border-white/10 pl-6 group hover:border-amber-500/50 transition-colors duration-300">
                    <h4 class="text-xs uppercase tracking-[0.25em] font-bold text-stone-200 group-hover:text-amber-500 transition-colors">Batik Tulis Masterpiece</h4>
                    <p class="text-[11px] text-stone-500 uppercase tracking-widest leading-[2.2]">Dibuat menggunakan canting tradisional dengan goresan malam cair lapis demi lapis. Ketidaksempurnaan titik malam justru menjadi segel keaslian yang bernilai tinggi dan eksklusif.</p>
                </div>
                <div class="space-y-4 border-l border-white/10 pl-6 group hover:border-amber-500/50 transition-colors duration-300">
                    <h4 class="text-xs uppercase tracking-[0.25em] font-bold text-stone-200 group-hover:text-amber-500 transition-colors">Batik Cap Klasik</h4>
                    <p class="text-[11px] text-stone-500 uppercase tracking-widest leading-[2.2]">Menggunakan stempel tembaga buatan tangan dengan tekanan presisi manual. Menghasilkan perulangan pola simetris yang rapi namun tetap mempertahankan tekstur rasa seni lilin otentik.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Core Values Section --}}
    <section class="py-32 bg-black">
        <div class="max-w-7xl mx-auto px-6 sm:px-10 grid grid-cols-1 md:grid-cols-3 gap-16 lg:gap-24 text-left">
            <div class="space-y-6 border-t border-white/5 pt-8 group">
                <p class="text-amber-500 text-[10px] tracking-widest font-black transition-transform duration-300 group-hover:translate-x-1">01</p>
                <h3 class="text-xs uppercase tracking-[0.3em] font-bold text-white tracking-widest">Authenticity Maturing</h3>
                <p class="text-stone-500 text-[10px] leading-[2.2] tracking-widest uppercase font-medium">Kami mengawinkan material katun premium dan sutra alam pilihan untuk menjamin kenyamanan mutlak tanpa mencederai sakralnya nilai filosofi lokal.</p>
            </div>
            <div class="space-y-6 border-t border-white/5 pt-8 group">
                <p class="text-amber-500 text-[10px] tracking-widest font-black transition-transform duration-300 group-hover:translate-x-1">02</p>
                <h3 class="text-xs uppercase tracking-[0.3em] font-bold text-white tracking-widest">Mindful Design Layout</h3>
                <p class="text-stone-500 text-[10px] leading-[2.2] tracking-widest uppercase font-medium">Setiap potongan pola ukur diposisikan secara matang demi menghadirkan siluet potongan busana yang memancarkan wibawa karismatik dan keanggunan abadi.</p>
            </div>
            <div class="space-y-6 border-t border-white/5 pt-8 group">
                <p class="text-amber-500 text-[10px] tracking-widest font-black transition-transform duration-300 group-hover:translate-x-1">03</p>
                <h3 class="text-xs uppercase tracking-[0.3em] font-bold text-white tracking-widest">Everlasting Heritage</h3>
                <p class="text-stone-500 text-[10px] leading-[2.2] tracking-widest uppercase font-medium">Produk kami didesain kokoh melampaui sekat tren musiman kontemporer, berevolusi menjadi sebuah benda warisan berharga yang bisa Anda teruskan selamanya.</p>
            </div>
        </div>
    </section>

    {{-- Final Quote --}}
    <section class="py-44 text-center relative border-t border-white/5 bg-gradient-to-t from-stone-950 to-black">
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] select-none pointer-events-none">
            <h2 class="text-[16vw] font-playfair italic tracking-tighter">Remembered</h2>
        </div>
        <div class="relative z-10 max-w-3xl mx-auto px-6 sm:px-10">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-playfair italic leading-[1.6] text-stone-100 tracking-wide">
                "We don't just sell batik. We weave fragments of memories that make your presence adored, respected, and loved."
            </h2>
            <div class="mt-16">
                <a href="{{ route('catalog.index') }}" class="inline-block text-[10px] uppercase tracking-[0.45em] border border-white/20 px-12 py-5 text-white bg-transparent hover:bg-white hover:text-black hover:border-white transition-all duration-300 font-bold shadow-xl">
                    Start Your Journey
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
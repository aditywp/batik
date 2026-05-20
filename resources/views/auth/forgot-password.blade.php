<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Minta Reset Sandi — Batik Ifawati Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body {
            background-color: #faf9f6;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-[#faf9f6] antialiased text-[#1a1a2e]">

    <div class="min-h-screen flex flex-col md:flex-row w-full">
        
        {{-- SISI KIRI: BRANDING VISUAL FILOSOFI BATIK IFAWATI --}}
        <div class="hidden md:flex md:w-1/2 bg-[#1a1a2e] relative overflow-hidden flex-col justify-between p-12 z-10 min-h-screen">
            {{-- Pola Hiasan Latar Belakang Geometris Batik --}}
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="batik-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M20 0 L40 20 L20 40 L0 20 Z" fill="none" stroke="#e8c9a0" stroke-width="1"/>
                            <circle cx="20" cy="20" r="3" fill="#e8c9a0"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#batik-pattern)" />
                </svg>
            </div>

            {{-- Bagian Atas Sisi Kiri --}}
            <div class="relative z-20">
                <span class="text-xs font-black uppercase tracking-[0.3em] text-[#e8c9a0] bg-[#e8c9a0]/10 px-3 py-1.5 rounded-lg border border-[#e8c9a0]/20">Keamanan Sistem</span>
                <h2 class="text-3xl font-black text-white italic mt-6 uppercase tracking-tight">Batik Ifawati</h2>
            </div>

            {{-- Bagian Bawah Sisi Kiri --}}
            <div class="relative z-20 max-w-md">
                <p class="text-2xl font-light leading-relaxed text-slate-200">
                    "Pemulihan akses terenkripsi untuk menjaga integritas data manajerial <span class="text-[#e8c9a0] font-bold italic">Batik Ifawati</span>."
                </p>
                <div class="mt-6 flex items-center gap-3 text-xs font-bold text-slate-400">
                    <span>© 2026 Admin Management System</span>
                </div>
            </div>
        </div>

        {{-- SISI KANAN: AREA FORM PERMINTAAN RESET LINK --}}
        <div class="w-full md:w-1/2 flex items-center justify-center p-6 sm:p-12 md:p-16 bg-[#faf9f6]">
            <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-[2.5rem] shadow-xl shadow-stone-200/50 border border-stone-100">
                
                {{-- Judul & Informasi Penjelas UX --}}
                <div class="mb-6">
                    <h3 class="text-2xl font-black text-[#1a1a2e] tracking-tight uppercase italic">Pulihkan Sandi</h3>
                    <p class="text-gray-500 text-xs mt-2.5 leading-relaxed font-medium">
                        Jangan khawatir! Masukkan alamat email terdaftar Anda di bawah ini, dan sistem otentikasi kami akan mengirimkan tautan pemulihan kata sandi baru secara instan.
                    </p>
                </div>

                {{-- Status Notifikasi Sukses Pengiriman Email (Session Status) --}}
                @if (session('status'))
                    <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-xs font-bold text-green-800 shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-6">
                        <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Alamat Email Terdaftar</label>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   placeholder="nama@email.com"
                                   class="w-full bg-gray-50/60 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1a1a2e] focus:border-[#1a1a2e] pl-10 pr-4 h-12 transition-all font-medium text-slate-800 placeholder:text-stone-300" />
                            <div class="absolute left-3 top-3.5 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                </svg>
                            </div>
                        </div>
                        @if ($errors->has('email'))
                            <p class="mt-1.5 text-xs text-red-500 font-bold">{{ $errors->first('email') }}</p>
                        @endif
                    </div>

                    {{-- Tombol Eksekusi Kirim Link & Opsi Kembali Ke Login --}}
                    <div class="flex flex-col gap-4">
                        <button type="submit" 
                                class="w-full bg-[#1a1a2e] text-[#e8c9a0] h-12 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-black hover:shadow-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-[#1a1a2e]/10">
                            <span>Kirim Tautan Pemulihan</span>
                            <svg class="w-4 h-4 text-[#e8c9a0]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </button>

                        <a href="{{ route('login') }}" class="text-center text-xs font-black text-gray-400 hover:text-[#1a1a2e] uppercase tracking-wider transition-colors py-1">
                            ← Kembali ke Halaman Login
                        </a>
                    </div>
                </form>

            </div>
        </div>

    </div>
</body>
</html>
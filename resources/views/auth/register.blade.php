<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun — Batik Ifawati</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body {
            background-color: #faf9f6;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Menghilangkan ikon mata bawaan browser (seperti Microsoft Edge) */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
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
                <span class="text-xs font-black uppercase tracking-[0.3em] text-[#e8c9a0] bg-[#e8c9a0]/10 px-3 py-1.5 rounded-lg border border-[#e8c9a0]/20">Pendaftaran Member</span>
                <h2 class="text-3xl font-black text-white italic mt-6 uppercase tracking-tight">Batik Ifawati</h2>
            </div>

            {{-- Bagian Bawah Sisi Kiri --}}
            <div class="relative z-20 max-w-md">
                <p class="text-2xl font-light leading-relaxed text-slate-200">
                    "Bergabunglah bersama kami untuk menikmati penawaran kain batik esklusif dengan <span class="text-[#e8c9a0] font-bold italic">Kualitas Premium</span> langsung ke genggaman Anda."
                </p>
                <div class="mt-6 flex items-center gap-3 text-xs font-bold text-slate-400">
                    <span>© 2026 Admin Management System</span>
                </div>
            </div>
        </div>

        {{-- SISI KANAN: AREA FORM REGISTER UTAMA --}}
        <div class="w-full md:w-1/2 flex items-center justify-center p-6 sm:p-12 md:p-16 bg-[#faf9f6]">
            <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-[2.5rem] shadow-xl shadow-stone-200/50 border border-stone-100">
                
                {{-- Judul Form Sambutan --}}
                <div class="mb-6">
                    <h3 class="text-2xl font-black text-[#1a1a2e] tracking-tight uppercase italic">Registrasi Akun</h3>
                    <p class="text-gray-400 text-xs mt-1 font-medium">Buat akun baru Anda untuk mulai mengeksplorasi katalog kami.</p>
                </div>

                {{-- Blok Peringatan Error Global & Validasi --}}
                @if (session('error') || $errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm">
                        <div class="flex items-center mb-1.5">
                            <svg class="mr-2 h-5 w-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-black text-[11px] uppercase tracking-widest text-red-800">Pendaftaran Gagal</span>
                        </div>
                        <ul class="list-disc pl-9 text-red-600 text-[11px] font-medium leading-relaxed">
                            @if(session('error'))
                                <li>{{ session('error') }}</li>
                            @endif
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" x-data="{ showPassword: false, showConfirmPassword: false }">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                        <div class="relative">
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                                   placeholder="Masukkan Nama Lengkap"
                                   class="w-full bg-gray-50/60 border {{ $errors->has('name') ? 'border-red-400' : 'border-stone-200' }} rounded-xl text-sm focus:ring-2 focus:ring-[#1a1a2e] focus:border-[#1a1a2e] pl-10 pr-4 h-11 transition-all font-medium text-slate-800 placeholder:text-stone-300" />
                            <div class="absolute left-3 top-3 {{ $errors->has('name') ? 'text-red-400' : 'text-gray-400' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Alamat Email</label>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                   placeholder="Masukkan Alamat Email"
                                   class="w-full bg-gray-50/60 border {{ $errors->has('email') ? 'border-red-400' : 'border-stone-200' }} rounded-xl text-sm focus:ring-2 focus:ring-[#1a1a2e] focus:border-[#1a1a2e] pl-10 pr-4 h-11 transition-all font-medium text-slate-800 placeholder:text-stone-300" />
                            <div class="absolute left-3 top-3 {{ $errors->has('email') ? 'text-red-400' : 'text-gray-400' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kata Sandi Baru</label>
                        <div class="relative">
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password"
                                   placeholder="••••••••"
                                   class="w-full bg-gray-50/60 border {{ $errors->has('password') ? 'border-red-400' : 'border-stone-200' }} rounded-xl text-sm focus:ring-2 focus:ring-[#1a1a2e] focus:border-[#1a1a2e] pl-10 pr-12 h-11 transition-all font-medium text-slate-800 placeholder:text-stone-300" />
                            <div class="absolute left-3 top-3 {{ $errors->has('password') ? 'text-red-400' : 'text-gray-400' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-3 text-gray-400 hover:text-[#1a1a2e] transition-colors focus:outline-none">
                                <template x-if="!showPassword">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </template>
                                <template x-if="showPassword">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                                </template>
                            </button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Konfirmasi Kata Sandi</label>
                        <div class="relative">
                            <input id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                                   placeholder="••••••••"
                                   class="w-full bg-gray-50/60 border {{ $errors->has('password_confirmation') ? 'border-red-400' : 'border-stone-200' }} rounded-xl text-sm focus:ring-2 focus:ring-[#1a1a2e] focus:border-[#1a1a2e] pl-10 pr-12 h-11 transition-all font-medium text-slate-800 placeholder:text-stone-300" />
                            <div class="absolute left-3 top-3 {{ $errors->has('password_confirmation') ? 'text-red-400' : 'text-gray-400' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-3 text-gray-400 hover:text-[#1a1a2e] transition-colors focus:outline-none">
                                <template x-if="!showConfirmPassword">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </template>
                                <template x-if="showConfirmPassword">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                                </template>
                            </button>
                        </div>
                    </div>

                    {{-- Tombol Submit Registrasi Member Baru --}}
                    <div class="flex flex-col gap-4">
                        <button type="submit" 
                                class="w-full bg-[#1a1a2e] text-[#e8c9a0] h-12 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-black hover:shadow-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-[#1a1a2e]/10">
                            <span>Daftarkan Akun</span>
                            <svg class="w-4 h-4 text-[#e8c9a0]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </button>

                        <a href="{{ route('login') }}" class="text-center text-xs font-black text-gray-400 hover:text-[#1a1a2e] uppercase tracking-wider transition-colors py-1">
                            Sudah Terdaftar? Masuk Sini
                        </a>
                    </div>
                </form>

            </div>
        </div>

    </div>

    {{-- SCRIPT UNTUK POP-UP SWEETALERT2 --}}
    @if (session('registered_success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: 'Pendaftaran Berhasil!',
                    text: "{{ session('registered_success') }}",
                    icon: 'success',
                    confirmButtonText: 'OK, LANJUT MASUK',
                    allowOutsideClick: false,
                    confirmButtonColor: '#1a1a2e'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('login') }}";
                    }
                });
            });
        </script>
    @endif

</body>
</html>
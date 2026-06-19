<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Batik Ifawati — Start to be Remembered')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #000; 
            color: #fff; 
            scroll-behavior: smooth;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .font-playfair { font-family: 'Playfair Display', serif; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #000; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #444; }
        .fade-in { animation: fadeIn 0.8s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        main { flex-grow: 1; }
    </style>

    @stack('styles')
</head>
<body class="antialiased overflow-x-hidden" x-data="{ mobileMenuOpen: false }">

    <nav class="fixed top-0 w-full z-[100] bg-black/80 backdrop-blur-md border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            
            <a href="{{ route('welcome') }}" class="text-lg font-bold tracking-[0.3em] uppercase text-white">
                Batik Ifawati
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-10 text-[11px] uppercase tracking-[0.2em] font-medium">
                <a href="{{ route('welcome') }}" class="{{ request()->routeIs('welcome') ? 'text-amber-500' : 'text-gray-400 hover:text-white' }} transition">Beranda</a>
                <a href="{{ route('catalog.index') }}" class="{{ request()->routeIs('catalog.index') ? 'text-amber-500' : 'text-gray-400 hover:text-white' }} transition">Katalog</a>
                
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="text-gray-400 hover:text-white transition uppercase tracking-[0.2em] py-8 flex items-center gap-1">
                        Collections
                        <svg class="w-2 h-2 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-cloak class="absolute top-[80%] left-0 w-56 bg-[#0a0a0a] border border-white/10 shadow-2xl py-6 px-6 z-[150]">
                        <ul class="flex flex-col gap-4">
                            <li><a href="{{ route('catalog.index', ['collection' => 'women']) }}" class="text-[11px] text-gray-400 hover:text-white transition tracking-[0.2em]">WOMEN</a></li>
                            <li><a href="{{ route('catalog.index', ['collection' => 'men']) }}" class="text-[11px] text-gray-400 hover:text-white transition tracking-[0.2em]">MEN</a></li>
                            <li><a href="{{ route('catalog.index', ['collection' => 'craft']) }}" class="text-[11px] text-gray-400 hover:text-white transition tracking-[0.2em]">CRAFT</a></li>
                            <li><a href="{{ route('catalog.index', ['collection' => 'family']) }}" class="text-[11px] text-gray-400 hover:text-white transition tracking-[0.2em]">FAMILY</a></li>
                        </ul>
                    </div>
                </div>
                <a href="{{ route('philosophy') }}" class="{{ request()->routeIs('philosophy') ? 'text-amber-500' : 'text-gray-400 hover:text-white' }} transition">Filosofi</a>
            </div>

            {{-- Mobile Toggle Button --}}
            <button class="md:hidden text-white p-2" @click="mobileMenuOpen = !mobileMenuOpen">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>

            {{-- Desktop Actions --}}
            <div class="hidden md:flex items-center gap-6">
                @auth
                    {{-- Keranjang dengan Badge --}}
                    <a href="{{ route('customer.cart.index') }}" class="relative text-gray-400 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        @if(Auth::user()->cartItems && Auth::user()->cartItems->count() > 0)
                            <span class="absolute -top-2 -right-2 bg-amber-600 text-[9px] text-white font-black px-1.5 py-0.5 rounded-full ring-2 ring-black">
                                {{ Auth::user()->cartItems->count() }}
                            </span>
                        @endif
                    </a>

                    {{-- Profil Akun dengan Dropdown --}}
                    <div class="flex items-center gap-3 group cursor-pointer relative" x-data="{ open: false }" @click.away="open = false">
                        <div @click="open = !open" class="w-9 h-9 rounded-full bg-stone-800 flex items-center justify-center border border-white/10 shadow-lg group-hover:border-amber-500 transition">
                            <span class="text-xs font-bold text-amber-500">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        </div>

                        <div x-show="open" x-cloak x-transition class="absolute top-12 right-0 w-52 bg-[#0a0a0a] border border-white/10 shadow-2xl rounded-xl py-2 z-[110]">
                            <a href="{{ route('customer.orders.index') }}" class="block px-4 py-2 text-sm text-[#f59e0b] hover:bg-gray-800 transition-all">MY ORDERS</a>
                            
                            {{-- TAMBAHAN MENU VOUCHER & POIN --}}
                            <a href="{{ route('customer.vouchers.index') }}" class="block px-4 py-2 text-[10px] uppercase tracking-widest text-[#f59e0b] hover:bg-gray-800 transition-all border-b border-white/5">VOUCHERS & POINTS</a>
                            
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-[10px] uppercase tracking-widest text-gray-400 hover:text-amber-500 border-b border-white/5">Settings</a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-[10px] uppercase tracking-widest text-red-500 hover:bg-red-500/10 transition">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-[11px] uppercase font-bold text-white hover:text-amber-500">Login</a>
                    <a href="{{ route('register') }}" class="text-[11px] uppercase font-bold border border-white/20 px-5 py-2 hover:bg-white hover:text-black transition">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Mobile Menu Panel --}}
    <div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 z-[200] bg-black md:hidden flex flex-col justify-center items-center gap-8 uppercase tracking-[0.2em] text-sm">
        <button class="absolute top-8 right-6 text-white" @click="mobileMenuOpen = false">CLOSE</button>
        <a href="{{ route('welcome') }}" @click="mobileMenuOpen = false">BERANDA</a>
        <a href="{{ route('catalog.index') }}" @click="mobileMenuOpen = false">KATALOG</a>
        <a href="{{ route('philosophy') }}" @click="mobileMenuOpen = false">FILOSOFI</a>
        
        @auth
            <a href="{{ route('customer.cart.index') }}" @click="mobileMenuOpen = false">CART</a>
            <a href="{{ route('customer.orders.index') }}" @click="mobileMenuOpen = false">MY ORDERS</a>
            
            {{-- TAMBAHAN MENU VOUCHER & POIN DI MOBILE --}}
            <a href="{{ route('customer.vouchers.index') }}" @click="mobileMenuOpen = false" class="text-amber-500">VOUCHERS & POINTS</a>
            
            <a href="{{ route('profile.edit') }}" @click="mobileMenuOpen = false">SETTINGS</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-red-500">LOGOUT</button>
            </form>
        @else
            <a href="{{ route('login') }}" @click="mobileMenuOpen = false">LOGIN</a>
            <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="text-amber-500">REGISTER</a>
        @endauth
    </div>

    <nav class="pt-20">
        @yield('content')
    </nav>

    <footer class="py-20 border-t border-white/5 bg-black w-full text-white mt-auto">
        <div class="max-w-7xl mx-auto px-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-16 mb-24">
                <div class="text-left">
                    <h2 class="text-xl font-bold text-white tracking-[0.3em] uppercase mb-6">Batik Ifawati</h2>
                    <p class="text-gray-500 text-[11px] leading-[2.2] uppercase tracking-[0.1em]">
                        Menghadirkan keindahan tradisi Nusantara melalui sentuhan modern.
                    </p>
                </div>
                <div class="text-left">
                    <h4 class="text-[10px] font-black text-white mb-8 uppercase tracking-[0.3em]">Bantuan</h4>
                    <ul class="text-gray-500 text-[10px] space-y-4 uppercase tracking-[0.2em]">
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-amber-500 transition">Katalog</a></li>
                        <li><a href="{{ route('customer.orders.index') }}" class="hover:text-amber-500 transition">Lacak Pesanan</a></li>
                    </ul>
                </div>
                <div class="text-left">
                    <h4 class="text-[10px] font-black text-white mb-8 uppercase tracking-[0.3em]">Legalitas</h4>
                    <ul class="text-gray-500 text-[10px] space-y-4 uppercase tracking-[0.2em]">
                        <li><a href="#" class="hover:text-amber-500 transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-amber-500 transition">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div class="text-left">
                    <h4 class="text-[10px] font-black text-white mb-8 uppercase tracking-[0.3em]">Hubungi Kami</h4>
                    <ul class="text-gray-500 text-[10px] space-y-4 uppercase tracking-[0.2em]">
                        <li><a href="https://wa.me/628157949494" target="_blank" class="hover:text-amber-500 transition">WhatsApp</a></li>
                        <li><a href="https://instagram.com/batik_ifawati" target="_blank" class="hover:text-amber-500 transition">Instagram</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
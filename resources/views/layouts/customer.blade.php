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
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #000; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #444; }

        /* Animasi fade-in */
        .fade-in { animation: fadeIn 0.8s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        main { flex-grow: 1; }
    </style>

    @stack('styles')
</head>
<body class="antialiased overflow-x-hidden">

    <nav class="fixed top-0 w-full z-[100] bg-black/80 backdrop-blur-md border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            
            <a href="{{ route('welcome') }}" class="text-lg font-bold tracking-[0.3em] uppercase text-white">
                Batik Ifawati
            </a>

            <div class="hidden md:flex items-center gap-10 text-[11px] uppercase tracking-[0.2em] font-medium">
                <a href="{{ route('welcome') }}" class="{{ request()->routeIs('welcome') ? 'text-amber-500' : 'text-gray-400 hover:text-white' }} transition">Beranda</a>
                <a href="{{ route('catalog.index') }}" class="{{ request()->routeIs('catalog.index') ? 'text-amber-500' : 'text-gray-400 hover:text-white' }} transition">Katalog</a>
                
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="text-gray-400 hover:text-white transition uppercase tracking-[0.2em] py-8 flex items-center gap-1">
                        Collections
                        <svg class="w-2 h-2 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-cloak
                         class="absolute top-[80%] left-0 w-72 bg-[#0a0a0a] border border-white/10 shadow-2xl py-8 px-10 z-[150]">
                        
                        <div class="inline-block bg-white text-black px-3 py-1 mb-8">
                            <span class="text-[11px] font-black uppercase tracking-tighter">Collections</span>
                        </div>

                        <ul class="flex flex-col gap-6">
                            <li><a href="{{ route('catalog.index', ['collection' => 'women']) }}" class="text-[11px] text-gray-400 hover:text-white hover:translate-x-2 transition-all duration-300 block tracking-[0.2em]">WOMEN</a></li>
                            <li><a href="{{ route('catalog.index', ['collection' => 'men']) }}" class="text-[11px] text-gray-400 hover:text-white hover:translate-x-2 transition-all duration-300 block tracking-[0.2em]">MEN</a></li>
                            <li><a href="{{ route('catalog.index', ['collection' => 'kids']) }}" class="text-[11px] text-gray-400 hover:text-white hover:translate-x-2 transition-all duration-300 block tracking-[0.2em]">KIDS</a></li>
                            <li><a href="{{ route('catalog.index', ['collection' => 'craft']) }}" class="text-[11px] text-gray-400 hover:text-white hover:translate-x-2 transition-all duration-300 block tracking-[0.2em]">CRAFT</a></li>
                            <li><a href="{{ route('catalog.index', ['collection' => 'family']) }}" class="text-[11px] text-gray-400 hover:text-white hover:translate-x-2 transition-all duration-300 block tracking-[0.2em]">FAMILY</a></li>
                        </ul>
                    </div>
                </div>

                <a href="{{ route('philosophy') }}" class="{{ request()->routeIs('philosophy') ? 'text-amber-500' : 'text-gray-400 hover:text-white' }} transition">Filosofi</a>
            </div>

            <div class="flex items-center gap-6">
                <a href="{{ Auth::check() ? route('customer.cart.index') : route('login') }}" class="relative text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    @auth
                        @if(Auth::user()->cartItems && Auth::user()->cartItems->count() > 0)
                            <span class="absolute -top-2 -right-2 bg-amber-600 text-[9px] text-white font-black px-1.5 py-0.5 rounded-full ring-2 ring-black">
                                {{ Auth::user()->cartItems->count() }}
                            </span>
                        @endif
                    @endauth
                </a>

                <div class="flex items-center gap-4 border-l border-white/10 pl-6">
                    @auth
                        <div class="flex items-center gap-3 group cursor-pointer relative" x-data="{ open: false }" @click.away="open = false">
                            <div @click="open = !open" class="text-right hidden sm:block">
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest leading-none mb-1">Account</p>
                                <p class="text-xs font-bold text-white tracking-wider">{{ explode(' ', Auth::user()->name)[0] }}</p>
                            </div>
                            <div @click="open = !open" class="w-9 h-9 rounded-full bg-stone-800 flex items-center justify-center border border-white/10 shadow-lg group-hover:border-amber-500 transition">
                                <span class="text-xs font-bold text-amber-500">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>

                            <div x-show="open" x-cloak x-transition class="absolute top-12 right-0 w-48 bg-[#0a0a0a] border border-white/10 shadow-2xl rounded-xl py-2 z-[110]">
                                <a href="{{ route('customer.home') }}" class="block px-4 py-2 text-[10px] uppercase tracking-widest text-gray-400 hover:text-amber-500">My Orders</a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-[10px] uppercase tracking-widest text-gray-400 hover:text-amber-500 border-b border-white/5">Settings</a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-[10px] uppercase tracking-widest text-red-500 hover:bg-red-500/10 transition">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-[11px] uppercase tracking-[0.2em] font-bold text-white hover:text-amber-500 transition">Login</a>
                        <a href="{{ route('register') }}" class="hidden lg:block text-[11px] uppercase tracking-[0.2em] border border-white/20 px-5 py-2 hover:bg-white hover:text-black transition font-bold">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-20">
        @yield('content')
    </main>

    <footer class="py-20 border-t border-white/5 bg-black w-full text-white">
        <div class="max-w-7xl mx-auto px-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-16 mb-24">
                
                <div class="col-span-1 md:col-span-2 text-left">
                    <h2 class="text-xl font-bold text-white tracking-[0.3em] uppercase mb-6">Batik Ifawati</h2>
                    <p class="text-gray-500 text-[11px] max-w-sm leading-[2.2] uppercase tracking-[0.1em]">
                        Menghadirkan keindahan tradisi Nusantara melalui sentuhan modern. Kami percaya setiap motif batik menceritakan filosofi yang akan membuat Anda selalu diingat.
                    </p>
                    <div class="mt-10 flex max-w-sm border-b border-white/20 focus-within:border-white transition">
                        <input type="email" placeholder="JOIN IFAWATI CIRCLE" class="bg-transparent py-4 w-full text-[10px] tracking-widest focus:outline-none text-white uppercase">
                        <button class="text-white text-[10px] uppercase font-bold tracking-widest px-4 hover:text-amber-500 transition">Join</button>
                    </div>
                </div>

                <div class="text-left">
                    <h4 class="text-[10px] font-black text-white mb-8 uppercase tracking-[0.3em]">Bantuan</h4>
                    <ul class="text-gray-500 text-[10px] space-y-4 uppercase tracking-[0.2em]">
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-amber-500 transition">Katalog</a></li>
                        <li><a href="#" class="hover:text-amber-500 transition">Lacak Pesanan</a></li>
                        <li><a href="#" class="hover:text-amber-500 transition">Hubungi Kami</a></li>
                    </ul>
                </div>

                <div class="text-left">
                    <h4 class="text-[10px] font-black text-white mb-8 uppercase tracking-[0.3em]">Legalitas</h4>
                    <ul class="text-gray-500 text-[10px] space-y-4 uppercase tracking-[0.2em]">
                        <li><a href="#" class="hover:text-amber-500 transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-amber-500 transition">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row justify-between items-center border-t border-white/5 pt-12 text-[9px] uppercase tracking-[0.3em] text-gray-600 gap-6 text-center md:text-left">
                <div class="flex flex-col md:flex-row items-center gap-2 md:gap-6">
                    <p>© 2026 Batik Ifawati Artifacts.</p>
                    <p class="hidden md:block text-white/10">|</p>
                    <p class="text-gray-400">Inspired by Tradition, Crafted for You.</p>
                </div>
                
                <div class="flex gap-8">
                    <a href="#" class="hover:text-white transition">Terms</a>
                    <a href="#" class="hover:text-white transition">Privacy</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
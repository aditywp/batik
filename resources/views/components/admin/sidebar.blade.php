<aside class="w-56 bg-[#1a1a2e] flex flex-col h-full flex-shrink-0">

    {{-- Logo --}}
    <div class="px-5 py-5 border-b border-white/10">
        <h1 class="text-[#e8c9a0] font-semibold text-base tracking-wide uppercase">Batik Ifawati</h1>
        <p class="text-white/30 text-xs mt-0.5">Admin Panel</p>
    </div>

    {{-- Navigasi --}}
    <nav class="flex-1 py-4 overflow-y-auto">

        <p class="px-5 text-[10px] text-white/25 uppercase tracking-widest mb-2">Utama</p>

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-5 py-2.5 text-sm transition-colors
                  {{ request()->routeIs('admin.dashboard') 
                      ? 'text-[#e8c9a0] bg-[#e8c9a0]/10 border-l-2 border-[#e8c9a0]' 
                      : 'text-white/55 hover:text-white/85 hover:bg-white/5 border-l-2 border-transparent' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>

        {{-- Kategori --}}
        <a href="{{ route('admin.categories.index') }}"
           class="flex items-center gap-3 px-5 py-2.5 text-sm transition-colors
                  {{ request()->routeIs('admin.categories.*')
                      ? 'text-[#e8c9a0] bg-[#e8c9a0]/10 border-l-2 border-[#e8c9a0]'
                      : 'text-white/55 hover:text-white/85 hover:bg-white/5 border-l-2 border-transparent' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
            Kategori
        </a>

        {{-- Produk --}}
        <a href="{{ route('admin.products.index') }}"
           class="flex items-center gap-3 px-5 py-2.5 text-sm transition-colors
                  {{ request()->routeIs('admin.products.*')
                      ? 'text-[#e8c9a0] bg-[#e8c9a0]/10 border-l-2 border-[#e8c9a0]'
                      : 'text-white/55 hover:text-white/85 hover:bg-white/5 border-l-2 border-transparent' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Produk
        </a>

        <p class="px-5 text-[10px] text-white/25 uppercase tracking-widest mb-2 mt-6">Transaksi</p>

        {{-- Pesanan --}}
        <a href="{{ route('admin.orders.index') }}"
           class="flex items-center gap-3 px-5 py-2.5 text-sm transition-colors border-l-2
                  {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show')
                      ? 'text-[#e8c9a0] bg-[#e8c9a0]/10 border-[#e8c9a0]'
                      : 'text-white/55 hover:text-white/85 hover:bg-white/5 border-transparent' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Pesanan
            @php $pending = \App\Models\Order::where('status', 'pending')->count(); @endphp
            @if($pending > 0)
                <span class="ml-auto text-xs bg-amber-400 text-amber-900 font-semibold px-1.5 py-0.5 rounded-full">
                    {{ $pending }}
                </span>
            @endif
        </a>

        {{-- Laporan (MENU BARU) --}}
        <a href="{{ route('admin.orders.report') }}"
           class="flex items-center gap-3 px-5 py-2.5 text-sm transition-colors border-l-2
                  {{ request()->routeIs('admin.orders.report')
                      ? 'text-[#e8c9a0] bg-[#e8c9a0]/10 border-[#e8c9a0]'
                      : 'text-white/55 hover:text-white/85 hover:bg-white/5 border-transparent' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Laporan
        </a>

    </nav>

    {{-- Footer Sidebar --}}
    <div class="px-5 py-4 border-t border-white/10 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-[#e8c9a0] flex items-center justify-center
                    text-[#1a1a2e] text-xs font-semibold flex-shrink-0">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-white/70 text-xs font-medium truncate">{{ auth()->user()->name }}</p>
            <p class="text-white/30 text-[10px]">Administrator</p>
        </div>

        {{-- Tombol Logout --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="text-white/30 hover:text-red-400 transition-colors"
                title="Keluar">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    </div>

</aside>
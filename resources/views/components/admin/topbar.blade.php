<header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 sticky top-0 z-10">
    <div class="text-gray-500 font-medium">
        Halaman: <span class="text-gray-800">{{ ucfirst(Request::segment(2)) }}</span>
    </div>
    
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
        
        {{-- Tombol Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs bg-red-50 text-red-600 px-3 py-1.5 rounded-md hover:bg-red-100 transition-colors">
                Keluar
            </button>
        </form>
    </div>
</header>
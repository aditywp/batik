<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Penting untuk aksi Delete/AJAX --}}
    
    <title>@yield('title', 'Dashboard') — Batik Ifawati Admin</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js (Ditambahkan x-cloak style agar UI tidak kedip) -->
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="bg-stone-50 font-sans antialiased text-[#1a1a2e]">

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <x-admin.sidebar />

        {{-- AREA KONTEN UTAMA --}}
        <div class="flex flex-col flex-1 overflow-hidden">

            {{-- TOPBAR --}}
            <x-admin.topbar :title="$title ?? 'Dashboard'" />

            {{-- MAIN CONTENT --}}
            <main class="flex-1 overflow-y-auto p-6 lg:p-10">

                {{-- FLASH MESSAGE (Success) --}}
                @if (session('success'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-init="setTimeout(() => show = false, 3000)"
                         class="mb-6 flex items-center justify-between rounded-2xl bg-green-50 border border-green-200 px-5 py-4 text-green-800 text-sm shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-green-400 hover:text-green-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif

                {{-- FLASH MESSAGE (Error) --}}
                @if (session('error') || $errors->any())
                    <div x-data="{ show: true }" 
                         x-show="show"
                         class="mb-6 rounded-2xl bg-red-50 border border-red-200 px-5 py-4 text-red-800 text-sm shadow-sm">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3 font-bold">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Perhatian!
                            </div>
                            <button @click="show = false" class="text-red-400 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <ul class="list-disc list-inside space-y-1 opacity-80">
                            @if(session('error')) <li>{{ session('error') }}</li> @endif
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
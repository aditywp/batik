<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email — Batik Ifawati</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f8f9fa] text-stone-900 antialiased">

    <div class="min-h-screen flex items-center justify-center py-12 px-6">
        <div class="max-w-md w-full bg-white p-10 rounded-[2rem] border border-gray-100 shadow-xl">
            
            {{-- ICON HEADER --}}
            <div class="flex justify-center mb-8">
                <div class="w-20 h-20 bg-stone-50 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-stone-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </div>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-2xl font-black uppercase italic tracking-tighter text-black mb-3">Verifikasi Email</h2>
                <p class="text-xs text-stone-500 font-medium leading-relaxed italic">
                    Terima kasih telah mendaftar! Sebelum melangkah lebih jauh, mohon verifikasi alamat email Anda melalui tautan yang telah kami kirimkan ke inbox Anda.
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl">
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-700 text-center">
                        Tautan baru telah dikirim ke email Anda.
                    </p>
                </div>
            @endif

            <div class="space-y-4">
                {{-- Form Kirim Ulang Verifikasi --}}
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="w-full bg-black text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-[3px] hover:bg-orange-600 transition-all shadow-lg active:scale-95">
                        Resend Verification Email
                    </button>
                </form>

                {{-- Form Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-stone-400 py-3 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:text-stone-900 transition-all">
                        Log Out
                    </button>
                </form>
            </div>
            
        </div>
    </div>

</body>
</html>
<section class="bg-white rounded-3xl p-6 md:p-8 border border-gray-100 shadow-sm">
    <header class="mb-8 border-b border-gray-50 pb-5">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-orange-50 text-orange-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-black text-[#1a1a2e] uppercase tracking-tight italic">
                    {{ __('Profile Information') }}
                </h2>
                <p class="text-xs text-gray-400 font-medium mt-0.5">
                    {{ __("Update your account's profile credentials and active email address.") }}
                </p>
            </div>
        </div>
    </header>

    {{-- Form Terisolasi untuk Resend Verifikasi Email --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        {{-- KOLOM INPUT: NAMA LENGKAP --}}
        <div>
            <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Full Name</label>
            <div class="relative">
                <input id="name" name="name" type="text" class="w-full bg-gray-50/60 border border-stone-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 pl-10 pr-4 h-12 transition-all font-medium text-slate-800 placeholder:text-stone-300" 
                       value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap..." />
                <div class="absolute left-3.5 top-3.5 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            @if($errors->get('name'))
                <p class="mt-1.5 text-xs text-red-500 font-bold ml-1">{{ $errors->first('name') }}</p>
            @endif
        </div>

        {{-- KOLOM INPUT: ALAMAT EMAIL --}}
        <div>
            <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Email Address</label>
            <div class="relative">
                <input id="email" name="email" type="email" class="w-full bg-gray-50/60 border border-stone-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 pl-10 pr-4 h-12 transition-all font-medium text-slate-800 placeholder:text-stone-300" 
                       value="{{ old('email', $user->email) }}" required autocomplete="username" placeholder="nama@email.com" />
                <div class="absolute left-3.5 top-3.5 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                    </svg>
                </div>
            </div>
            @if($errors->get('email'))
                <p class="mt-1.5 text-xs text-red-500 font-bold ml-1">{{ $errors->first('email') }}</p>
            @endif

            {{-- Logika Email Belum Terverifikasi --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-100 flex flex-col gap-1">
                    <p class="text-xs text-amber-800 font-medium">
                        ⚠️ {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="underline font-black text-orange-600 hover:text-black transition-colors focus:outline-none ml-1">
                            {{ __('Click here to re-send verification link.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="font-bold text-[11px] text-green-600 mt-1">
                            ✨ {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- AREA TOMBOL SUBMIT & FEEDBACK ACTION --}}
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" 
                    class="bg-[#1a1a2e] text-[#e8c9a0] px-6 h-11 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-black hover:shadow-lg transition-all flex items-center justify-center gap-2 shadow-sm">
                <span>Save Changes</span>
                <svg class="w-4 h-4 text-[#e8c9a0]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </button>

            @if (session('status') === 'profile-updated')
                <div x-data Tint="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 3000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="flex items-center gap-1.5 text-emerald-600 text-xs font-black uppercase tracking-wider bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-100 shadow-sm" x-cloak>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>
                    <span>{{ __('Saved Successfully.') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
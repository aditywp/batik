<section class="bg-white rounded-3xl p-6 md:p-8 border border-gray-100 shadow-sm mt-8">
    <header class="mb-8 border-b border-gray-50 pb-5">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-orange-50 text-orange-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-black text-[#1a1a2e] uppercase tracking-tight italic">
                    {{ __('Update Password') }}
                </h2>
                <p class="text-xs text-gray-400 font-medium mt-0.5">
                    {{ __('Ensure your account is using a long, random password to stay secure.') }}
                </p>
            </div>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
        @csrf
        @method('put')

        {{-- KOLOM: PASSWORD SAAT INI --}}
        <div>
            <label for="update_password_current_password" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Current Password</label>
            <div class="relative">
                <input id="update_password_current_password" name="current_password" :type="showCurrent ? 'text' : 'password'" 
                       class="w-full bg-gray-50/60 border border-stone-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 pl-10 pr-12 h-12 transition-all font-medium text-slate-800 placeholder:text-stone-300" 
                       autocomplete="current-password" placeholder="••••••••" />
                <div class="absolute left-3.5 top-3.5 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <button type="button" @click="showCurrent = !showCurrent" class="absolute right-3.5 top-3.5 text-gray-400 hover:text-orange-600 transition-colors focus:outline-none">
                    <svg x-show="!showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5 text-xs text-red-500 font-bold ml-1" />
        </div>

        {{-- KOLOM: PASSWORD BARU --}}
        <div>
            <label for="update_password_password" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">New Password</label>
            <div class="relative">
                <input id="update_password_password" name="password" :type="showNew ? 'text' : 'password'" 
                       class="w-full bg-gray-50/60 border border-stone-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 pl-10 pr-12 h-12 transition-all font-medium text-slate-800 placeholder:text-stone-300" 
                       autocomplete="new-password" placeholder="••••••••" />
                <div class="absolute left-3.5 top-3.5 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <button type="button" @click="showNew = !showNew" class="absolute right-3.5 top-3.5 text-gray-400 hover:text-orange-600 transition-colors focus:outline-none">
                    <svg x-show="!showNew" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="showNew" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5 text-xs text-red-500 font-bold ml-1" />
        </div>

        {{-- KOLOM: KONFIRMASI PASSWORD BARU --}}
        <div>
            <label for="update_password_password_confirmation" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Confirm Password</label>
            <div class="relative">
                <input id="update_password_password_confirmation" name="password_confirmation" :type="showConfirm ? 'text' : 'password'" 
                       class="w-full bg-gray-50/60 border border-stone-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 pl-10 pr-12 h-12 transition-all font-medium text-slate-800 placeholder:text-stone-300" 
                       autocomplete="new-password" placeholder="••••••••" />
                <div class="absolute left-3.5 top-3.5 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3.5 top-3.5 text-gray-400 hover:text-orange-600 transition-colors focus:outline-none">
                    <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5 text-xs text-red-500 font-bold ml-1" />
        </div>

        {{-- AREA TOMBOL SUBMIT & FEEDBACK ACTION --}}
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" 
                    class="bg-[#1a1a2e] text-[#e8c9a0] px-6 h-11 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-black hover:shadow-lg transition-all flex items-center justify-center gap-2 shadow-sm">
                <span>Update Password</span>
                <svg class="w-4 h-4 text-[#e8c9a0]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4 L19 7" />
                </svg>
            </button>

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = false, 3000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="flex items-center gap-1.5 text-emerald-600 text-xs font-black uppercase tracking-wider bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-100 shadow-sm" x-cloak>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>
                    <span>{{ __('Password Updated.') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>
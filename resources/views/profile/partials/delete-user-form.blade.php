<section class="bg-white rounded-3xl p-6 md:p-8 border border-gray-100 shadow-sm mt-8">
    <header class="mb-8 border-b border-gray-50 pb-5">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-red-50 text-red-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-black text-[#1a1a2e] uppercase tracking-tight italic">
                    {{ __('Delete Account') }}
                </h2>
                <p class="text-xs text-gray-400 font-medium mt-0.5">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
                </p>
            </div>
        </div>
    </header>

    {{-- TOMBOL PEMICU MODAL UTAMA --}}
    <div>
        <button type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="bg-red-600 text-white px-6 h-11 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-red-700 hover:shadow-lg hover:shadow-red-100 transition-all flex items-center justify-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
            </svg>
            <span>{{ __('Delete Account') }}</span>
        </button>
    </div>

    {{-- MODAL KONFIRMASI DELETION --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 md:p-8 font-sans bg-white rounded-3xl" x-data="{ showConfirmPass: false }">
            @csrf
            @method('delete')

            <div class="mb-4">
                <h3 class="text-xl font-black text-red-900 uppercase tracking-tight italic">
                    {{ __('Are you sure you want to delete your account?') }}
                </h3>
                <p class="mt-2 text-xs text-gray-500 leading-relaxed font-medium">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>
            </div>

            {{-- INPUT PASSWORD KONFIRMASI DENGAN INTERACTIVE EYE TOGGLE --}}
            <div class="mt-6">
                <label for="password" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Confirm Security Password</label>
                <div class="relative w-full md:w-3/4">
                    <input id="password" name="password" :type="showConfirmPass ? 'text' : 'password'" 
                           class="w-full bg-gray-50/60 border border-stone-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-red-500 focus:border-red-500 pl-10 pr-12 h-12 transition-all font-medium text-slate-800 placeholder:text-stone-300" 
                           placeholder="{{ __('Password Keamanan Akun...') }}" required />
                    
                    {{-- Ikon Gembok --}}
                    <div class="absolute left-3.5 top-3.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>

                    {{-- Toggle Mata Alpine.js --}}
                    <button type="button" @click="showConfirmPass = !showConfirmPass" class="absolute right-3.5 top-3.5 text-gray-400 hover:text-red-600 transition-colors focus:outline-none">
                        <svg x-show="!showConfirmPass" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="showConfirmPass" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                    </button>
                </div>

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-xs text-red-500 font-bold ml-1" />
            </div>

            {{-- FOOTER MODAL ACTION --}}
            <div class="mt-8 flex justify-end gap-3 border-t border-gray-50 pt-4">
                <button type="button" x-on:click="$dispatch('close')" 
                        class="px-5 h-11 bg-stone-100 hover:bg-stone-200 text-stone-600 rounded-xl font-black text-xs uppercase tracking-widest transition-all">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" 
                        class="px-6 h-11 bg-red-600 hover:bg-red-700 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-md shadow-red-100 transition-all">
                    {{ __('Permanently Delete') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
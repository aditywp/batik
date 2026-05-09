{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.auth')

@section('title', 'Masuk — Batik Ifawati')

@section('content')

<div class="mb-6">
    <h2 class="text-xl font-semibold text-stone-900">Selamat datang kembali</h2>
    <p class="text-sm text-stone-400 mt-1">Masuk ke akun kamu untuk melanjutkan</p>
</div>

{{-- Error global (email/password salah) --}}
@if ($errors->has('email'))
    <div class="mb-4 flex items-center gap-2 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-700 text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
        </svg>
        {{ $errors->first('email') }}
    </div>
@endif

<form action="{{ route('auth.login.post') }}" method="POST" class="space-y-4">
    @csrf

    {{-- Email --}}
    <div>
        <label class="block text-sm font-medium text-stone-700 mb-1.5" for="email">
            Email
        </label>
        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            autocomplete="email"
            placeholder="nama@email.com"
            class="w-full rounded-lg border px-3.5 py-2.5 text-sm text-stone-900
                   placeholder-stone-300 outline-none transition-colors
                   {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-stone-200 bg-white' }}
                   focus:border-stone-900 focus:ring-0"
        />
    </div>

    {{-- Password --}}
    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label class="block text-sm font-medium text-stone-700" for="password">
                Password
            </label>
            {{-- Placeholder lupa password (bisa dikembangkan nanti) --}}
            <span class="text-xs text-stone-400">Lupa password?</span>
        </div>
        <div class="relative">
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                placeholder="Masukkan password"
                class="w-full rounded-lg border border-stone-200 bg-white px-3.5 py-2.5
                       text-sm text-stone-900 placeholder-stone-300 outline-none
                       transition-colors focus:border-stone-900 pr-10"
            />
            {{-- Toggle show/hide password --}}
            <button type="button" id="toggle-password"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700">
                <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Remember Me --}}
    <div class="flex items-center gap-2">
        <input type="checkbox" id="remember" name="remember"
               class="h-4 w-4 rounded border-stone-300 accent-stone-900 cursor-pointer">
        <label for="remember" class="text-sm text-stone-600 cursor-pointer">
            Ingat saya
        </label>
    </div>

    {{-- Submit --}}
    <button type="submit"
        class="w-full rounded-lg bg-stone-900 py-2.5 text-sm font-medium text-amber-200
               transition-colors hover:bg-stone-800 active:scale-[0.99] mt-2">
        Masuk
    </button>
</form>

<p class="mt-6 text-center text-sm text-stone-400">
    Belum punya akun?
    <a href="{{ route('auth.register') }}"
       class="font-medium text-stone-900 hover:underline">
        Daftar sekarang
    </a>
</p>

@push('scripts')
<script>
    document.getElementById('toggle-password').addEventListener('click', function () {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    });
</script>
@endpush

@endsection
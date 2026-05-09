{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.auth')

@section('title', 'Daftar — Batik Ifawati')

@section('content')

<div class="mb-6">
    <h2 class="text-xl font-semibold text-stone-900">Buat akun baru</h2>
    <p class="text-sm text-stone-400 mt-1">Bergabung dan mulai berbelanja batik premium</p>
</div>

<form action="{{ route('auth.register.post') }}" method="POST" class="space-y-4">
    @csrf

    {{-- Nama --}}
    <div>
        <label class="block text-sm font-medium text-stone-700 mb-1.5" for="name">
            Nama Lengkap
        </label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name') }}"
            autocomplete="name"
            placeholder="Nama lengkap kamu"
            class="w-full rounded-lg border px-3.5 py-2.5 text-sm outline-none transition-colors
                   {{ $errors->has('name') ? 'border-red-300 bg-red-50' : 'border-stone-200 bg-white' }}
                   focus:border-stone-900"
        />
        @error('name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

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
            class="w-full rounded-lg border px-3.5 py-2.5 text-sm outline-none transition-colors
                   {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-stone-200 bg-white' }}
                   focus:border-stone-900"
        />
        @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div>
        <label class="block text-sm font-medium text-stone-700 mb-1.5" for="password">
            Password
        </label>
        <input
            type="password"
            id="password"
            name="password"
            autocomplete="new-password"
            placeholder="Minimal 8 karakter"
            class="w-full rounded-lg border px-3.5 py-2.5 text-sm outline-none transition-colors
                   {{ $errors->has('password') ? 'border-red-300 bg-red-50' : 'border-stone-200 bg-white' }}
                   focus:border-stone-900"
        />
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Konfirmasi Password --}}
    <div>
        <label class="block text-sm font-medium text-stone-700 mb-1.5" for="password_confirmation">
            Konfirmasi Password
        </label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            autocomplete="new-password"
            placeholder="Ulangi password kamu"
            class="w-full rounded-lg border border-stone-200 bg-white px-3.5 py-2.5
                   text-sm outline-none transition-colors focus:border-stone-900"
        />
    </div>

    {{-- Submit --}}
    <button type="submit"
        class="w-full rounded-lg bg-stone-900 py-2.5 text-sm font-medium text-amber-200
               transition-colors hover:bg-stone-800 active:scale-[0.99] mt-2">
        Daftar Sekarang
    </button>
</form>

<p class="mt-6 text-center text-sm text-stone-400">
    Sudah punya akun?
    <a href="{{ route('auth.login') }}"
       class="font-medium text-stone-900 hover:underline">
        Masuk di sini
    </a>
</p>

@endsection
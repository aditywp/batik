@extends('layouts.admin')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-[#1a1a2e]">Dashboard Overview</h1>
        <p class="text-gray-500 text-sm">Selamat datang kembali, Admin Batik Ifawati.</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Kartu Total Pesanan -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-6 hover:shadow-md transition-shadow">
            <div class="bg-blue-100 p-4 rounded-xl">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Pesanan</p>
                <h2 class="text-3xl font-bold text-[#1a1a2e]">{{ $totalOrders }}</h2>
            </div>
        </div>

        <!-- Kartu Total Produk -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-6 hover:shadow-md transition-shadow">
            <div class="bg-emerald-100 p-4 rounded-xl">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Produk Batik</p>
                <h2 class="text-3xl font-bold text-[#1a1a2e]">{{ $totalProducts }}</h2>
            </div>
        </div>

        <!-- Kartu Total Pelanggan -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-6 hover:shadow-md transition-shadow">
            <div class="bg-purple-100 p-4 rounded-xl">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Pelanggan</p>
                <h2 class="text-3xl font-bold text-[#1a1a2e]">{{ $totalCustomers }}</h2>
            </div>
        </div>
    </div>

    {{-- Secondary Section (Optional: Order Terbaru) --}}
    <div class="mt-12">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50">
                <h3 class="font-bold text-[#1a1a2e]">Aktivitas Terakhir</h3>
            </div>
            <div class="p-12 text-center">
                <p class="text-gray-400 italic">Belum ada aktivitas pesanan baru hari ini.</p>
            </div>
        </div>
    </div>
</div>
@endsection
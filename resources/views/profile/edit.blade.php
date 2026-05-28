@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-[#f8f9fa] pt-32 pb-20 font-sans">
    <div class="max-w-4xl mx-auto px-6">
        
        {{-- HEADER HALAMAN PROFIL (PRESTIGE STYLE) --}}
        <div class="mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-4xl font-black text-[#1a1a2e] italic tracking-tighter uppercase leading-none">
                    Account Settings
                </h1>
                <p class="text-gray-400 text-[10px] uppercase tracking-[3px] mt-3 font-bold italic">
                    Manage your credentials and security preference
                </p>
            </div>
            
            {{-- BREADCRUMB NAVIGATION BACK LINK --}}
            <a href="{{ route('customer.home') }}" 
               class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-orange-600 transition-all flex items-center gap-2 border-b border-transparent hover:border-orange-600 pb-1 w-fit">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        {{-- GRID UTAMA WRAPPER DATA INPUT --}}
        <div class="space-y-6">
            
            {{-- SEKSI 1: UPDATE PROFILE INFORMATION --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-md">
                <div class="p-6 sm:p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- SEKSI 2: UPDATE PASSWORD SECURE --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-md">
                <div class="p-6 sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- SEKSI 3: DANGER ZONE - DELETE ACCOUNT --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-red-100/60 overflow-hidden transition-all duration-300 hover:shadow-md border-dashed">
                <div class="p-6 sm:p-8 bg-red-50/5">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>

        {{-- DEKORASI LOGO BAWAH --}}
        <div class="mt-12 text-center">
            <p class="text-[9px] font-black text-stone-300 uppercase tracking-[4px]">
                Batik Ifawati Management System &copy; 2026
            </p>
        </div>
    </div>
</div>

<style>
    /* Pasang reset overriding style agar partials form di dalamnya bisa beradaptasi sempurna */
    section {
        max-width: 100% !important;
        padding: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        margin-top: 0 !important;
    }
    
    /* Halus scrollbar bertema premium orange */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #ea580c; }
</style>
@endsection
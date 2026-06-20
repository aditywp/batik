@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.vouchers.index') }}" class="text-gray-400 hover:text-gray-800">← Kembali</a>
        <h2 class="text-xl font-bold text-gray-800">Tambah Voucher Baru</h2>
    </div>

    <form action="{{ route('admin.vouchers.store') }}" method="POST" class="space-y-5">
        @csrf
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Voucher <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Contoh: Diskon Kemerdekaan (Maks 100 Karakter)" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-black focus:outline-none">
            @error('name') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Voucher <span class="text-red-500">*</span></label>
            <input type="text" name="code" value="{{ old('code') }}" required minlength="3" maxlength="30" 
                   oninput="this.value = this.value.replace(/\s+/g, '').toUpperCase();" 
                   placeholder="Contoh: MERDEKA20 (Maks 30 Karakter)" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 uppercase focus:ring-2 focus:ring-black focus:outline-none">
            @error('code') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Potongan <span class="text-red-500">*</span></label>
                <div class="relative">
                    {{-- Tulisan Rp Statis di Kiri --}}
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-gray-500 font-bold text-sm">Rp</span>
                    </div>
                    
                    {{-- Input Teks yang Terlihat (Untuk ngetik dengan titik) --}}
                    <input type="text" id="discount_visible" required placeholder="Contoh: 50.000" 
                           class="w-full border border-gray-300 rounded-lg pl-11 pr-4 py-2.5 focus:ring-2 focus:ring-black focus:outline-none">
                    
                    {{-- Input Hidden yang Dikirim ke Database Laravel (Tanpa titik) --}}
                    <input type="hidden" name="discount_amount" id="discount_hidden" value="{{ old('discount_amount') }}">
                </div>
                @error('discount_amount') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Syarat Poin (Isi 0 jika gratis) <span class="text-red-500">*</span> </label>
                {{-- PERBAIKAN: onkeypress untuk tolak karakter minus (-), huruf, dll --}}
                <input type="number" name="points_required" value="{{ old('points_required', 0) }}" required min="0" max="10000" 
                       onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                       oninput="if(this.value !== '' && parseInt(this.value) > 10000) this.value = 10000;" 
                       placeholder="Maks: 10.000" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-black focus:outline-none">
                @error('points_required') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai Tanggal (Opsional)</label>
            <input type="date" name="valid_until" value="{{ old('valid_until') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-black focus:outline-none">
            @error('valid_until') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
        </div>

        <div class="flex flex-col gap-3 pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_welcome_voucher" value="1" {{ old('is_welcome_voucher') ? 'checked' : '' }} class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                <span class="text-sm font-medium text-gray-700">Jadikan Welcome Voucher (Otomatis dibagikan untuk pendaftar baru)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                <span class="text-sm font-medium text-gray-700">Aktifkan Voucher Ini</span>
            </label>
        </div>

        <div class="pt-4 border-t border-gray-100">
            <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-bold hover:bg-gray-800 transition-colors">Simpan Voucher</button>
        </div>
    </form>
</div>

{{-- SCRIPT UNTUK MASKING INPUT RUPIAH --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const visibleInput = document.getElementById('discount_visible');
    const hiddenInput = document.getElementById('discount_hidden');

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    if (hiddenInput.value) {
        visibleInput.value = formatNumber(hiddenInput.value);
    }

    visibleInput.addEventListener('input', function(e) {
        let rawValue = this.value.replace(/[^0-9]/g, '');

        if (rawValue.length > 7) {
            rawValue = rawValue.slice(0, 7);
        }

        hiddenInput.value = rawValue;
        this.value = formatNumber(rawValue);
    });
});
</script>
@endsection
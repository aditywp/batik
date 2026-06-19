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
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Voucher</label>
            <input type="text" name="name" required placeholder="Contoh: Diskon Kemerdekaan" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-black focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Voucher</label>
            <input type="text" name="code" required placeholder="Contoh: MERDEKA20" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 uppercase focus:ring-2 focus:ring-black focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Potongan (Rp)</label>
                <input type="number" name="discount_amount" required min="0" placeholder="20000" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-black focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Syarat Poin (Isi 0 jika gratis)</label>
                <input type="number" name="points_required" required min="0" value="0" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-black focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai Tanggal (Opsional)</label>
            <input type="date" name="valid_until" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-black focus:outline-none">
        </div>

        <div class="flex flex-col gap-3 pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_welcome_voucher" value="1" class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                <span class="text-sm font-medium text-gray-700">Jadikan Welcome Voucher (Otomatis dibagikan untuk pendaftar baru)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                <span class="text-sm font-medium text-gray-700">Aktifkan Voucher Ini</span>
            </label>
        </div>

        <div class="pt-4 border-t border-gray-100">
            <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-bold hover:bg-gray-800 transition-colors">Simpan Voucher</button>
        </div>
    </form>
</div>
@endsection
@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.vouchers.index') }}" class="text-gray-400 hover:text-gray-800">← Kembali</a>
        <h2 class="text-xl font-bold text-gray-800">Edit Voucher</h2>
    </div>

    <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Voucher</label>
            <input type="text" name="name" value="{{ $voucher->name }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-black focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Voucher</label>
            <input type="text" name="code" value="{{ $voucher->code }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 uppercase focus:ring-2 focus:ring-black focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Potongan (Rp)</label>
                <input type="number" name="discount_amount" value="{{ floatval($voucher->discount_amount) }}" required min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-black focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Syarat Poin</label>
                <input type="number" name="points_required" value="{{ $voucher->points_required }}" required min="0" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-black focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai Tanggal</label>
            <input type="date" name="valid_until" value="{{ $voucher->valid_until ? \Carbon\Carbon::parse($voucher->valid_until)->format('Y-m-d') : '' }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-black focus:outline-none">
        </div>

        <div class="flex flex-col gap-3 pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_welcome_voucher" value="1" {{ $voucher->is_welcome_voucher ? 'checked' : '' }} class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                <span class="text-sm font-medium text-gray-700">Jadikan Welcome Voucher (Otomatis dibagikan untuk pendaftar baru)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $voucher->is_active ? 'checked' : '' }} class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                <span class="text-sm font-medium text-gray-700">Aktifkan Voucher Ini</span>
            </label>
        </div>

        <div class="pt-4 border-t border-gray-100">
            <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-bold hover:bg-gray-800 transition-colors">Perbarui Voucher</button>
        </div>
    </form>
</div>
@endsection
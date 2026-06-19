@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-lg shadow-sm border border-gray-100 font-sans">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800 uppercase italic tracking-tighter">Manajemen Voucher</h2>
        <a href="{{ route('admin.vouchers.create') }}" class="bg-black hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all">
            + Tambah Voucher
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs font-black text-gray-400 uppercase tracking-widest">
                    <th class="p-4">Nama Voucher</th>
                    <th class="p-4">Kode</th>
                    <th class="p-4">Potongan</th>
                    <th class="p-4">Syarat Poin</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($vouchers as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 text-gray-800 font-bold">
                        {{ $item->name }}
                        @if($item->is_welcome_voucher)
                            <span class="ml-2 bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wide">Welcome</span>
                        @endif
                    </td>
                    <td class="p-4"><span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg font-mono text-xs border border-gray-200 font-bold">{{ $item->code }}</span></td>
                    <td class="p-4 text-green-600 font-black italic">Rp {{ number_format($item->discount_amount, 0, ',', '.') }}</td>
                    <td class="p-4 text-blue-600 font-black">{{ $item->points_required }} Poin</td>
                    <td class="p-4">
                        @if($item->is_active)
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Aktif</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Nonaktif</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.vouchers.edit', $item->id) }}" class="bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-blue-100 transition-colors">Edit</a>
                            
                            {{-- Form Hapus dengan Class btn-delete --}}
                            <form action="{{ route('admin.vouchers.destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-delete bg-red-50 text-red-600 px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-red-100 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-400 italic">Belum ada voucher yang ditambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- SCRIPT SWEETALERT2 --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    // 1. Konfirmasi Hapus
    $('.btn-delete').on('click', function (e) {
        e.preventDefault();
        let form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus Voucher?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1a1a2e',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: '<span style="color: #e8c9a0; font-weight: 900; text-transform: uppercase; font-size: 11px;">Ya, Hapus</span>',
            cancelButtonText: '<span style="color: #4b5563; font-weight: 900; text-transform: uppercase; font-size: 11px;">Batal</span>',
            customClass: {
                popup: 'rounded-[32px]',
                title: 'font-sans font-black text-[#1a1a2e] uppercase italic',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // 2. Flash Success
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'BERHASIL',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            background: '#1a1a2e',
            iconColor: '#e8c9a0',
            customClass: {
                title: 'font-sans font-black text-[#e8c9a0] italic',
                htmlContainer: 'font-sans text-xs font-bold text-white'
            }
        });
    @endif

    // 3. Flash Error
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'GAGAL',
            text: "{{ session('error') }}",
            timer: 3000,
            showConfirmButton: false,
            customClass: {
                title: 'font-sans font-black text-red-600 italic',
            }
        });
    @endif
});
</script>
@endsection
@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6 font-sans">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-fit">
            <div class="bg-[#1a1a2e] p-5">
                <h2 class="text-[#e8c9a0] font-bold text-sm uppercase tracking-wider">Tambah Kategori</h2>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="p-5">
                @csrf
                <div class="mb-5">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Kategori</label>
                    <input type="text" name="name" class="block w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-[#1a1a2e] focus:border-[#1a1a2e] h-11 px-4 placeholder:text-gray-300" placeholder="Contoh: Batik Tulis" required>
                </div>
                <button type="submit" class="w-full bg-[#e8c9a0] text-[#1a1a2e] py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#d4b78d] transition shadow-sm active:scale-95">
                    Simpan Kategori
                </button>
            </form>
        </div>

        {{-- Tabel Daftar Kategori --}}
        <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-[#1a1a2e] p-5 flex justify-between items-center">
                <h2 class="text-[#e8c9a0] font-bold text-sm uppercase tracking-wider">Daftar Kategori Batik</h2>
                {{-- Menampilkan total data kategori --}}
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 bg-white/5 px-3 py-1 rounded-full">Total: {{ $categories->total() }} Data</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="p-5">Nama</th>
                            <th class="p-5">Slug</th>
                            <th class="p-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @forelse($categories as $category)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="p-5 text-gray-800 font-bold">{{ $category->name }}</td>
                            <td class="p-5 text-gray-400 font-mono text-xs">{{ $category->slug }}</td>
                            <td class="p-5 text-center">
                                {{-- PERBAIKAN: Mengganti onsubmit bawaan menjadi pemicu class SweetAlert2 --}}
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="delete-form inline">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="button" class="btn-delete text-red-400 hover:text-red-600 font-black text-xs uppercase tracking-widest transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-16 text-center text-gray-400 italic">Belum ada kategori batik.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- NAVIGASI HALAMAN (PAGINATION) --}}
            @if($categories->hasPages())
            <div class="p-5 bg-gray-50/50 border-t border-gray-100">
                {{ $categories->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- SCRIPT JQUERY & SWEETALERT2 ENGINE --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    
    // 1. INTERSEPSI CLICK TOMBOL HAPUS UNTUK MEMICU SWEETALERT2 PRESTISIUS
    $('.btn-delete').on('click', function (e) {
        e.preventDefault();
        let form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus Kategori?',
            text: "Kategori yang dihapus dapat mempengaruhi relasi data produk batik terkait!",
            icon: 'warning',
            showCancelButton: true,
            background: '#ffffff',
            confirmButtonColor: '#1a1a2e', // Warna Navy Utama Tokomu
            cancelButtonColor: '#f3f4f6',  // Abu-abu netral minimalis
            confirmButtonText: '<span style="color: #e8c9a0; font-weight: 900; text-transform: uppercase; font-size: 11px; letter-spacing: 1px;">Ya, Hapus</span>',
            cancelButtonText: '<span style="color: #4b5563; font-weight: 900; text-transform: uppercase; font-size: 11px; letter-spacing: 1px;">Batal</span>',
            customClass: {
                popup: 'rounded-[32px]', // Menyamakan kelengkungan border UI tokomu
                title: 'font-sans font-black text-[#1a1a2e] uppercase italic tracking-tight',
                htmlContainer: 'font-sans text-xs font-medium text-gray-400'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Kirim form DELETE jika disetujui
            }
        });
    });

    // 2. TOAST NOTIFIKASI OTOMATIS JIKA PROSES LARAVEL BERHASIL (SUCCESS / ERROR FLASH)
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
                popup: 'rounded-[24px]',
                title: 'font-sans font-black text-[#e8c9a0] italic',
                htmlContainer: 'font-sans text-xs font-bold text-white'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'GAGAL',
            text: "{{ session('error') }}",
            timer: 4000,
            showConfirmButton: false,
            background: '#ffffff',
            confirmButtonColor: '#1a1a2e',
            customClass: {
                popup: 'rounded-[24px]',
                title: 'font-sans font-black text-red-600 italic',
                htmlContainer: 'font-sans text-xs font-bold text-gray-400'
            }
        });
    @endif
});
</script>
@endsection
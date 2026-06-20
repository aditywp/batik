@extends('layouts.admin')

@section('content')
{{-- Membungkus seluruh konten dengan Alpine.js data untuk mengatur Modal Edit --}}
<div class="container mx-auto p-6 font-sans" x-data="{ 
    editModalOpen: false, 
    editId: '', 
    editName: '', 
    editAction: '' 
}">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- PANEL KIRI: Form Tambah Kategori --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-fit">
            <div class="bg-[#1a1a2e] p-5">
                <h2 class="text-[#e8c9a0] font-bold text-sm uppercase tracking-wider">Tambah Kategori</h2>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="p-5">
                @csrf
                <div class="mb-5">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Kategori</label>
                    <input type="text" name="name" class="block w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-[#1a1a2e] focus:border-[#1a1a2e] h-11 px-4 placeholder:text-gray-300 transition-all" placeholder="Contoh: Batik Tulis" required>
                    @error('name') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full bg-[#e8c9a0] text-[#1a1a2e] py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#d4b78d] transition shadow-sm active:scale-95">
                    Simpan Kategori
                </button>
            </form>
        </div>

        {{-- PANEL KANAN: Tabel Daftar Kategori & Fitur Pencarian --}}
        <div class="md:col-span-2 space-y-4 flex flex-col h-full">
            
            {{-- Form Pencarian --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-wrap gap-3 items-center justify-between">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="flex w-full md:w-auto gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..." 
                           class="bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-[#1a1a2e] focus:border-[#1a1a2e] h-11 px-4 w-full md:w-64 transition-all">
                    <button type="submit" class="bg-[#1a1a2e] text-white px-6 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all shadow-sm active:scale-95">
                        Cari
                    </button>
                    @if(request()->filled('search'))
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-center bg-red-50 text-red-500 px-5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-red-100 transition-all">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Tabel Kategori --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex-1 flex flex-col">
                <div class="bg-[#1a1a2e] p-5 flex justify-between items-center">
                    <h2 class="text-[#e8c9a0] font-bold text-sm uppercase tracking-wider">Daftar Kategori Batik</h2>
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 bg-white/5 px-3 py-1 rounded-full">Total: {{ $categories->total() }} Data</span>
                </div>
                
                <div class="overflow-x-auto flex-1">
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
                                    <div class="flex justify-center items-center gap-4">
                                        {{-- Tombol Edit (Memanggil Alpine Modal) --}}
                                        <button type="button" 
                                                @click="editModalOpen = true; editId = '{{ $category->id }}'; editName = '{{ $category->name }}'; editAction = '{{ route('admin.categories.update', $category->id) }}'" 
                                                class="text-blue-500 hover:text-blue-700 font-black text-xs uppercase tracking-widest transition-colors">
                                            Edit
                                        </button>

                                        {{-- Form Hapus --}}
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="delete-form inline">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="button" class="btn-delete text-red-400 hover:text-red-600 font-black text-xs uppercase tracking-widest transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-16 text-center text-gray-400 italic">
                                    {{ request('search') ? 'Kategori yang dicari tidak ditemukan.' : 'Belum ada kategori batik.' }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- UI Pagination Premium Kustomisasi Sempurna --}}
                @if($categories->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-4 mt-auto">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Menampilkan <span class="text-[#1a1a2e] font-black">{{ $categories->firstItem() }}</span> - <span class="text-[#1a1a2e] font-black">{{ $categories->lastItem() }}</span> dari <span class="text-[#1a1a2e] font-black">{{ $categories->total() }}</span> Kategori
                    </div>
                    <div class="flex items-center gap-1">
                        {{-- Tombol Previous --}}
                        @if ($categories->onFirstPage())
                            <span class="px-3 py-2 bg-gray-100 text-gray-300 text-xs font-black rounded-xl uppercase tracking-widest cursor-not-allowed">Prev</span>
                        @else
                            <a href="{{ $categories->appends(request()->query())->previousPageUrl() }}" class="px-3 py-2 bg-white border border-gray-200 text-gray-600 hover:border-[#1a1a2e] hover:text-[#1a1a2e] text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-sm">Prev</a>
                        @endif

                        {{-- Nomor Halaman Dinamis --}}
                        <div class="hidden sm:flex items-center gap-1">
                            @foreach ($categories->getUrlRange(max(1, $categories->currentPage() - 2), min($categories->lastPage(), $categories->currentPage() + 2)) as $page => $url)
                                <a href="{{ $url . '&' . http_build_query(request()->except('page')) }}" 
                                   class="w-9 h-9 flex items-center justify-center rounded-xl text-xs font-bold transition-all border {{ $page == $categories->currentPage() ? 'bg-[#1a1a2e] text-[#e8c9a0] border-[#1a1a2e] font-black shadow-md shadow-[#1a1a2e]/10' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-400' }}">
                                    {{ $page }}
                                </a>
                            @endforeach
                        </div>

                        {{-- Tombol Next --}}
                        @if ($categories->hasMorePages())
                            <a href="{{ $categories->appends(request()->query())->nextPageUrl() }}" class="px-3 py-2 bg-white border border-gray-200 text-gray-600 hover:border-[#1a1a2e] hover:text-[#1a1a2e] text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-sm">Next</a>
                        @else
                            <span class="px-3 py-2 bg-gray-100 text-gray-300 text-xs font-black rounded-xl uppercase tracking-widest cursor-not-allowed">Next</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL EDIT KATEGORI (ALPINE JS) --}}
    <div x-show="editModalOpen" x-transition x-cloak class="fixed inset-0 z-[99] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div @click.away="editModalOpen = false" class="bg-white rounded-[32px] w-full max-w-md shadow-2xl overflow-hidden">
            <div class="bg-[#1a1a2e] p-6 flex justify-between items-center border-b border-white/10">
                <h2 class="text-[#e8c9a0] font-bold text-lg tracking-tight">Edit Kategori</h2>
                <button @click="editModalOpen = false" class="p-2 bg-white/5 rounded-full hover:bg-white/20 transition-colors">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form :action="editAction" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="mb-8">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Kategori Baru</label>
                    <input type="text" name="name" x-model="editName" class="block w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-[#1a1a2e] focus:border-[#1a1a2e] h-12 px-4 transition-all" required>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="editModalOpen = false" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-gray-200 transition-colors">Batal</button>
                    <button type="submit" class="px-6 py-3 bg-[#e8c9a0] text-[#1a1a2e] rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#d4b78d] transition-all shadow-sm active:scale-95">Update</button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- CSS KHUSUS --}}
<style>
    [x-cloak] { display: none !important; }
</style>

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
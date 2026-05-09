@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Form Tambah Kategori -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden h-fit">
            <div class="bg-[#1a1a2e] p-4">
                <h2 class="text-[#e8c9a0] font-semibold">Tambah Kategori</h2>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="p-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                    <input type="text" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]" placeholder="Contoh: Batik Tulis" required>
                </div>
                <button type="submit" class="w-full bg-[#e8c9a0] text-[#1a1a2e] py-2 rounded-md font-bold hover:bg-[#d4b78d] transition">
                    Simpan Kategori
                </button>
            </form>
        </div>

        {{-- Tabel Daftar Kategori --}}
        <div class="md:col-span-2 bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-[#1a1a2e] p-4 flex justify-between items-center">
                <h2 class="text-[#e8c9a0] font-semibold">Daftar Kategori Batik</h2>
                {{-- Menampilkan total data kategori --}}
                <span class="text-xs text-stone-400">Total: {{ $categories->total() }} Data</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4 text-sm font-semibold text-gray-600">Nama</th>
                            <th class="p-4 text-sm font-semibold text-gray-600">Slug</th>
                            <th class="p-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4 text-gray-800 font-medium">{{ $category->name }}</td>
                            <td class="p-4 text-gray-500 text-sm">{{ $category->slug }}</td>
                            <td class="p-4 text-center">
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-400 italic">Belum ada kategori batik.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- NAVIGASI HALAMAN (PAGINATION) --}}
            <div class="p-4 bg-gray-50 border-t">
                {{ $categories->links() }}
            </div>
        </div>
</div>
@endsection
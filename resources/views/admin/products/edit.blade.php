@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
        {{-- Header Form --}}
        <div class="bg-[#1a1a2e] p-6 flex justify-between items-center">
            <h2 class="text-[#e8c9a0] font-bold text-xl">Edit Produk Batik</h2>
            <span class="text-[#e8c9a0]/50 text-sm">ID: #{{ $product->id }}</span>
        </div>
        
        {{-- Form Start --}}
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            {{-- 1. INFORMASI DASAR --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                    <select name="category_id" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Harga Utama (Rp)</label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]" required>
                <p class="text-[10px] text-gray-400 mt-2 italic">*Harga ini digunakan jika variasi ukuran tidak memiliki harga khusus.</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <hr class="border-gray-100">

            {{-- 2. BAGIAN VARIASI BERLEVEL (MOTIF > UKURAN) --}}
            {{-- Mengambil data motif unik dari tabel variants --}}
            @php
                $groupedVariants = $product->variants->groupBy('motif');
            @endphp

            <div x-data="{ 
                motifs: [
                    @foreach($groupedVariants as $motifName => $variants)
                    { 
                        name: '{{ $motifName }}', 
                        existing_image: '{{ $variants->first()->image_path }}',
                        sizes: [
                            @foreach($variants as $v)
                            { size: '{{ $v->size }}', price: '{{ $v->price }}', stock: {{ $v->stock }} },
                            @endforeach
                        ]
                    },
                    @endforeach
                    @if($groupedVariants->isEmpty())
                    { name: '', sizes: [{ size: '', price: '', stock: 0 }] }
                    @endif
                ] 
            }">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg text-[#1a1a2e]">Variasi Motif & Ukuran</h3>
                    <button type="button" @click="motifs.push({ name: '', sizes: [{ size: '', price: '', stock: 0 }] })"
                            class="bg-[#1a1a2e] text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-black transition-all">
                        + Tambah Motif Baru
                    </button>
                </div>
                
                <div class="space-y-8">
                    <template x-for="(motif, mIndex) in motifs" :key="mIndex">
                        <div class="p-6 bg-gray-50 rounded-[32px] border border-gray-200 relative">
                            {{-- Tombol Hapus Motif --}}
                            <button type="button" @click="motifs.splice(mIndex, 1)" x-show="motifs.length > 1"
                                    class="absolute top-6 right-6 text-red-400 hover:text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Motif</label>
                                    <input type="text" :name="`motifs[${mIndex}][name]`" x-model="motif.name" 
                                           class="mt-2 block w-full border-gray-200 rounded-xl text-sm shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]" placeholder="Contoh: Parang Biru">
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Foto Motif</label>
                                    <div class="flex items-center gap-4 mt-2">
                                        <template x-if="motif.existing_image">
                                            <img :src="'/storage/' + motif.existing_image" class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                                        </template>
                                        <input type="file" :name="`motifs[${mIndex}][image]`" 
                                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-white file:text-[#1a1a2e] file:font-bold border border-gray-200 rounded-xl bg-white p-1">
                                    </div>
                                    <p x-show="motif.existing_image" class="text-[9px] text-gray-400 mt-1 italic">*Kosongkan jika tidak ingin mengganti foto motif.</p>
                                </div>
                            </div>

                            {{-- LEVEL 2: UKURAN DI DALAM MOTIF --}}
                            <div class="bg-white p-6 rounded-2xl border border-gray-100">
                                <div class="flex justify-between items-center mb-4">
                                    <p class="text-xs font-bold text-gray-500 italic">Daftar Ukuran untuk motif ini:</p>
                                    <button type="button" @click="motif.sizes.push({ size: '', price: '', stock: 0 })"
                                            class="text-[10px] bg-blue-50 text-blue-600 px-3 py-1 rounded-lg font-bold hover:bg-blue-100">
                                        + Tambah Ukuran
                                    </button>
                                </div>

                                <div class="space-y-3">
                                    <template x-for="(sItem, sIndex) in motif.sizes" :key="sIndex">
                                        <div class="grid grid-cols-4 gap-4 items-end bg-gray-50/50 p-3 rounded-xl border border-dashed border-gray-200">
                                            <div>
                                                <label class="text-[9px] text-gray-400 uppercase">Size</label>
                                                <select :name="`motifs[${mIndex}][sizes][${sIndex}][size]`" x-model="sItem.size" class="w-full rounded-lg border-gray-200 text-xs">
                                                    <option value="">N/A</option>
                                                    <option value="S">S</option><option value="M">M</option><option value="L">L</option><option value="XL">XL</option><option value="XXL">XXL</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[9px] text-gray-400 uppercase">Harga Khusus</label>
                                                <input type="number" :name="`motifs[${mIndex}][sizes][${sIndex}][price]`" x-model="sItem.price" class="w-full rounded-lg border-gray-200 text-xs" placeholder="Sama dg utama">
                                            </div>
                                            <div>
                                                <label class="text-[9px] text-gray-400 uppercase">Stok</label>
                                                <input type="number" :name="`motifs[${mIndex}][sizes][${sIndex}][stock]`" x-model="sItem.stock" class="w-full rounded-lg border-gray-200 text-xs" required>
                                            </div>
                                            <button type="button" @click="motif.sizes.splice(sIndex, 1)" x-show="motif.sizes.length > 1" class="text-red-400 p-2 hover:text-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                @error('motifs') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <hr class="border-gray-100">

            {{-- 3. FOTO GALERI TAMBAHAN --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-4">Galeri Foto Produk (Opsional)</label>
                
                {{-- Preview Foto Galeri Saat Ini --}}
                @if($product->images->count() > 0)
                <div id="current-images-grid" class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                    @foreach($product->images as $image)
                        <div id="image-container-{{ $image->id }}" class="relative group aspect-square bg-white rounded-2xl border p-1 shadow-sm hover:border-red-300 transition-all">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-full object-cover rounded-xl">
                            
                            {{-- Tombol Hapus AJAX --}}
                            <button type="button" 
                                    onclick="deleteImage({{ $image->id }})"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                @endif

                <input type="file" name="images[]" multiple class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-stone-100 file:text-stone-700 font-medium border border-dashed border-gray-300 p-4 rounded-2xl">
                <p class="text-[10px] text-gray-400 mt-2 italic">*Foto baru akan ditambahkan ke galeri produk.</p>
            </div>

            {{-- BUTTON AKSI --}}
            <div class="flex justify-end gap-4 mt-12 border-t pt-8">
                <a href="{{ route('admin.products.index') }}" class="px-8 py-3 bg-gray-100 rounded-2xl text-gray-600 font-bold hover:bg-gray-200 transition-all">Batal</a>
                <button type="submit" class="px-10 py-3 bg-[#e8c9a0] text-[#1a1a2e] rounded-2xl font-extrabold hover:bg-[#d4b78d] transition-all shadow-xl shadow-[#e8c9a0]/20">
                    Update Produk
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SweetAlert & Ajax Script tetap dipertahankan untuk Galeri --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteImage(imageId) {
        Swal.fire({
            title: 'Hapus foto galeri?',
            text: "Foto akan dihapus permanen dari server!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/product-images/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`image-container-${imageId}`).remove();
                        Swal.fire('Terhapus!', data.message, 'success');
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                });
            }
        })
    }
</script>
@endsection
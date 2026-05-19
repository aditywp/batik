@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-5xl mx-auto bg-white rounded-[40px] shadow-xl overflow-hidden border border-gray-100">
        {{-- Header Form --}}
        <div class="bg-[#1a1a2e] p-8 flex justify-between items-center">
            <div>
                <h2 class="text-[#e8c9a0] font-bold text-2xl tracking-tight">Edit Produk Batik</h2>
                <p class="text-gray-400 text-xs mt-1">Perbarui informasi stok dan detail variasi produk.</p>
            </div>
            <span class="bg-[#e8c9a0]/10 text-[#e8c9a0] px-4 py-2 rounded-2xl text-xs font-bold border border-[#e8c9a0]/20">ID: #{{ $product->id }}</span>
        </div>
        
        {{-- Form Start --}}
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-8 lg:p-12 space-y-10">
            @csrf
            @method('PUT')

            {{-- LOGIKA REDIRECT: Menyimpan URL asal (termasuk filter & halaman) --}}
            <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">

            {{-- 1. INFORMASI DASAR --}}
            <div class="space-y-6">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-6">01. Informasi Dasar</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2 ml-1">Nama Produk</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" 
                               class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3.5 px-5 focus:border-[#e8c9a0] focus:ring-[#e8c9a0] transition-all" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2 ml-1">Kategori Utama</label>
                        <select name="category_id" class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3.5 px-5 focus:border-[#e8c9a0] focus:ring-[#e8c9a0] transition-all" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2 ml-1">Harga Utama (Rp)</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" 
                               class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3.5 px-5 focus:border-[#e8c9a0] focus:ring-[#e8c9a0] transition-all" required>
                        <p class="text-[10px] text-gray-400 mt-2 italic ml-1">*Harga dasar jika varian tidak memiliki harga khusus.</p>
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2 ml-1">Target Koleksi (Collection)</label>
                        <select name="collection" class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3.5 px-5 focus:border-[#e8c9a0] focus:ring-[#e8c9a0] transition-all" required>
                            <option value="Women" {{ old('collection', $product->collection) == 'Women' ? 'selected' : '' }}>Women</option>
                            <option value="Men" {{ old('collection', $product->collection) == 'Men' ? 'selected' : '' }}>Men</option>
                            <option value="Craft" {{ old('collection', $product->collection) == 'Craft' ? 'selected' : '' }}>Craft</option>
                            <option value="Family" {{ old('collection', $product->collection) == 'Family' ? 'selected' : '' }}>Family</option>
                        </select>
                        @error('collection') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2 ml-1">Deskripsi</label>
                    <textarea name="description" rows="4" 
                              class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3.5 px-5 focus:border-[#e8c9a0] focus:ring-[#e8c9a0] transition-all" required>{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- 2. BAGIAN VARIASI BERLEVEL --}}
            @php $groupedVariants = $product->variants->groupBy('motif'); @endphp

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
                ] 
            }">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em]">02. Variasi Motif & Ukuran</h3>
                    <button type="button" @click="motifs.push({ name: '', sizes: [{ size: '', price: '', stock: 0 }] })"
                            class="bg-[#1a1a2e] text-white px-5 py-2.5 rounded-2xl text-xs font-bold hover:bg-black transition-all shadow-lg shadow-gray-200">
                        + Tambah Motif Baru
                    </button>
                </div>
                
                <div class="space-y-10">
                    <template x-for="(motif, mIndex) in motifs" :key="mIndex">
                        <div class="p-8 bg-gray-50/50 rounded-[40px] border border-gray-100 relative shadow-sm">
                            <button type="button" @click="motifs.splice(mIndex, 1)" x-show="motifs.length > 1"
                                    class="absolute top-8 right-8 text-red-300 hover:text-red-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Motif</label>
                                    <input type="text" :name="`motifs[${mIndex}][name]`" x-model="motif.name" 
                                           class="mt-2 block w-full bg-white border-gray-200 rounded-2xl py-3 px-5 text-sm shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Foto Motif</label>
                                    <div class="flex items-center gap-4 mt-2">
                                        <template x-if="motif.existing_image">
                                            <div class="relative group">
                                                <img :src="'/storage/' + motif.existing_image" class="w-14 h-14 object-cover rounded-2xl border-2 border-white shadow-md">
                                                <div class="absolute inset-0 bg-black/40 rounded-2xl opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                                    <span class="text-[8px] text-white font-bold">Old</span>
                                                </div>
                                            </div>
                                        </template>
                                        <input type="file" :name="`motifs[${mIndex}][image]`" 
                                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-5 file:rounded-full file:border-0 file:bg-[#1a1a2e] file:text-white file:font-bold border border-gray-200 rounded-2xl bg-white p-1.5">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-[30px] border border-gray-100 shadow-inner">
                                <div class="flex justify-between items-center mb-6">
                                    <p class="text-xs font-bold text-gray-500 italic ml-2">Daftar Ukuran & Stok:</p>
                                    <button type="button" @click="motif.sizes.push({ size: '', price: '', stock: 0 })"
                                            class="text-[10px] bg-blue-50 text-blue-600 px-4 py-2 rounded-xl font-bold hover:bg-blue-100 transition-colors">
                                        + Tambah Ukuran
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <template x-for="(sItem, sIndex) in motif.sizes" :key="sIndex">
                                        <div class="grid grid-cols-4 gap-4 items-end bg-gray-50/50 p-4 rounded-2xl border border-dashed border-gray-200">
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-1 block ml-1">Size</label>
                                                <select :name="`motifs[${mIndex}][sizes][${sIndex}][size]`" x-model="sItem.size" class="w-full rounded-xl border-gray-200 text-xs focus:ring-[#e8c9a0]">
                                                    <option value="">N/A</option>
                                                    <option value="S">S</option><option value="M">M</option><option value="L">L</option><option value="XL">XL</option><option value="XXL">XXL</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-1 block ml-1">Harga Khusus</label>
                                                <input type="number" :name="`motifs[${mIndex}][sizes][${sIndex}][price]`" x-model="sItem.price" class="w-full rounded-xl border-gray-200 text-xs focus:ring-[#e8c9a0]">
                                            </div>
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-1 block ml-1">Stok</label>
                                                <input type="number" :name="`motifs[${mIndex}][sizes][${sIndex}][stock]`" x-model="sItem.stock" class="w-full rounded-xl border-gray-200 text-xs focus:ring-[#e8c9a0]" required>
                                            </div>
                                            <div class="flex justify-center">
                                                <button type="button" @click="motif.sizes.splice(sIndex, 1)" x-show="motif.sizes.length > 1" class="text-red-300 p-2 hover:text-red-600 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- 3. FOTO GALERI --}}
            <div class="space-y-6">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em]">03. Galeri Foto Produk</h3>
                
                @if($product->images->count() > 0)
                <div id="current-images-grid" class="grid grid-cols-2 md:grid-cols-5 gap-6 mb-8">
                    @foreach($product->images as $image)
                        <div id="image-container-{{ $image->id }}" class="relative group aspect-square bg-white rounded-[32px] border p-2 shadow-sm hover:border-red-300 transition-all">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-full object-cover rounded-[24px]">
                            <button type="button" onclick="deleteImage({{ $image->id }})"
                                    class="absolute -top-3 -right-3 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                @endif
                
                <div class="bg-gray-50/50 p-8 rounded-[40px] border border-gray-100 border-dashed">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-4 ml-1">Unggah Foto Baru (Pilih Banyak)</label>
                    <input type="file" name="images[]" multiple 
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-full file:border-0 file:bg-[#1a1a2e] file:text-white file:font-bold border border-gray-200 rounded-2xl bg-white p-2">
                </div>
            </div>

            {{-- BUTTON AKSI --}}
            <div class="flex flex-col sm:flex-row justify-end gap-4 mt-12 border-t pt-10">
                {{-- Batal kembali ke URL redirect_to jika ada --}}
                <a href="{{ request('redirect_to') ?? route('admin.products.index') }}" class="px-10 py-4 bg-gray-100 rounded-2xl text-gray-600 font-bold hover:bg-gray-200 transition-all text-center text-sm">Batal</a>
                <button type="submit" 
                        class="px-12 py-4 bg-[#e8c9a0] text-[#1a1a2e] rounded-2xl font-black uppercase tracking-widest hover:bg-[#d4b78d] transition-all shadow-xl shadow-[#e8c9a0]/30 active:scale-95">
                    Update Produk
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteImage(imageId) {
        Swal.fire({
            title: 'Hapus foto galeri?',
            text: 'Tindakan ini tidak bisa dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1a1a2e',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            borderRadius: '40px'
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
                        document.getElementById(`image-container-${imageId}`).style.transform = 'scale(0)';
                        setTimeout(() => document.getElementById(`image-container-${imageId}`).remove(), 300);
                        Swal.fire({
                            title: 'Terhapus!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#1a1a2e'
                        });
                    }
                });
            }
        })
    }
</script>

<style>
    [x-cloak] { display: none !important; }
    input:focus, select:focus, textarea:focus {
        outline: none !important;
        box-shadow: 0 10px 15px -3px rgba(232, 201, 160, 0.2) !important;
    }
</style>
@endsection
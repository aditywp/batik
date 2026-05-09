@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden">
        {{-- Header Form --}}
        <div class="bg-[#1a1a2e] p-6">
            <h2 class="text-[#e8c9a0] font-bold text-xl">Tambah Produk Batik Baru</h2>
        </div>
        
        {{-- Form Start --}}
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf

            {{-- 1. INFORMASI DASAR --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]" required placeholder="Contoh: Kemeja Batik Solo">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori Utama</label>
                    <select name="category_id" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- INPUT COLLECTIONS BARU --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Target Koleksi (Collections)</label>
                    <select name="collection" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]" required>
                        <option value="">-- Pilih Koleksi --</option>
                        <option value="women" {{ old('collection') == 'women' ? 'selected' : '' }}>Women</option>
                        <option value="men" {{ old('collection') == 'men' ? 'selected' : '' }}>Men</option>
                        <option value="kids" {{ old('collection') == 'kids' ? 'selected' : '' }}>Kids</option>
                        <option value="craft" {{ old('collection') == 'craft' ? 'selected' : '' }}>Craft</option>
                        <option value="family" {{ old('collection') == 'family' ? 'selected' : '' }}>Family</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-2 italic">*Koleksi menentukan di menu mana produk ini akan muncul.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga Utama (Rp)</label>
                    <input type="number" name="price" value="{{ old('price') }}" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]" required placeholder="Harga dasar produk">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full border-gray-200 rounded-xl shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]" required>{{ old('description') }}</textarea>
            </div>

            <hr class="border-gray-100">

            {{-- 2. BAGIAN VARIASI BERLEVEL (MOTIF > UKURAN) --}}
            <div x-data="{ 
                motifs: [{ 
                    name: '', 
                    sizes: [{ size: '', price: '', stock: 0 }] 
                }] 
            }">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-lg text-[#1a1a2e]">Variasi Produk (Stok per Ukuran/Motif)</h3>
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
                                           class="mt-2 block w-full border-gray-200 rounded-xl text-sm shadow-sm" placeholder="Contoh: Parang Rusak Biru">
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Foto Motif</label>
                                    <input type="file" :name="`motifs[${mIndex}][image]`" 
                                           class="mt-2 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-white file:text-[#1a1a2e] file:font-bold border border-gray-200 rounded-xl bg-white p-1">
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
                                                <input type="number" :name="`motifs[${mIndex}][sizes][${sIndex}][price]`" x-model="sItem.price" class="w-full rounded-lg border-gray-200 text-xs" placeholder="Opsional">
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

            {{-- 3. FOTO GALERI --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Foto Galeri Produk (Opsional/Pilih Banyak)</label>
                <input type="file" name="images[]" multiple class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-stone-100 file:text-stone-700 font-medium">
            </div>

            {{-- BUTTON AKSI --}}
            <div class="flex justify-end gap-4 mt-12 border-t pt-8">
                <a href="{{ route('admin.products.index') }}" class="px-8 py-3 bg-gray-100 rounded-2xl text-gray-600 font-bold hover:bg-gray-200 transition-all">Batal</a>
                <button type="submit" class="px-10 py-3 bg-[#e8c9a0] text-[#1a1a2e] rounded-2xl font-extrabold hover:bg-[#d4b78d] transition-all shadow-xl shadow-[#e8c9a0]/20">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6" x-data="{ 
    motifs: [{ 
        name: '', 
        sizes: [{ size: '', price: '', stock: '' }] 
    }] 
}">
    <div class="max-w-5xl mx-auto bg-white rounded-[40px] shadow-xl overflow-hidden border border-gray-100">
        {{-- Header Form --}}
        <div class="bg-[#1a1a2e] p-8">
            <h2 class="text-[#e8c9a0] font-bold text-2xl tracking-tight">Tambah Produk Batik Baru</h2>
            <p class="text-gray-400 text-xs mt-1">Lengkapi informasi dasar dan variasi stok produk Anda. Tanda (<span class="text-red-500 font-bold">*</span>) wajib diisi.</p>
        </div>
        
        {{-- Form Start --}}
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-8 lg:p-12 space-y-10">
            @csrf

            {{-- 1. INFORMASI DASAR --}}
            <div class="space-y-6">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-6">01. Informasi Dasar</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2 ml-1">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" maxlength="100"
                               class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3.5 px-5 focus:border-[#e8c9a0] focus:ring-[#e8c9a0] transition-all" 
                               required placeholder="Contoh: Kemeja Batik Solo">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2 ml-1">Kategori Utama <span class="text-red-500">*</span></label>
                        <select name="category_id" class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3.5 px-5 focus:border-[#e8c9a0] focus:ring-[#e8c9a0] transition-all" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- INPUT COLLECTIONS --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2 ml-1">Target Koleksi <span class="text-red-500">*</span></label>
                        <select name="collection" class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3.5 px-5 focus:border-[#e8c9a0] focus:ring-[#e8c9a0] transition-all" required>
                            <option value="" disabled selected>-- Pilih Koleksi --</option>
                            <option value="Women" {{ old('collection') == 'Women' ? 'selected' : '' }}>Women</option>
                            <option value="Men" {{ old('collection') == 'Men' ? 'selected' : '' }}>Men</option>
                            <option value="Craft" {{ old('collection') == 'Craft' ? 'selected' : '' }}>Craft</option>
                            <option value="Family" {{ old('collection') == 'Family' ? 'selected' : '' }}>Family</option>
                        </select>
                        <p class="text-[10px] text-gray-400 mt-2 italic ml-1">*Koleksi menentukan di menu mana produk ini akan muncul.</p>
                        @error('collection') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- HARGA UTAMA DENGAN PERINGATAN DINAMIS --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2 ml-1">Harga Utama <span class="text-red-500">*</span></label>
                        <div class="relative" x-data="{
                            rawPrice: '{{ old('price', '') }}',
                            formatInput(e) {
                                let val = e.target.value.replace(/\D/g, ''); 
                                if(val.length > 8) val = val.substring(0, 8); 
                                this.rawPrice = val;
                                e.target.value = val ? new Intl.NumberFormat('id-ID').format(val) : '';
                            }
                        }">
                            <span class="absolute left-5 top-3.5 text-gray-500 font-bold">Rp</span>
                            
                            {{-- Input teks visual --}}
                            <input type="text" minlength="6"
                                   :value="rawPrice ? new Intl.NumberFormat('id-ID').format(rawPrice) : ''"
                                   @input="formatInput"
                                   class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3.5 pl-12 pr-5 focus:border-[#e8c9a0] focus:ring-[#e8c9a0] transition-all" 
                                   required placeholder="Minimal 10.000">
                            
                            {{-- Input tersembunyi untuk backend --}}
                            <input type="hidden" name="price" :value="rawPrice">
                            
                            {{-- Teks Peringatan Dinamis jika di bawah 10.000 --}}
                            <p x-cloak x-show="rawPrice !== '' && parseInt(rawPrice) < 10000" class="text-red-500 text-[10px] mt-1.5 ml-1 font-bold italic">
                                ⚠️ Harga tidak valid (Minimal Rp 10.000)
                            </p>
                        </div>
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2 ml-1">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" maxlength="1000"
                              class="w-full bg-gray-50 border-gray-200 rounded-2xl py-3.5 px-5 focus:border-[#e8c9a0] focus:ring-[#e8c9a0] transition-all" 
                              required placeholder="Tuliskan detail bahan, kenyamanan, dan motif batik...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- 2. BAGIAN VARIASI BERLEVEL (MOTIF > UKURAN) --}}
            <div>
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em]">02. Variasi Produk (Stok per Ukuran/Motif)</h3>
                    <button type="button" @click="motifs.push({ name: '', sizes: [{ size: '', price: '', stock: '' }] })"
                            class="bg-[#1a1a2e] text-white px-5 py-2.5 rounded-2xl text-xs font-bold hover:bg-black transition-all shadow-lg shadow-gray-200">
                        + Tambah Motif Baru
                    </button>
                </div>
                
                <div class="space-y-10">
                    <template x-for="(motif, mIndex) in motifs" :key="mIndex">
                        <div class="p-8 bg-gray-50/50 rounded-[40px] border border-gray-100 relative shadow-sm">
                            {{-- Tombol Hapus Motif --}}
                            <button type="button" @click="motifs.splice(mIndex, 1)" x-show="motifs.length > 1"
                                    class="absolute top-8 right-8 text-red-300 hover:text-red-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Motif <span class="text-red-500">*</span></label>
                                    <input type="text" :name="`motifs[${mIndex}][name]`" x-model="motif.name" maxlength="100" required
                                           class="mt-2 block w-full bg-white border-gray-200 rounded-2xl py-3 px-5 text-sm shadow-sm focus:border-[#e8c9a0] focus:ring-[#e8c9a0]" 
                                           placeholder="Contoh: Parang Rusak Biru">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Foto Motif <span class="text-red-500">*</span></label>
                                    <input type="file" :name="`motifs[${mIndex}][image]`" required
                                           class="mt-2 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-6 file:rounded-full file:border-0 file:bg-[#1a1a2e] file:text-white file:font-bold border border-gray-200 rounded-2xl bg-white p-1.5">
                                </div>
                            </div>

                            {{-- LEVEL 2: UKURAN DI DALAM MOTIF --}}
                            <div class="bg-white p-6 rounded-[30px] border border-gray-100 shadow-inner">
                                <div class="flex justify-between items-center mb-6">
                                    <p class="text-xs font-bold text-gray-500 italic ml-2">Daftar Ukuran untuk motif ini:</p>
                                    <button type="button" @click="motif.sizes.push({ size: '', price: '', stock: '' })"
                                            class="text-[10px] bg-blue-50 text-blue-600 px-4 py-2 rounded-xl font-bold hover:bg-blue-100 transition-colors">
                                        + Tambah Ukuran
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <template x-for="(sItem, sIndex) in motif.sizes" :key="sIndex">
                                        <div class="grid grid-cols-4 gap-4 items-end bg-gray-50/50 p-4 rounded-2xl border border-dashed border-gray-200">
                                            
                                            {{-- SIZE --}}
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-1 block ml-1">Size <span class="text-red-500">*</span></label>
                                                <select :name="`motifs[${mIndex}][sizes][${sIndex}][size]`" x-model="sItem.size" required
                                                        class="w-full rounded-xl border-gray-200 bg-white text-xs focus:ring-[#e8c9a0]">
                                                    <option value="" disabled selected>-- Pilih Size --</option>
                                                    <option value="S">S</option>
                                                    <option value="M">M</option>
                                                    <option value="L">L</option>
                                                    <option value="XL">XL</option>
                                                    <option value="XXL">XXL</option>
                                                </select>
                                            </div>
                                            
                                            {{-- HARGA KHUSUS --}}
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-1 block ml-1">Harga Khusus (Opsional)</label>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-2.5 text-gray-400 font-bold text-xs">Rp</span>
                                                    
                                                    <input type="text" minlength="6"
                                                           :value="sItem.price ? new Intl.NumberFormat('id-ID').format(sItem.price) : ''"
                                                           @input="let val = $event.target.value.replace(/\D/g, ''); if(val.length > 8) val = val.substring(0,8); sItem.price = val; $event.target.value = val ? new Intl.NumberFormat('id-ID').format(val) : ''"
                                                           class="w-full rounded-xl border-gray-200 bg-white text-xs focus:ring-[#e8c9a0] pl-8" 
                                                           placeholder="Min 10.000">
                                                    
                                                    <input type="hidden" :name="`motifs[${mIndex}][sizes][${sIndex}][price]`" :value="sItem.price">
                                                </div>
                                                {{-- Peringatan Dinamis --}}
                                                <p x-cloak x-show="sItem.price !== '' && parseInt(sItem.price) < 10000" class="text-red-500 text-[9px] mt-1 ml-1 font-bold italic">
                                                    ⚠️ Min. Rp 10.000
                                                </p>
                                            </div>

                                            {{-- STOK --}}
                                            <div>
                                                <label class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-1 block ml-1">Stok <span class="text-red-500">*</span></label>
                                                {{-- Batas Stok Maksimal 3 Digit --}}
                                                <input type="text" :name="`motifs[${mIndex}][sizes][${sIndex}][stock]`" x-model="sItem.stock" required maxlength="3"
                                                       @input="$event.target.value = $event.target.value.replace(/\D/g, ''); sItem.stock = $event.target.value"
                                                       class="w-full rounded-xl border-gray-200 bg-white text-xs focus:ring-[#e8c9a0]" placeholder="Max 999">
                                            </div>
                                            
                                            <div class="flex justify-center">
                                                <button type="button" @click="motif.sizes.splice(sIndex, 1)" x-show="motif.sizes.length > 1" 
                                                        class="text-red-400 p-2 hover:text-red-600 transition-colors">
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
                @error('motifs') <p class="text-red-500 text-sm mt-4 font-bold">{{ $message }}</p> @enderror
            </div>

            <hr class="border-gray-100">

            {{-- 3. FOTO GALERI --}}
            <div class="space-y-4">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em]">03. Galeri Foto Produk</h3>
                <div class="bg-gray-50/50 p-8 rounded-[40px] border border-gray-100">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-4 ml-1">Unggah Foto Galeri (Opsional/Pilih Banyak)</label>
                    <input type="file" name="images[]" multiple 
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-full file:border-0 file:bg-[#1a1a2e] file:text-white file:font-bold border border-gray-200 rounded-2xl bg-white p-2">
                    <p class="text-[10px] text-gray-400 mt-3 italic ml-1">*Pilih foto yang menunjukkan detail produk dari berbagai sudut.</p>
                </div>
            </div>

            {{-- BUTTON AKSI --}}
            <div class="flex flex-col sm:flex-row justify-end gap-4 mt-12 border-t pt-10">
                <a href="{{ route('admin.products.index') }}" 
                   class="px-10 py-4 bg-gray-100 rounded-2xl text-gray-600 font-bold hover:bg-gray-200 transition-all text-center">
                   Batal
                </a>
                <button type="submit" 
                        class="px-12 py-4 bg-[#e8c9a0] text-[#1a1a2e] rounded-2xl font-black uppercase tracking-widest hover:bg-[#d4b78d] transition-all shadow-xl shadow-[#e8c9a0]/30 active:scale-95">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>

{{-- CSS KHUSUS --}}
<style>
    [x-cloak] { display: none !important; }
    input:focus, select:focus, textarea:focus {
        outline: none !important;
        box-shadow: 0 10px 15px -3px rgba(232, 201, 160, 0.2) !important;
    }
</style>
@endsection
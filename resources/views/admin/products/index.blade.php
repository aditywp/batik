@extends('layouts.admin')

@section('content')
{{-- CSS Khusus untuk mencegah modal berkedip saat refresh --}}
<style>
    [x-cloak] { display: none !important; }
</style>

{{-- PERBAIKAN: x-data dibuat seringkas mungkin agar tidak ada konflik tanda petik di HTML --}}
<div class="container mx-auto p-6 font-sans" x-data="{ 
    viewMode: 'table', 
    showDetail: false, 
    selectedProduct: {},
    activeImage: '',
    selectedMotif: null,
    selectedSize: null
}">
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-black text-[#1a1a2e] tracking-tight uppercase italic">Daftar Produk Batik</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola stok dan lihat variasi motif/ukuran secara interaktif.</p>
        </div>

        <div class="flex items-center gap-4">
            {{-- Tombol Switch View --}}
            <div class="flex bg-gray-100 p-1 rounded-lg border border-gray-200">
                <button @click="viewMode = 'table'" 
                        :class="viewMode === 'table' ? 'bg-white shadow-sm text-[#1a1a2e]' : 'text-gray-500'"
                        class="px-3 py-1.5 rounded-md transition-all flex items-center gap-2 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    Tabel
                </button>
                <button @click="viewMode = 'grid'" 
                        :class="viewMode === 'grid' ? 'bg-white shadow-sm text-[#1a1a2e]' : 'text-gray-500'"
                        class="px-3 py-1.5 rounded-md transition-all flex items-center gap-2 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Kartu
                </button>
            </div>

            <a href="{{ route('admin.products.create') }}" class="bg-[#e8c9a0] text-[#1a1a2e] px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#d4b78d] transition-all flex items-center gap-2 shadow-sm">
                <span class="text-sm">+</span> Tambah Produk
            </a>
        </div>
    </div>

    {{-- BAGIAN FILTER & PENCARIAN --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-8">
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Cari Nama Produk</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Kemeja Arkanza..." 
                           class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-[#1a1a2e] focus:border-[#1a1a2e] pl-10 h-11">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="w-full md:w-[180px]">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kategori</label>
                <select name="category" onchange="this.form.submit()" 
                        class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-[#1a1a2e] focus:border-[#1a1a2e] h-11">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-[150px]">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Collections</label>
                <select name="collection" onchange="this.form.submit()" 
                        class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-[#1a1a2e] focus:border-[#1a1a2e] h-11">
                    <option value="">Semua Koleksi</option>
                    @foreach(['Women', 'Men', 'Craft', 'Family'] as $col)
                        <option value="{{ $col }}" {{ request('collection') == $col ? 'selected' : '' }}>
                            {{ $col }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-[180px]">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kondisi Stok</label>
                <select name="stock_status" onchange="this.form.submit()" 
                        class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-[#1a1a2e] focus:border-[#1a1a2e] h-11 font-medium">
                    <option value="">Semua Status Stok</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>⚠️ Stok Menipis (≤ 5)</option>
                    <option value="empty" {{ request('stock_status') === 'empty' ? 'selected' : '' }}>❌ Stok Habis (0)</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-[#1a1a2e] text-white px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all text-sm h-11">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'category', 'collection', 'stock_status']))
                    <a href="{{ route('admin.products.index') }}" 
                       class="px-4 py-2.5 text-xs font-black text-red-500 hover:bg-red-50 rounded-xl transition-colors flex items-center h-11 uppercase tracking-widest">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TAMPILAN 1: TABEL --}}
    <div x-show="viewMode === 'table'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Foto</th>
                    <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Produk</th>
                    <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Total Stok</th>
                    <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($products as $product)
                <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" 
                    @click="selectedProduct = {{ $product->load(['category', 'images', 'variants'])->toJson() }}; 
                            activeImage = selectedProduct.variants?.[0]?.image_path || selectedProduct.images?.[0]?.image_path; 
                            selectedMotif = selectedProduct.variants?.[0]?.motif;
                            selectedSize = null;
                            showDetail = true">
                    <td class="p-5">
                        <img src="{{ asset('storage/' . ($product->variants->first()->image_path ?? 'placeholder.jpg')) }}" 
                             class="w-16 h-16 object-cover rounded-xl shadow-sm border border-gray-100">
                    </td>
                    <td class="p-5">
                        <p class="font-bold text-[#1a1a2e]">{{ $product->name }}</p>
                        <div class="flex gap-2 mt-1">
                            <span class="text-[9px] bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full font-black uppercase">{{ $product->category->name ?? 'Batik' }}</span>
                            <span class="text-[9px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-black uppercase">{{ $product->collection }}</span>
                        </div>
                    </td>
                    <td class="p-5 text-center font-bold">
                        @if($product->stock == 0)
                            <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-xs font-extrabold uppercase">Habis</span>
                        @elseif($product->stock <= 5)
                            <span class="px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-xs font-extrabold uppercase">{{ $product->stock }} pcs (Menipis)</span>
                        @else
                            <span class="text-[#1a1a2e] text-sm">{{ $product->stock }} pcs</span>
                        @endif
                    </td>
                    <td class="p-5" @click.stop>
                        <div class="flex justify-center items-center gap-4">
                            <a href="{{ route('admin.products.edit', [$product->id, 'redirect_to' => request()->fullUrl()]) }}" class="text-blue-500 hover:text-blue-700 font-bold text-xs uppercase tracking-wider">Edit</a>
                            
                            {{-- Button Tong Sampah Tabel Terikat Fungsi Global --}}
                            <button type="button" @click.stop="triggerDelete({{ $product->id }})" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- TAMPILAN 2: KARTU (GRID) --}}
    <div x-show="viewMode === 'grid'" x-transition class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition-all cursor-pointer"
             @click="selectedProduct = {{ $product->load(['category', 'images', 'variants'])->toJson() }}; 
                     activeImage = selectedProduct.variants?.[0]?.image_path || selectedProduct.images?.[0]?.image_path; 
                     selectedMotif = selectedProduct.variants?.[0]?.motif;
                     selectedSize = null;
                     showDetail = true">
            <div class="relative aspect-square">
                <img src="{{ asset('storage/' . ($product->variants->first()->image_path ?? 'placeholder.jpg')) }}" 
                     class="w-full h-full object-cover">
                <div class="absolute top-3 right-3">
                    @if($product->stock == 0)
                        <span class="px-2 py-1 bg-red-600 text-white text-[10px] font-black rounded-lg shadow-sm uppercase">Habis</span>
                    @elseif($product->stock <= 5)
                        <span class="px-2 py-1 bg-orange-500 text-white text-[10px] font-black rounded-lg shadow-sm uppercase">Sisa {{ $product->stock }}</span>
                    @else
                        <span class="px-2 py-1 bg-white/90 backdrop-blur-sm text-[#1a1a2e] text-[10px] font-bold rounded-lg shadow-sm">{{ $product->stock }} pcs</span>
                    @endif
                </div>
            </div>
            <div class="p-4">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest mb-1">{{ $product->category->name ?? 'Batik' }} • {{ $product->collection }}</p>
                <h3 class="font-bold text-[#1a1a2e] mb-2 truncate">{{ $product->name }}</h3>
                <p class="text-emerald-600 font-bold mb-4">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                
                <div class="flex items-center gap-2 border-t border-gray-50 pt-4" @click.stop>
                    <a href="{{ route('admin.products.edit', [$product->id, 'redirect_to' => request()->fullUrl()]) }}" class="flex-1 text-center py-2 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-100 transition-colors">
                        Edit
                    </a>
                    
                    {{-- Button Tong Sampah Kartu Terikat Fungsi Global --}}
                    <button type="button" @click.stop="triggerDelete({{ $product->id }})" class="px-3 py-2 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- MODAL DETAIL (Alpine.js) --}}
    <div x-show="showDetail" 
         class="fixed inset-0 z-[99] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-cloak x-transition>
        <div @click.away="showDetail = false" 
             class="bg-white rounded-[40px] max-w-5xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
            
            <div class="p-8 lg:p-12">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-xl font-bold text-[#1a1a2e]">Preview Produk</h2>
                    <button @click="showDetail = false" class="p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div class="space-y-4">
                        <div class="aspect-square bg-gray-50 rounded-[32px] overflow-hidden border border-gray-100 shadow-inner">
                            <img :src="'/storage/' + activeImage" class="w-full h-full object-cover transition-all duration-500">
                        </div>
                        <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                            <template x-for="v in [...new Map(selectedProduct.variants?.map(item => [item['image_path'], item])).values()]" :key="v.id">
                                <div @click="activeImage = v.image_path; selectedMotif = v.motif" 
                                     class="w-16 h-16 rounded-xl border-2 overflow-hidden flex-shrink-0 cursor-pointer transition-all"
                                     :class="activeImage === v.image_path ? 'border-[#1a1a2e]' : 'border-transparent opacity-50'">
                                    <img :src="'/storage/' + v.image_path" class="w-full h-full object-cover">
                                </div>
                            </template>
                            <template x-for="img in selectedProduct.images" :key="img.id">
                                <div @click="activeImage = img.image_path" 
                                     class="w-16 h-16 rounded-xl border-2 overflow-hidden flex-shrink-0 cursor-pointer transition-all"
                                     :class="activeImage === img.image_path ? 'border-[#1a1a2e]' : 'border-transparent opacity-50'">
                                    <img :src="'/storage/' + img.image_path" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- INFO KANAN MODAL --}}
                    <div class="flex flex-col">
                        <div class="mb-6">
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 text-[10px] font-bold uppercase tracking-widest rounded-full" x-text="selectedProduct.category?.name"></span>
                            <h1 class="text-4xl font-extrabold text-[#1a1a2e] mt-4 leading-tight" x-text="selectedProduct.name"></h1>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-3xl mb-8 border border-gray-100">
                            <p class="text-xs text-gray-400 mb-1 font-bold uppercase tracking-widest">Harga Dasar</p>
                            <h3 class="text-3xl font-black text-red-600" x-text="'Rp ' + (selectedProduct.price ? parseInt(selectedProduct.price).toLocaleString('id-ID') : 0)"></h3>
                        </div>

                        <div class="space-y-8">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Pilih Motif</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="motifName in [...new Set(selectedProduct.variants?.map(v => v.motif))]" :key="motifName">
                                        <button @click="selectedMotif = motifName; 
                                                        activeImage = selectedProduct.variants.find(v => v.motif === motifName).image_path;
                                                        selectedSize = null"
                                                class="px-4 py-2 rounded-xl border text-xs font-bold transition-all"
                                                :class="selectedMotif === motifName ? 'bg-[#1a1a2e] text-white border-[#1a1a2e]' : 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'"
                                                x-text="motifName">
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Pilih Ukuran</p>
                                <div class="flex flex-wrap gap-3">
                                    <template x-for="v in selectedProduct.variants?.filter(v => v.motif === selectedMotif)" :key="v.id">
                                        <button @click="selectedSize = v.id"
                                                class="px-4 py-3 border rounded-2xl text-center min-w-[70px] transition-all relative overflow-hidden"
                                                :class="selectedSize === v.id ? 'border-[#1a1a2e] bg-[#f8fafc] ring-2 ring-[#1a1a2e]/10' : 'border-gray-200 bg-white hover:border-gray-300'">
                                            <p class="text-sm font-black text-[#1a1a2e]" x-text="v.size || 'N/A'"></p>
                                            <p class="text-[9px] font-bold" :class="v.stock > 0 ? 'text-emerald-600' : 'text-red-500'" x-text="v.stock + ' pcs'"></p>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Deskripsi</h4>
                                <p class="text-sm text-gray-600 leading-relaxed italic" x-text="selectedProduct.description"></p>
                            </div>
                        </div>

                        <div class="pt-10 flex gap-4 mt-auto">
                            <a :href="'/admin/products/' + selectedProduct.id + '/edit'" 
                               class="flex-[4] bg-[#1a1a2e] text-[#e8c9a0] text-center py-4 rounded-2xl font-bold hover:shadow-lg hover:bg-black transition-all">
                                Edit Produk Ini
                            </a>
                            
                            {{-- Button Tong Sampah Modal Terikat Fungsi Global --}}
                            <button type="button" @click="triggerDelete(selectedProduct.id)" class="flex-1 bg-red-50 text-red-500 flex items-center justify-center rounded-2xl border border-red-100 hover:bg-red-100 transition-all py-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- AREA ISOLASI FORM: Ditaruh di luar seksi mana pun agar 100% aman dari Nested Form --}}
<div id="isolated-delete-containers" class="hidden" x-cloak>
    @foreach($products as $product)
        <form id="global-delete-form-{{ $product->id }}" action="{{ route('admin.products.destroy', $product->id) }}" method="POST">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
</div>

{{-- SCRIPT ENGINE JAVASCRIPT VANILLA & POPUP TOAST LARAVEL --}}
<script>
    // Fungsi pemicu SweetAlert ditaruh di level global window agar bisa dipanggil lancar oleh AlpineJS
    function triggerDelete(productId) {
        Swal.fire({
            title: 'Hapus Produk Kain?',
            text: "Data katalog produk beserta seluruh variasi motif/ukuran akan dihapus permanen dari sistem!",
            icon: 'warning',
            showCancelButton: true,
            background: '#ffffff',
            confirmButtonColor: '#1a1a2e',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: '<span style="color: #e8c9a0; font-weight: 900; text-transform: uppercase; font-size: 11px; letter-spacing: 1px;">Hapus Permanen</span>',
            cancelButtonText: '<span style="color: #4b5563; font-weight: 900; text-transform: uppercase; font-size: 11px; letter-spacing: 1px;">Batal</span>',
            customClass: {
                popup: 'rounded-[32px]',
                title: 'font-sans font-black text-[#1a1a2e] uppercase italic tracking-tight',
                htmlContainer: 'font-sans text-xs font-medium text-gray-400'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('global-delete-form-' + productId).submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Otomatis Render Popup Toast jika Controller Laravel mengirim session success
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
    });
</script>
@endsection
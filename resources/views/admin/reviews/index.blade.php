@extends('layouts.admin')

@section('content')
{{-- CSS Khusus untuk mencegah modal berkedip saat refresh --}}
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="container mx-auto p-6 font-sans">
    {{-- HEADER MANAGEMENT REVIEW --}}
    <div class="mb-8">
        <h1 class="text-3xl font-black text-[#1a1a2e] tracking-tight uppercase italic">Ulasan Pelanggan</h1>
        <p class="text-gray-500 text-sm mt-1">Semua ulasan yang dikirim pembeli otomatis diterbitkan langsung ke halaman katalog produk.</p>
    </div>

    {{-- INTERFACE FILTER STATISTIK CEPAT --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-8">
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="w-full md:w-[200px]">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Saring Berdasarkan Rating</label>
                <select name="rating" onchange="this.form.submit()" class="w-full bg-gray-50 border-gray-100 rounded-xl text-sm focus:ring-[#1a1a2e] focus:border-[#1a1a2e] h-11 font-medium">
                    <option value="">Semua Bintang</option>
                    @foreach([5,4,3,2,1] as $star)
                        <option value="{{ $star }}" {{ request('rating') == $star ? 'selected' : '' }}>⭐ {{ $star }} Bintang</option>
                    @endforeach
                </select>
            </div>

            @if(request()->filled('rating'))
                <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2.5 text-xs font-black text-red-500 hover:bg-red-50 rounded-xl transition-colors flex items-center h-11 uppercase tracking-widest">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    {{-- TABEL MODERASI REVIEWS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Pelanggan & Order</th>
                    <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Produk Batik</th>
                    <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider">Rating & Komentar</th>
                    <th class="p-5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reviews as $review)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    {{-- Info Pelanggan --}}
                    <td class="p-5">
                        <p class="font-bold text-[#1a1a2e]">{{ $review->user->name ?? 'Anonim Customer' }}</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mt-0.5">Invoice ID: #{{ $review->order_id }}</p>
                    </td>
                    
                    {{-- Info Produk (MENGARAH KE HALAMAN EDIT PRODUK ADMIN) --}}
                    <td class="p-5">
                        @if($review->product)
                            <a href="{{ route('admin.products.edit', $review->product->id) }}" 
                               class="group inline-flex flex-col max-w-[200px]">
                                <span class="font-bold text-[#1a1a2e] group-hover:text-amber-600 group-hover:underline transition-colors truncate">
                                    {{ $review->product->name }}
                                </span>
                                <span class="text-[9px] bg-amber-50 text-amber-800 px-2 py-0.5 rounded-full font-black uppercase inline-block mt-1 self-start">
                                    {{ $review->product->collection ?? 'Batik' }} 
                                </span>
                            </a>
                        @else
                            <span class="text-gray-400 italic text-xs">Produk Telah Dihapus</span>
                        @endif
                    </td>

                    {{-- Rating & Isi Komentar --}}
                    <td class="p-5 max-w-sm">
                        <div class="flex text-amber-400 gap-0.5 mb-1.5">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="text-sm">{{ $i <= $review->rating ? '★' : '☆' }}</span>
                            @endfor
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed italic">"{{ $review->comment }}"</p>
                    </td>

                    {{-- Tombol Aksi Kerja --}}
                    <td class="p-5 text-center">
                        <div class="flex justify-center items-center">
                            <button type="button" onclick="triggerDelete({{ $review->id }})" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Hapus Ulasan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-12 text-center text-gray-400 italic text-sm">Tidak ada data ulasan ulasan pembeli yang terekam dalam sistem.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION INTERACTIVE --}}
        @if($reviews->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    Menampilkan <span class="text-[#1a1a2e] font-black">{{ $reviews->firstItem() }}</span> - <span class="text-[#1a1a2e] font-black">{{ $reviews->lastItem() }}</span> dari <span class="text-[#1a1a2e] font-black">{{ $reviews->total() }}</span> Ulasan
                </div>
                <div class="flex items-center gap-1">
                    @if ($reviews->onFirstPage())
                        <span class="px-3 py-2 bg-gray-100 text-gray-300 text-xs font-black rounded-xl uppercase tracking-widest cursor-not-allowed">Prev</span>
                    @else
                        <a href="{{ $reviews->previousPageUrl() }}" class="px-3 py-2 bg-white border border-gray-200 text-gray-600 hover:border-[#1a1a2e] hover:text-[#1a1a2e] text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-sm">Prev</a>
                    @endif

                    @if ($reviews->hasMorePages())
                        <a href="{{ $reviews->nextPageUrl() }}" class="px-3 py-2 bg-white border border-gray-200 text-gray-600 hover:border-[#1a1a2e] hover:text-[#1a1a2e] text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-sm">Next</a>
                    @else
                        <span class="px-3 py-2 bg-gray-100 text-gray-300 text-xs font-black rounded-xl uppercase tracking-widest cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

{{-- AREA ISOLASI FORM UNTUK ROUTE DESTRUCTION --}}
<div id="isolated-review-forms" class="hidden" x-cloak>
    @foreach($reviews as $review)
        <form id="delete-form-{{ $review->id }}" action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
</div>

{{-- JAVASCRIPT SWEETALERT ENGINE --}}
<script>
    function triggerDelete(reviewId) {
        Swal.fire({
            title: 'Hapus Ulasan Ini?',
            text: "Komentar ulasan terpilih akan dimusnahkan selamanya dari basis data toko!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: '<span style="color: #ffffff; font-weight: 900; text-transform: uppercase; font-size: 11px;">Hapus Permanen</span>',
            cancelButtonText: '<span style="color: #4b5563; font-weight: 900; text-transform: uppercase; font-size: 11px;">Batal</span>',
            customClass: { popup: 'rounded-[32px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + reviewId).submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL',
                text: "{{ session('success') }}",
                timer: 2500,
                showConfirmButton: false,
                background: '#1a1a2e',
                iconColor: '#e8c9a0',
                customClass: {
                    popup: 'rounded-[24px]',
                    title: 'font-sans font-black text-[#e8c9a0]'
                }
            });
        @endif
    });
</script>
@endsection
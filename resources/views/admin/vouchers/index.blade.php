@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-2xl shadow-sm border border-gray-100 font-sans">
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-[#1a1a2e] uppercase italic tracking-tighter">Manajemen Voucher</h2>
        <a href="{{ route('admin.vouchers.create') }}" class="bg-[#e8c9a0] hover:bg-[#d4b78d] text-[#1a1a2e] px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-sm">
            + Tambah Voucher
        </a>
    </div>

    {{-- PANEL PENCARIAN & FILTER 3 STATUS --}}
    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 mb-6">
        <form action="{{ route('admin.vouchers.index') }}" method="GET" class="flex flex-wrap gap-3">
            
            {{-- Input Pencarian --}}
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode voucher..." 
                       class="w-full bg-white border-gray-200 rounded-xl text-sm focus:ring-[#1a1a2e] focus:border-[#1a1a2e] h-11 px-4 transition-all">
            </div>

            {{-- Dropdown Filter Status --}}
            <div class="w-full md:w-[220px]">
                <select name="status" onchange="this.form.submit()" 
                        class="w-full bg-white border-gray-200 rounded-xl text-sm focus:ring-[#1a1a2e] focus:border-[#1a1a2e] h-11 px-4 transition-all font-medium text-gray-600">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                </select>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex gap-2">
                <button type="submit" class="bg-[#1a1a2e] text-white px-6 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all shadow-sm active:scale-95 h-11">
                    Cari
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.vouchers.index') }}" class="flex items-center justify-center bg-red-50 text-red-500 px-5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-red-100 transition-all h-11">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABEL DATA VOUCHER --}}
    <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-[11px] font-black text-gray-400 uppercase tracking-widest">
                    <th class="p-4">Nama Voucher</th>
                    <th class="p-4">Kode</th>
                    <th class="p-4">Potongan</th>
                    <th class="p-4">Syarat Poin</th>
                    <th class="p-4">Berlaku Sampai</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($vouchers as $item)
                
                {{-- LOGIKA KEDALUWARSA: Memaksa tanggal ke ujung hari (23:59:59) sebelum di cek --}}
                @php
                    $isExpired = $item->valid_until && \Carbon\Carbon::parse($item->valid_until)->endOfDay()->isPast();
                @endphp

                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="p-4 text-gray-800 font-bold">
                        {{ $item->name }}
                        @if($item->is_welcome_voucher)
                            <span class="ml-2 bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wide">Welcome</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg font-mono text-xs border border-gray-200 font-bold tracking-widest">{{ $item->code }}</span>
                    </td>
                    <td class="p-4 text-green-600 font-black italic">Rp {{ number_format($item->discount_amount, 0, ',', '.') }}</td>
                    <td class="p-4 text-blue-600 font-black">{{ $item->points_required }} Poin</td>
                    
                    {{-- Tampilan format paksa ke 23:59 agar jelas --}}
                    <td class="p-4 text-xs font-bold text-gray-500">
                        {{ $item->valid_until ? \Carbon\Carbon::parse($item->valid_until)->format('d M Y') . ', 23:59' : 'Tanpa Batas Waktu' }}
                    </td>
                    
                    <td class="p-4 text-center">
                        {{-- STATUS --}}
                        @if($isExpired)
                            <span class="bg-gray-200 text-gray-500 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Kedaluwarsa</span>
                        @elseif($item->is_active)
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Aktif</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Nonaktif</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.vouchers.edit', $item->id) }}" class="bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-blue-100 transition-colors">Edit</a>
                            
                            {{-- Form Hapus --}}
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
                    <td colspan="7" class="p-12 text-center text-gray-400 italic font-medium">
                        {{ request('search') || request('status') ? 'Tidak ada voucher yang cocok dengan pencarian/filter.' : 'Belum ada voucher yang ditambahkan.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        {{-- UI Pagination Premium Kustomisasi Sempurna (Diadopsi dari halaman Orders) --}}
        @if($vouchers->hasPages())
        <div class="px-6 py-4 border-t border-stone-100 bg-stone-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs font-semibold text-stone-400 uppercase tracking-wider">
                Menampilkan <span class="text-[#1a1a2e] font-black">{{ $vouchers->firstItem() }}</span> - <span class="text-[#1a1a2e] font-black">{{ $vouchers->lastItem() }}</span> dari <span class="text-[#1a1a2e] font-black">{{ $vouchers->total() }}</span> Voucher
            </div>
            <div class="flex items-center gap-1">
                {{-- Tombol Previous --}}
                @if ($vouchers->onFirstPage())
                    <span class="px-3 py-2 bg-gray-100 text-gray-300 text-xs font-black rounded-xl uppercase tracking-widest cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $vouchers->appends(request()->query())->previousPageUrl() }}" class="px-3 py-2 bg-white border border-gray-200 text-gray-600 hover:border-[#1a1a2e] hover:text-[#1a1a2e] text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-sm">Prev</a>
                @endif

                {{-- Nomor Halaman Dinamis --}}
                <div class="hidden sm:flex items-center gap-1">
                    @foreach ($vouchers->getUrlRange(max(1, $vouchers->currentPage() - 2), min($vouchers->lastPage(), $vouchers->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url . '&' . http_build_query(request()->except('page')) }}" 
                           class="w-9 h-9 flex items-center justify-center rounded-xl text-xs font-bold transition-all border {{ $page == $vouchers->currentPage() ? 'bg-[#1a1a2e] text-[#e8c9a0] border-[#1a1a2e] font-black shadow-md shadow-[#1a1a2e]/10' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-400' }}">
                            {{ $page }}
                        </a>
                    @endforeach
                </div>

                {{-- Tombol Next --}}
                @if ($vouchers->hasMorePages())
                    <a href="{{ $vouchers->appends(request()->query())->nextPageUrl() }}" class="px-3 py-2 bg-white border border-gray-200 text-gray-600 hover:border-[#1a1a2e] hover:text-[#1a1a2e] text-xs font-black rounded-xl uppercase tracking-widest transition-all shadow-sm">Next</a>
                @else
                    <span class="px-3 py-2 bg-gray-100 text-gray-300 text-xs font-black rounded-xl uppercase tracking-widest cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif
        
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
                popup: 'rounded-[24px]',
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
                popup: 'rounded-[24px]',
                title: 'font-sans font-black text-red-600 italic',
            }
        });
    @endif
});
</script>
@endsection
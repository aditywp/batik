@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-[#f5f5f5] text-black py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-12" x-data="{ activeTab: 'tersedia' }">

        {{-- HEADER --}}
        <div>
            <div class="flex items-center gap-3 text-sm text-gray-400 mb-6">
                <a href="{{ route('welcome') }}" class="hover:text-black transition-all">Home</a>
                <span>›</span>
                <span class="font-semibold text-black">Vouchers & Points</span>
            </div>

            <h1 class="text-4xl font-bold text-black mb-2 font-playfair">Voucher & Poin Saya</h1>
            <p class="text-gray-500">Tukarkan poin belanja Anda dengan berbagai potongan harga menarik.</p>
        </div>

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl font-medium flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl font-medium flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- PANEL POIN CUSTOMER --}}
        <div class="bg-black text-white rounded-2xl p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -top-10 opacity-10">
                <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
            </div>
            
            <div class="relative z-10 text-center md:text-left">
                <h2 class="text-lg font-medium text-gray-300 mb-1">Total Poin Belanja</h2>
                <div class="text-5xl font-black tracking-tight">{{ number_format($user->points, 0, ',', '.') }} <span class="text-2xl font-semibold text-[#e8c9a0]">Poin</span></div>
                <p class="text-sm text-gray-400 mt-3">Dapatkan 1 Poin untuk setiap pembelanjaan Rp 10.000,-</p>
            </div>
            <div class="relative z-10">
                <a href="#tukar-poin" class="inline-block bg-white text-black font-bold px-8 py-4 rounded-xl hover:bg-gray-200 transition-colors">
                    Tukar Poin Sekarang ↓
                </a>
            </div>
        </div>

        {{-- DOMPET VOUCHER --}}
        <div>
            <h2 class="text-2xl font-bold text-black mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                Dompet Voucher Saya
            </h2>

            <div class="flex gap-8 border-b border-gray-300 mb-8">
                <button @click="activeTab = 'tersedia'" :class="activeTab === 'tersedia' ? 'border-black text-black' : 'border-transparent text-gray-400'" class="pb-4 border-b-2 font-bold uppercase text-xs tracking-widest transition-all">
                    Tersedia ({{ $availableVouchers->count() }})
                </button>
                <button @click="activeTab = 'terpakai'" :class="activeTab === 'terpakai' ? 'border-black text-black' : 'border-transparent text-gray-400'" class="pb-4 border-b-2 font-bold uppercase text-xs tracking-widest transition-all">
                    Riwayat ({{ $usedVouchers->count() }})
                </button>
            </div>

            {{-- TAB TERSEDIA --}}
            <div x-show="activeTab === 'tersedia'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6 fade-in">
                @forelse($availableVouchers as $myVoucher)
                    <div class="bg-white border-2 border-black rounded-2xl p-6 flex flex-col justify-between relative overflow-hidden shadow-sm">
                        <div class="absolute w-6 h-6 bg-[#f5f5f5] rounded-full -left-3 top-1/2"></div>
                        <div class="absolute w-6 h-6 bg-[#f5f5f5] rounded-full -right-3 top-1/2"></div>
                        <div class="pl-4">
                            {{-- LOGIKA SNAPSHOT: Mengambil data yang terkunci dari Model UserVoucher --}}
                            @php
                                $lockedCode = $myVoucher->code_snapshot;
                                $lockedDiscount = $myVoucher->discount_snapshot;
                            @endphp

                            <div class="text-xs text-gray-500 font-mono tracking-widest uppercase mb-1">{{ $lockedCode }}</div>
                            <h3 class="text-xl font-bold text-black">{{ $myVoucher->voucher?->name ?? 'Voucher Spesial' }}</h3>
                            <div class="text-3xl font-black text-black mt-4">Rp {{ number_format($lockedDiscount, 0, ',', '.') }}</div>
                            <span class="inline-block mt-4 bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Tersedia</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 py-10 text-center bg-white border border-dashed border-gray-300 rounded-2xl">
                        <p class="text-gray-500 italic">Dompet voucher tersedia Anda kosong. Tukarkan poin Anda di bawah!</p>
                    </div>
                @endforelse
            </div>

            {{-- TAB TERPAKAI --}}
            <div x-show="activeTab === 'terpakai'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6 fade-in">
                @forelse($usedVouchers as $myVoucher)
                    <div class="bg-gray-100 border border-gray-200 rounded-2xl p-6 opacity-60 grayscale relative overflow-hidden">
                        <div class="absolute w-6 h-6 bg-[#f5f5f5] rounded-full -left-3 top-1/2"></div>
                        <div class="absolute w-6 h-6 bg-[#f5f5f5] rounded-full -right-3 top-1/2"></div>
                        <div class="pl-4">
                            @php
                                $lockedCode = $myVoucher->code_snapshot;
                                $lockedDiscount = $myVoucher->discount_snapshot;
                            @endphp

                            <div class="text-xs text-gray-400 font-mono uppercase">{{ $lockedCode }}</div>
                            <h3 class="text-xl font-bold mt-1">{{ $myVoucher->voucher?->name ?? 'Voucher Spesial (Diarsipkan)' }}</h3>
                            <div class="text-3xl font-black mt-4">Rp {{ number_format($lockedDiscount, 0, ',', '.') }}</div>
                            <div class="text-[10px] text-gray-500 mt-4 uppercase font-bold">
                                Digunakan: {{ $myVoucher->used_at ? $myVoucher->used_at->format('d M Y') : '-' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 py-10 text-center bg-white border border-dashed border-gray-300 rounded-2xl">
                        <p class="text-gray-500 italic">Belum ada riwayat penggunaan voucher.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- AREA TUKAR POIN --}}
        <div id="tukar-poin" class="pt-12 border-t border-gray-200">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-black flex items-center gap-2">
                    <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Katalog Tukar Poin
                </h2>
                <p class="text-gray-500 mt-1">Pilih voucher yang ingin Anda tukarkan dengan poin Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($vouchers as $voucher)
                    <div class="bg-white p-6 rounded-2xl border border-gray-200 hover:border-black hover:shadow-lg transition-all flex flex-col h-full relative overflow-hidden group">
                        
                        <div class="absolute top-0 right-0 w-20 h-20 bg-green-50 rounded-bl-full -z-0 group-hover:bg-green-100 transition-colors"></div>

                        <div class="flex-grow relative z-10">
                            <h3 class="font-bold text-lg mb-1 text-gray-800 line-clamp-2">{{ $voucher->name }}</h3>
                            
                            <div class="mt-4 mb-2">
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Nilai Potongan</p>
                                <div class="text-3xl font-black text-green-600 tracking-tight">
                                    Rp {{ number_format($voucher->discount_amount, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 text-xs font-medium text-gray-500 mt-4">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $voucher->valid_until ? 'Berlaku s/d ' . \Carbon\Carbon::parse($voucher->valid_until)->format('d M Y') : 'Tanpa Batas Waktu' }}
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-5 border-t border-gray-100 relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-bold text-gray-500">Harga:</span>
                                <span class="text-blue-600 font-black text-xl bg-blue-50 px-3 py-1 rounded-lg">{{ $voucher->points_required }} Poin</span>
                            </div>
                            
                            <form action="{{ route('customer.vouchers.redeem', $voucher->id) }}" method="POST" class="w-full">
                                @csrf
                                <button type="button" 
                                        data-poin="{{ $voucher->points_required }}" 
                                        data-diskon="{{ number_format($voucher->discount_amount, 0, ',', '.') }}"
                                        {{ $user->points < $voucher->points_required ? 'disabled' : '' }}
                                        class="btn-tukar w-full {{ $user->points >= $voucher->points_required ? 'bg-black text-white hover:bg-gray-800 shadow-md' : 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200' }} py-3.5 rounded-xl text-sm font-bold tracking-wide transition-all active:scale-95">
                                    {{ $user->points >= $voucher->points_required ? 'Tukar Poin Sekarang' : 'Poin Anda Kurang' }}
                                </button>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 py-16 text-center bg-white border border-dashed border-gray-300 rounded-2xl">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4M8 16l-4-4 4-4m8 8l4-4-4-4"></path></svg>
                        <p class="text-gray-500 font-medium">Saat ini belum ada katalog voucher yang tersedia untuk ditukarkan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-tukar').forEach(button => {
        button.addEventListener('click', function () {
            if(this.disabled) return;
            const form = this.closest('form');
            const poin = this.getAttribute('data-poin');
            const diskon = this.getAttribute('data-diskon');

            Swal.fire({
                title: '<span style="font-family: \'Playfair Display\', serif;">Konfirmasi Penukaran</span>',
                html: `<p style="font-family: 'Plus Jakarta Sans', sans-serif; color: #6b7280; font-size: 14px; line-height: 1.6;">
                          Anda akan menukarkan <b style="color: #2563eb;">${poin} Poin</b> untuk mendapatkan voucher diskon senilai <b style="color: #16a34a;">Rp ${diskon}</b>.<br><br>Lanjutkan penukaran?
                       </p>`,
                icon: 'question',
                iconColor: '#e8c9a0',
                showCancelButton: true,
                confirmButtonColor: '#000',
                cancelButtonColor: '#f3f4f6',
                confirmButtonText: 'Ya, Tukar Sekarang',
                cancelButtonText: '<span style="color: #4b5563;">Batal</span>',
                customClass: { 
                    popup: 'rounded-[24px]', 
                    confirmButton: 'rounded-xl px-6 py-3 text-sm font-bold', 
                    cancelButton: 'rounded-xl px-6 py-3 text-sm font-bold' 
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ 
                        title: 'Memproses...', 
                        allowOutsideClick: false, 
                        showConfirmButton: false, 
                        didOpen: () => { Swal.showLoading(); } 
                    });
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
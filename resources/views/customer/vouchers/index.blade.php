@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-[#f5f5f5] text-black py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-12" x-data="{ activeTab: 'tersedia' }">

        <div>
            <div class="flex items-center gap-3 text-sm text-gray-400 mb-6">
                <a href="{{ route('welcome') }}" class="hover:text-black transition-all">Home</a>
                <span>›</span>
                <span class="font-semibold text-black">Vouchers & Points</span>
            </div>

            <h1 class="text-4xl font-bold text-black mb-2 font-playfair">Voucher & Poin Saya</h1>
            <p class="text-gray-500">Tukarkan poin belanja Anda dengan berbagai potongan harga menarik.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl font-medium">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl font-medium">
                ❌ {{ session('error') }}
            </div>
        @endif

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

            <div x-show="activeTab === 'tersedia'" class="grid grid-cols-1 md:grid-cols-2 gap-6 fade-in">
                @forelse($availableVouchers as $myVoucher)
                    <div class="bg-white border-2 border-black rounded-2xl p-6 flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute w-6 h-6 bg-[#f5f5f5] rounded-full -left-3 top-1/2"></div>
                        <div class="absolute w-6 h-6 bg-[#f5f5f5] rounded-full -right-3 top-1/2"></div>
                        <div class="pl-4">
                            <div class="text-xs text-gray-500 font-mono tracking-widest uppercase mb-1">{{ $myVoucher->code }}</div>
                            <h3 class="text-xl font-bold text-black">{{ $myVoucher->name }}</h3>
                            <div class="text-3xl font-black text-black mt-4">Rp {{ number_format($myVoucher->discount_amount, 0, ',', '.') }}</div>
                            <span class="inline-block mt-4 bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Tersedia</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 italic">Dompet voucher tersedia Anda kosong.</p>
                @endforelse
            </div>

            <div x-show="activeTab === 'terpakai'" class="grid grid-cols-1 md:grid-cols-2 gap-6 fade-in">
                @forelse($usedVouchers as $myVoucher)
                    <div class="bg-gray-100 border border-gray-200 rounded-2xl p-6 opacity-60 grayscale">
                        <div class="text-xs text-gray-400 font-mono uppercase">{{ $myVoucher->code }}</div>
                        <h3 class="text-xl font-bold mt-1">{{ $myVoucher->name }}</h3>
                        <div class="text-3xl font-black mt-4">Rp {{ number_format($myVoucher->discount_amount, 0, ',', '.') }}</div>
                        <div class="text-[10px] text-gray-500 mt-4 uppercase font-bold">
                            Digunakan: {{ \Carbon\Carbon::parse($myVoucher->pivot->used_at)->format('d M Y') }}
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 italic">Belum ada riwayat penggunaan voucher.</p>
                @endforelse
            </div>
        </div>

        <div id="tukar-poin" class="pt-12 border-t border-gray-200">
            <h2 class="text-2xl font-bold text-black mb-6">Tukar Poin</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($vouchers as $voucher)
                    <div class="bg-white p-6 rounded-2xl border border-gray-200 hover:border-black transition-all">
                        <h3 class="font-bold text-lg mb-2">{{ $voucher->name }}</h3>
                        <div class="text-blue-600 font-black text-xl mb-6">{{ $voucher->points_required }} Poin</div>
                        <form action="{{ route('customer.vouchers.redeem', $voucher->id) }}" method="POST">
                            @csrf
                            <button type="button" 
                                    data-poin="{{ $voucher->points_required }}" 
                                    {{ $user->points < $voucher->points_required ? 'disabled' : '' }}
                                    class="btn-tukar w-full {{ $user->points >= $voucher->points_required ? 'bg-black text-white hover:bg-gray-800' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }} py-3 rounded-lg text-sm font-bold transition-colors">
                                {{ $user->points >= $voucher->points_required ? 'Tukar Poin' : 'Poin Kurang' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-tukar').forEach(button => {
        button.addEventListener('click', function () {
            if(this.disabled) return;
            const form = this.closest('form');
            const poin = this.getAttribute('data-poin');

            Swal.fire({
                title: '<span style="font-family: \'Playfair Display\', serif;">Konfirmasi Penukaran</span>',
                html: `<p style="font-family: 'Plus Jakarta Sans', sans-serif; color: #6b7280; font-size: 14px;">Tukar <b style="color: #000;">${poin} Poin</b> dengan voucher ini?</p>`,
                icon: 'question',
                iconColor: '#e8c9a0',
                showCancelButton: true,
                confirmButtonColor: '#000',
                cancelButtonColor: '#f3f4f6',
                confirmButtonText: 'Ya, Tukar',
                cancelButtonText: '<span style="color: #000;">Batal</span>',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-3 text-sm font-bold', cancelButton: 'rounded-xl px-6 py-3 text-sm font-bold' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => { Swal.showLoading(); } });
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
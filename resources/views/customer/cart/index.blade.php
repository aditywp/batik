@extends('layouts.customer')

@section('title', 'Shopping Cart — Batik Ifawati')

@section('content')
<div class="min-h-screen bg-[#f8f8f8] pt-24 md:pt-32 pb-20 font-sans text-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 md:mb-12 gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-black italic tracking-tighter uppercase">Your Cart</h1>
                <p class="text-gray-400 text-[9px] md:text-[10px] uppercase tracking-[2px] md:tracking-[3px] mt-2 font-bold italic">Review your exquisite selection</p>
            </div>

            <a href="{{ route('catalog.index') }}"
                class="text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-black transition-all border-b border-transparent hover:border-black pb-1 w-max">
                Continue Shopping
            </a>
        </div>

        @if($cartItems->isEmpty())
            {{-- STATE KOSONG --}}
            <div class="bg-white rounded-[32px] md:rounded-[40px] py-16 md:py-24 px-6 text-center border border-gray-100 shadow-sm mx-auto max-w-2xl">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <p class="text-gray-400 text-xs uppercase tracking-widest mb-8 italic">Your cart is currently empty.</p>
                <a href="{{ route('catalog.index') }}"
                    class="inline-block bg-black text-white px-8 md:px-12 py-4 md:py-5 rounded-xl md:rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] md:tracking-[0.3em] hover:bg-orange-500 transition-all shadow-xl active:scale-95">
                    Explore Collection
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10">

                {{-- DAFTAR ITEM KERANJANG --}}
                <div class="lg:col-span-8 space-y-5 md:space-y-6">
                    @foreach($cartItems as $item)
                        @php
                            $price = $item->variant->price ?? $item->product->price;
                            $subtotalItem = $price * $item->quantity;
                            $imagePath = $item->variant->image_path ?? ($item->product->variants->first()->image_path ?? null);
                        @endphp

                        {{-- KARTU ITEM (RESPONSIVE: Stack di HP, Row di Desktop) --}}
                        <div class="bg-white rounded-[24px] md:rounded-[32px] p-5 md:p-8 border border-gray-100 shadow-sm flex flex-col sm:flex-row gap-5 md:gap-8 items-start transition-all hover:shadow-md relative group overflow-hidden">

                            {{-- LINK IMAGES --}}
                            <a href="{{ route('catalog.show', $item->product->slug) }}" 
                               class="w-full sm:w-28 h-48 sm:h-36 bg-[#0a0a0a] flex-shrink-0 overflow-hidden rounded-xl md:rounded-2xl border border-gray-50 shadow-inner block relative group/img">
                                <img
                                    src="{{ $imagePath ? asset('storage/' . $imagePath) : asset('images/placeholder.jpg') }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover/img:scale-110"
                                    alt="{{ $item->product->name }}"
                                >
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-white text-[9px] font-black uppercase tracking-widest bg-black/60 px-2 py-1 rounded-md">View</span>
                                </div>
                            </a>

                            <div class="flex-grow w-full flex flex-col justify-between self-stretch py-1">
                                <div class="flex flex-col md:flex-row justify-between items-start gap-3 md:gap-4">
                                    <div class="min-w-0 w-full">
                                        {{-- LINK TEXT --}}
                                        <h3 class="text-sm md:text-base font-black uppercase tracking-tight text-black mb-1 md:mb-2 leading-tight truncate">
                                            <a href="{{ route('catalog.show', $item->product->slug) }}" class="hover:text-orange-500 hover:underline transition-colors block truncate">
                                                {{ $item->product->name }}
                                            </a>
                                        </h3>
                                        
                                        <div class="flex flex-wrap gap-3 md:gap-5 mb-3 md:mb-4">
                                            <p class="text-[9px] md:text-[10px] text-orange-600 font-black uppercase tracking-widest italic">
                                                Motif: {{ $item->variant->motif ?? 'Standard' }}
                                            </p>
                                            <p class="text-[9px] md:text-[10px] text-gray-400 font-black uppercase tracking-widest italic">
                                                Size: {{ $item->variant->size ?? '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Desktop Price (Sembunyi di HP) --}}
                                    <div class="hidden md:block text-right flex-shrink-0">
                                        <p class="text-[10px] text-gray-300 font-black uppercase tracking-widest mb-1 italic">Price</p>
                                        <p class="text-sm font-black text-black italic whitespace-nowrap">
                                            Rp {{ number_format($price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-end justify-between mt-auto gap-4">
                                    {{-- AKSI PENGATUR QUANTITY & REMOVE --}}
                                    <div class="flex items-center gap-4 md:gap-6 w-full sm:w-auto justify-between sm:justify-start">
                                        <div class="flex items-center bg-gray-50 rounded-xl p-1 border border-gray-100 flex-shrink-0">
                                            <form action="{{ route('customer.cart.update', $item->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="decrease">
                                                <button type="submit" 
                                                    class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center text-gray-400 hover:text-black hover:bg-white rounded-lg transition-all font-bold text-sm @if($item->quantity <= 1) opacity-20 cursor-not-allowed @endif"
                                                    @if($item->quantity <= 1) disabled @endif>-</button>
                                            </form>

                                            <form action="{{ route('customer.cart.update', $item->id) }}" method="POST" class="form-update-qty m-0 p-0">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="manual">
                                                {{-- PERBAIKAN: Menambahkan max="1000" dan membatasi pengetikan lebih dari 4 digit --}}
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="1000" data-original="{{ $item->quantity }}"
                                                    oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);"
                                                    class="input-qty w-10 md:w-12 bg-transparent text-center text-xs font-black text-black focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                            </form>

                                            <form action="{{ route('customer.cart.update', $item->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="action" value="increase">
                                                <button type="submit" class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center text-gray-400 hover:text-black hover:bg-white rounded-lg transition-all font-bold text-sm">+</button>
                                            </form>
                                        </div>

                                        <form action="{{ route('customer.cart.remove', $item->id) }}" method="POST" class="form-remove-item">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn-remove-trigger flex items-center gap-1.5 text-red-600 font-black uppercase tracking-widest transition-opacity hover:opacity-70 px-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span class="text-[9px] md:text-xs">Remove</span>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Desktop Subtotal (Sembunyi di HP) --}}
                                    <div class="hidden md:block">
                                        <p class="font-black text-2xl text-black italic tracking-tighter leading-none whitespace-nowrap">
                                            Rp {{ number_format($subtotalItem, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- MOBILE PRICE & SUBTOTAL (Tampil KHUSUS di HP) --}}
                                <div class="flex md:hidden justify-between items-end mt-5 pt-4 border-t border-gray-50">
                                    <div>
                                        <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-0.5 italic">Price</p>
                                        <p class="text-xs font-black text-black italic">Rp {{ number_format($price, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-0.5 italic">Subtotal</p>
                                        <p class="font-black text-lg text-black italic tracking-tighter leading-none">Rp {{ number_format($subtotalItem, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- RINGKASAN ORDER / CHECKOUT CARD --}}
                <div class="lg:col-span-4 mt-6 lg:mt-0">
                    <div class="bg-white rounded-[32px] md:rounded-[40px] p-6 md:p-8 border border-gray-100 shadow-xl sticky top-24 md:top-32">
                        <h2 class="text-[10px] font-black uppercase tracking-[4px] text-orange-500 mb-8 md:mb-10 italic text-center lg:text-left">Order Summary</h2>
                        
                        <div class="flex justify-between items-end mb-8 md:mb-10">
                            <span class="text-[9px] md:text-[10px] font-black uppercase tracking-widest text-gray-400 italic">Total</span>
                            <span class="text-2xl md:text-3xl font-black text-black italic tracking-tighter">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <a href="{{ route('customer.checkout.index') }}" 
                           class="block w-full bg-black text-white py-4 md:py-5 rounded-xl md:rounded-2xl text-center text-[9px] md:text-[10px] font-black uppercase tracking-[0.2em] md:tracking-[0.3em] hover:bg-orange-600 transition-all shadow-xl active:scale-95">
                            Proceed to Checkout
                        </a>

                        <div class="mt-6 md:mt-8 text-center">
                            <p class="text-[8px] md:text-[9px] text-gray-400 uppercase tracking-widest leading-relaxed">
                                Shipping and taxes calculated at checkout.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>
</div>

<style>
    /* Styling for Luxury Feel */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #000; }
</style>

{{-- SCRIPT PENDUKUNG UNTUK SWEETALERT --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {

        // Cegah submit otomatis saat tombol "Enter" ditekan
        $('.form-update-qty').on('submit', function(e) {
            e.preventDefault(); 
            $(this).find('.input-qty').blur(); // Alihkan fokus agar memicu event 'change' di bawah ini
        });

        // Trigger validasi saat admin selesai mengetik (kehilangan fokus/klik di luar)
        $('.input-qty').on('change', function() {
            let input = $(this);
            let val = parseInt(input.val());
            let original = input.data('original');
            let form = input.closest('form');

            // 1. Jika nilainya 0, minus, atau kosong
            if (isNaN(val) || val <= 0) {
                input.val(original); // Kembalikan ke angka awal
                Swal.fire({
                    icon: 'warning',
                    title: '<span style="font-family: \'Playfair Display\', serif; font-style: italic;">Invalid Quantity</span>',
                    html: '<p style="font-family: \'Plus Jakarta Sans\', sans-serif; color: #6b7280; font-size: 13px;">Jumlah barang tidak dapat diatur ke 0. Jika Anda ingin membatalkan pesanan ini, mohon gunakan tombol <b style="color:#ef4444">Remove</b>.</p>',
                    confirmButtonColor: '#000',
                    confirmButtonText: 'Mengerti',
                    customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl px-8 py-3 text-xs font-black tracking-widest uppercase' }
                });
            } 
            // 2. PERBAIKAN: Jika nilainya lebih dari 1000
            else if (val > 1000) {
                input.val(original); // Kembalikan ke angka awal
                Swal.fire({
                    icon: 'warning',
                    title: '<span style="font-family: \'Playfair Display\', serif; font-style: italic;">Limit Exceeded</span>',
                    html: '<p style="font-family: \'Plus Jakarta Sans\', sans-serif; color: #6b7280; font-size: 13px;">Maaf, Anda hanya dapat memesan maksimal <b>1.000 unit</b> per item.</p>',
                    confirmButtonColor: '#000',
                    confirmButtonText: 'Mengerti',
                    customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl px-8 py-3 text-xs font-black tracking-widest uppercase' }
                });
            }
            // 3. Jika valid dan berubah nilainya
            else if (val !== original) {
                Swal.fire({ title: 'Memperbarui...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => { Swal.showLoading(); } });
                form[0].submit(); 
            }
        });

        // FUNGSI KONFIRMASI HAPUS ITEM (REMOVE)
        $('.btn-remove-trigger').on('click', function(e) {
            e.preventDefault();
            let form = $(this).closest('.form-remove-item');

            Swal.fire({
                title: '<span style="font-family: \'Playfair Display\', serif; font-style: italic;">Remove Item?</span>',
                html: '<p style="font-family: \'Plus Jakarta Sans\', sans-serif; color: #6b7280; font-size: 13px;">Item ini akan dihapus dari keranjang belanja Anda.</p>',
                icon: 'warning',
                iconColor: '#ef4444',
                showCancelButton: true,
                confirmButtonColor: '#000',
                cancelButtonColor: '#f3f4f6',
                confirmButtonText: 'Yes, Remove',
                cancelButtonText: '<span style="color: #000;">Cancel</span>',
                customClass: { 
                    popup: 'rounded-[32px]', 
                    confirmButton: 'rounded-xl px-6 py-3 text-[10px] font-black tracking-[2px] uppercase', 
                    cancelButton: 'rounded-xl px-6 py-3 text-[10px] font-black tracking-[2px] uppercase' 
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => { Swal.showLoading(); } });
                    form.submit();
                }
            });
        });

        // FLASH TOAST (BERHASIL)
        @if(session('success'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        // FLASH ALERT BACKEND (ERROR)
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: '<span style="font-family: \'Playfair Display\', serif; font-style: italic;">Oops!</span>',
                text: "{{ session('error') }}",
                confirmButtonColor: '#000',
                customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl px-8 py-3 text-xs font-black' }
            });
        @endif
    });
</script>
@endsection
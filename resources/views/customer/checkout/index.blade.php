@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-[#f5f5f5] text-black">
    <div class="max-w-7xl mx-auto flex flex-col-reverse lg:grid lg:grid-cols-2 min-h-screen text-black">

        {{-- BAGIAN KIRI: FORM PENGIRIMAN --}}
        <div class="bg-white px-6 py-10 sm:px-8 lg:px-16 lg:py-12 border-r border-gray-200">
            <div class="mb-6 md:mb-8">
                <h1 class="text-3xl md:text-5xl font-black tracking-[8px] md:tracking-[12px] text-black">BATIK IFAWATI</h1>
            </div>

            <div class="flex flex-wrap items-center gap-2 md:gap-3 text-xs md:text-sm text-gray-400 mb-8 md:mb-10">
                <a href="{{ route('customer.cart.index') }}" class="hover:text-black transition-all">Cart</a>
                <span>›</span>
                <span class="font-semibold text-black">Information & Shipping</span>
                <span>›</span>
                <span class="text-gray-300">Payment</span>
            </div>

            <div class="mb-8 md:mb-10">
                <h2 class="text-2xl md:text-3xl font-bold mb-3 md:mb-5 text-black">Contact</h2>
                <div class="text-sm md:text-base text-gray-700 bg-gray-50 px-4 py-3 rounded-lg border border-gray-100">
                    <span class="font-bold">{{ auth()->user()->name }}</span> <br class="md:hidden"> 
                    <span class="text-gray-500">({{ auth()->user()->email }})</span>
                </div>
            </div>

            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-4 md:mb-6 text-black">Shipping address</h2>
                
                <div class="space-y-4 md:space-y-5">
                    <div>
                        <label class="block text-xs md:text-sm font-medium mb-1.5 md:mb-2 text-black">Nama Penerima</label>
                        <input type="text" id="receiver_name" value="{{ auth()->user()->name }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 md:py-4 text-sm md:text-base text-black bg-white focus:outline-none focus:ring-2 focus:ring-black transition-shadow"
                            placeholder="Nama penerima">
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium mb-1.5 md:mb-2 text-black">Nomor Telepon (Awali dengan angka 8)</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 md:px-4 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-bold">
                                +62
                            </span>
                            <input type="text" id="phone"
                                maxlength="14"
                                oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value.startsWith('0')) this.value = this.value.substring(1);"
                                class="w-full border border-gray-300 rounded-r-lg px-3 md:px-4 py-3 md:py-4 text-sm md:text-base text-black bg-white focus:outline-none focus:ring-2 focus:ring-black transition-shadow"
                                placeholder="812xxxx (Tanpa 0 di depan)">
                        </div>
                        <p id="error-phone" class="text-red-500 text-xs mt-1.5 font-medium hidden error-text"></p>
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium mb-1.5 md:mb-2 text-black">Provinsi</label>
                        <select id="shipping_province"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 md:py-4 text-sm md:text-base text-black bg-white focus:outline-none focus:ring-2 focus:ring-black transition-shadow truncate">
                            <option value="" disabled selected>Pilih Provinsi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium mb-1.5 md:mb-2 text-black">Kota / Kabupaten</label>
                        <select id="shipping_city" disabled
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 md:py-4 text-sm md:text-base text-black bg-white focus:outline-none focus:ring-2 focus:ring-black transition-shadow truncate">
                            <option value="" disabled selected>Pilih Kota</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium mb-1.5 md:mb-2 text-black">Kecamatan</label>
                        <select id="shipping_district" disabled
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 md:py-4 text-sm md:text-base text-black bg-white focus:outline-none focus:ring-2 focus:ring-black transition-shadow truncate">
                            <option value="" disabled selected>Pilih Kecamatan</option>
                        </select>
                        <p id="error-district" class="text-red-500 text-xs mt-1.5 font-medium hidden error-text"></p>
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium mb-1.5 md:mb-2 text-black">Kode Pos (5-6 Digit)</label>
                        <input type="text" id="postal_code"
                            minlength="5" maxlength="6"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 md:py-4 text-sm md:text-base text-black bg-white focus:outline-none focus:ring-2 focus:ring-black transition-shadow"
                            placeholder="Kode Pos">
                        <p id="error-postal" class="text-red-500 text-xs mt-1.5 font-medium hidden error-text"></p>
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium mb-1.5 md:mb-2 text-black">Alamat Lengkap</label>
                        <textarea id="shipping_address" rows="3" placeholder="Nama jalan, RT/RW, nomor rumah..."
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 md:py-4 text-sm md:text-base text-black bg-white focus:outline-none focus:ring-2 focus:ring-black transition-shadow"></textarea>
                        <p id="error-address" class="text-red-500 text-xs mt-1.5 font-medium hidden error-text"></p>
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium mb-1.5 md:mb-2 text-black">Opsi Pengiriman</label>
                        <div id="cheapest_delivery_info" class="w-full border border-dashed border-gray-300 bg-gray-50 rounded-lg p-4 text-xs md:text-sm text-gray-500 italic">
                            Silakan lengkapi wilayah pengiriman untuk melihat biaya kurir...
                        </div>
                        <p id="error-courier" class="text-red-500 text-xs mt-1.5 font-medium hidden error-text"></p>
                    </div>

                    <div id="error-general" class="mt-4 text-red-600 text-sm font-bold hidden error-text bg-red-50 p-3 rounded-lg border border-red-100"></div>

                    <div class="pt-6 md:pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                        <a href="{{ route('customer.cart.index') }}" class="text-sm text-gray-500 hover:text-black order-2 md:order-1 font-medium">← Return to cart</a>
                        <button id="checkout-button"
                            class="w-full md:w-auto order-1 md:order-2 bg-black hover:bg-gray-800 text-white px-8 md:px-10 py-4 rounded-xl font-bold tracking-wide transition-all shadow-lg active:scale-95">
                            Continue to payment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN KANAN: RINGKASAN KERANJANG --}}
        <div class="bg-[#fafafa] px-6 py-10 sm:px-8 lg:px-12 lg:py-12 border-b md:border-b-0 border-gray-200">
            <div class="space-y-6 md:space-y-8">
                @php $subtotal = 0; @endphp
                @foreach($cartItems as $item)
                    @php
                        $itemTotal = ($item->variant->price ?? $item->product->price) * $item->quantity;
                        $subtotal += $itemTotal;
                    @endphp
                    <div class="flex justify-between items-start gap-3 md:gap-4">
                        <div class="flex gap-3 md:gap-4">
                            <div class="relative w-16 h-16 md:w-20 md:h-20 bg-white border rounded-lg md:rounded-xl overflow-hidden flex-shrink-0 shadow-sm">
                                <img src="{{ asset('storage/' . ($item->variant->image_path ?? 'placeholder.jpg')) }}" class="w-full h-full object-cover">
                                <span class="absolute -top-1.5 -right-1.5 bg-black text-white text-[9px] md:text-[10px] w-4 h-4 md:w-5 md:h-5 rounded-full flex items-center justify-center font-bold">{{ $item->quantity }}</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-xs md:text-sm leading-snug max-w-[180px] md:max-w-[230px] text-black">{{ $item->product->name }}</h3>
                                <p class="text-[10px] md:text-xs text-gray-400 mt-1 font-medium">
                                    {{ $item->product->category->name ?? 'Batik' }} 
                                    @if($item->variant) • {{ $item->variant->motif }} ({{ $item->variant->size }}) @endif
                                </p>
                            </div>
                        </div>
                        <div class="font-black text-xs md:text-sm whitespace-nowrap text-black">Rp {{ number_format($itemTotal,0,',','.') }}</div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 md:mt-10 border-t border-gray-300 pt-6 md:pt-8 space-y-3 md:space-y-4">
                
                <div class="mb-4 md:mb-6">
                    <label class="block text-xs md:text-sm font-bold mb-2 text-black">Punya Voucher Diskon?</label>
                <select id="user_voucher_id" class="w-full border border-gray-300 rounded-xl px-3 py-3 md:px-4 md:py-3.5 text-sm text-black bg-white focus:outline-none focus:ring-2 focus:ring-black transition-shadow truncate">
                    <option value="" data-discount="0">Tidak memakai voucher</option>
                    @if(isset($myActiveVouchers) && $myActiveVouchers->count() > 0)
                        @foreach($myActiveVouchers as $voucher)
                            <option value="{{ $voucher->id }}" data-discount="{{ (int)$voucher->discount_snapshot }}">
                                {{ $voucher->voucher?->name ?? 'Voucher Spesial' }} (Potongan Rp {{ number_format($voucher->discount_snapshot, 0, ',', '.') }})
                            </option>
                        @endforeach
                    @endif
                </select>
                </div>

                <div class="flex justify-between text-gray-600 text-sm font-medium"><span>Subtotal</span><span>Rp {{ number_format($subtotal,0,',','.') }}</span></div>
                <div class="flex justify-between text-gray-600 text-sm font-medium"><span>Shipping</span><span id="shipping-cost">Rp 0</span></div>
                
                <div class="flex justify-between text-green-600 text-sm font-bold hidden bg-green-50 p-2 rounded-lg -mx-2 px-2" id="voucher-discount-row">
                    <span>Discount Voucher</span>
                    <span id="voucher-discount-amount">- Rp 0</span>
                </div>

                <div class="border-t border-gray-300 pt-5 md:pt-6 flex justify-between items-end mt-2 md:mt-4">
                    <div><span class="text-xl md:text-2xl font-black text-black">Total</span></div>
                    <div class="text-right">
                        <div class="text-[9px] md:text-xs text-gray-400 font-bold uppercase tracking-widest mb-0.5">IDR</div>
                        <div id="grand-total" data-subtotal="{{ $subtotal }}" class="text-2xl md:text-4xl font-black text-black tracking-tight">Rp {{ number_format($subtotal,0,',','.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    let selectedCourierName = "";
    let selectedCourierService = "";
    let selectedCourierCost = 0;
    let selectedVoucherDiscount = 0;
    let selectedVoucherId = "";

    $.get("{{ route('api.provinces') }}", function (data) {
        $.each(data, function (key, value) {
            if(value && value.id) $('#shipping_province').append('<option value="' + value.id + '">' + value.name + '</option>');
        });
    });

    $('#shipping_province').change(function () {
        let provinceId = $(this).val();
        $('#shipping_city').empty().append('<option value="" disabled selected>Pilih Kota</option>').prop('disabled', true);
        $('#shipping_district').empty().append('<option value="" disabled selected>Pilih Kecamatan</option>').prop('disabled', true);
        resetShippingCost();
        
        if (provinceId) {
            $.get("/api/cities/" + provinceId, function (data) {
                $('#shipping_city').prop('disabled', false);
                $.each(data, function (key, value) {
                    if(value && value.id) $('#shipping_city').append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            });
        }
    });

    $('#shipping_city').change(function () {
        let cityId = $(this).val();
        $('#shipping_district').empty().append('<option value="" disabled selected>Pilih Kecamatan</option>').prop('disabled', true);
        resetShippingCost();
        
        if (cityId) {
            $.get("/api/districts/" + cityId, function (data) {
                $('#shipping_district').prop('disabled', false);
                $.each(data, function (key, value) {
                    if(value && value.id) $('#shipping_district').append('<option value="' + value.id + '" data-postal="' + (value.postal_code || '') + '">' + value.name + '</option>');
                });
            });
        }
    });

    $('#shipping_district').change(function () {
        let postalCode = $(this).find(':selected').data('postal');
        if (postalCode) $('#postal_code').val(postalCode);
        let districtId = $(this).val();
        if (districtId) fetchCheapestShipping(districtId); else resetShippingCost();
    });

    function fetchCheapestShipping(districtId) {
        $('#cheapest_delivery_info').html('⏳ Mencari opsi pengiriman termurah...');
        $.ajax({
            url: "{{ route('api.check-cost') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}", district_id: districtId, weight: 1000 },
            success: function (data) {
                if (data && data.length > 0) {
                    let s = data[0]; 
                    selectedCourierName = s.courier_name || s.name || 'Kurir';
                    selectedCourierService = s.service || 'REG';
                    selectedCourierCost = parseInt(s.cost) || 0;
                    $('#cheapest_delivery_info').html(`<div class="p-3 md:p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm"><span class="font-black">✅ ${selectedCourierName} - ${selectedCourierService}</span><br><span class="font-medium text-xs mt-1 block">Biaya: Rp ${selectedCourierCost.toLocaleString('id-ID')}</span></div>`);
                    updateGrandTotal();
                } else {
                    $('#cheapest_delivery_info').html('❌ Tidak ada kurir yang melayani rute ini.');
                    resetShippingCost();
                }
            }
        });
    }

    function resetShippingCost() {
        selectedCourierName = ""; selectedCourierService = ""; selectedCourierCost = 0;
        $('#cheapest_delivery_info').html('Silakan lengkapi wilayah pengiriman untuk melihat biaya kurir...');
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let subtotal = parseInt($('#grand-total').data('subtotal'));
        let finalTotal = (subtotal + selectedCourierCost) - selectedVoucherDiscount;
        if (finalTotal < 0) finalTotal = 0; 

        $('#shipping-cost').text('Rp ' + selectedCourierCost.toLocaleString('id-ID'));
        $('#grand-total').text('Rp ' + finalTotal.toLocaleString('id-ID'));
    }

    $('#user_voucher_id').change(function() {
        let selectedOption = $(this).find('option:selected');
        selectedVoucherId = $(this).val();
        selectedVoucherDiscount = parseInt(selectedOption.data('discount')) || 0;

        if (selectedVoucherDiscount > 0) {
            $('#voucher-discount-amount').text('- Rp ' + selectedVoucherDiscount.toLocaleString('id-ID'));
            $('#voucher-discount-row').removeClass('hidden');
        } else {
            $('#voucher-discount-row').addClass('hidden');
        }
        updateGrandTotal();
    });

    $('input, textarea, select').on('input change', function() {
        $(this).closest('div').find('.error-text').addClass('hidden');
        $('#error-general').addClass('hidden');
    });

    $('#checkout-button').click(function () {
        let rawPhone = $('#phone').val().replace(/\D/g, ''); 
        let postal = $('#postal_code').val();
        let detailAddress = $('#shipping_address').val();
        let district = $('#shipping_district').val();
        let isValid = true;

        $('.error-text').addClass('hidden').text('');

        if (rawPhone.startsWith('0')) {
            rawPhone = rawPhone.substring(1); 
        }

        if (!rawPhone.startsWith('8')) {
            $('#error-phone').removeClass('hidden').text('Nomor telepon harus valid (contoh: 812...).');
            isValid = false;
        } else if (rawPhone.length < 9 || rawPhone.length > 13) { 
            $('#error-phone').removeClass('hidden').text('Panjang nomor telepon tidak valid.'); 
            isValid = false;
        }
        
        if (!district) {
            $('#error-district').removeClass('hidden').text('Kecamatan wajib dipilih.'); 
            isValid = false;
        }

        if (postal.length < 5 || postal.length > 6) { 
            $('#error-postal').removeClass('hidden').text('Kode pos harus 5-6 angka.'); 
            isValid = false;
        }
        
        if (!detailAddress.trim()) {
            $('#error-address').removeClass('hidden').text('Alamat lengkap tidak boleh kosong.'); 
            isValid = false;
        }
        
        if (selectedCourierCost === 0) {
            $('#error-courier').removeClass('hidden').text('Sistem belum menghitung biaya pengiriman.');
            isValid = false;
        }

        if (!isValid) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        let finalPhone = "+62" + rawPhone;
        let formattedFullAddress = detailAddress + ", Kec. " + $('#shipping_district option:selected').text() + ", " + $('#shipping_city option:selected').text() + ", " + $('#shipping_province option:selected').text() + " (" + postal + ") — Telp: " + finalPhone;

        let btn = $(this);
        let originalText = btn.text();
        btn.text('Memproses...').prop('disabled', true);

        Swal.fire({ 
            title: 'Memproses Pesanan...', 
            allowOutsideClick: false, 
            showConfirmButton: false, 
            didOpen: () => { Swal.showLoading(); } 
        });

        $.ajax({
            url: "{{ route('customer.checkout.process') }}",
            type: "POST",
            data: { 
                _token: "{{ csrf_token() }}", 
                shipping_address: formattedFullAddress, 
                courier: selectedCourierName, 
                courier_service: selectedCourierService, 
                shipping_cost: selectedCourierCost,
                user_voucher_id: selectedVoucherId 
            },
            success: function (response) {
                Swal.close(); // Tutup loading agar X bisa ditekan

                if (response.is_free) {
                    window.location.href = "/checkout/finish?order_id=" + response.order_code;
                    return; 
                }

                if (response.snap_token) {
                    snap.pay(response.snap_token, {
                        onSuccess: function(result) { 
                            let paymentType = result.payment_type ? result.payment_type : '';
                            window.location.href = "/checkout/finish?order_id=" + response.order_code + "&payment_type=" + paymentType; 
                        },
                        onPending: function(result) { 
                            let paymentType = result.payment_type ? result.payment_type : '';
                            window.location.href = "/checkout/finish?order_id=" + response.order_code + "&payment_type=" + paymentType; 
                        },
                        onError: function(result) { 
                            window.location.href = "/checkout/finish?order_id=" + response.order_code; 
                        },
                        onClose: function() {
                            Swal.fire({
                                title: 'Pembayaran Tertunda',
                                text: 'Pesanan Anda telah disimpan. Silakan lanjutkan pembayaran melalui halaman nota.',
                                icon: 'info',
                                showConfirmButton: false,
                                timer: 2500
                            }).then(() => {
                                window.location.href = "/checkout/finish?order_id=" + response.order_code;
                            });
                        }
                    });
                }
            },
            error: function (xhr) {
                Swal.close();
                $('#error-general').removeClass('hidden').text('Sistem gagal memproses pesanan. Pastikan keranjang Anda tidak kosong.');
                btn.text(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
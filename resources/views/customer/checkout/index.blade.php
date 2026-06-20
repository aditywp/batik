@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-[#f5f5f5] text-black">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 min-h-screen text-black">

        <div class="bg-white px-8 lg:px-16 py-10 border-r border-gray-200">
            <div class="mb-6">
                <h1 class="text-5xl font-black tracking-[14px] text-black">BATIK IFAWATI</h1>
            </div>

            <div class="flex items-center gap-3 text-sm text-gray-400 mb-10">
                <a href="{{ route('customer.cart.index') }}" class="hover:text-black transition-all">Cart</a>
                <span>›</span>
                <span class="font-semibold text-black">Information & Shipping</span>
                <span>›</span>
                <span class="text-gray-300">Payment</span>
            </div>

            <div class="mb-10">
                <h2 class="text-3xl font-bold mb-5 text-black">Contact</h2>
                <div class="text-gray-700">
                    {{ auth()->user()->name }} ({{ auth()->user()->email }})
                </div>
            </div>

            <div>
                <h2 class="text-3xl font-bold mb-6 text-black">Shipping address</h2>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">Nama Penerima</label>
                        <input type="text" id="receiver_name" value="{{ auth()->user()->name }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black"
                            placeholder="Nama penerima">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">Nomor Telepon</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-bold">
                                +62
                            </span>
                            <input type="text" id="phone"
                                minlength="9" maxlength="12"
                                onkeypress="if(this.value.length === 0 && event.key === '0') return false; return event.charCode >= 48 && event.charCode <= 57;"
                                class="w-full border border-gray-300 rounded-r-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black"
                                placeholder="8xxxxxxxxxx">
                        </div>
                        <p id="error-phone" class="text-red-500 text-xs mt-1 font-medium hidden error-text"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">Provinsi</label>
                        <select id="shipping_province"
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="">Pilih Provinsi Tujuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">Kota / Kabupaten</label>
                        <select id="shipping_city" disabled
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="">Pilih Kota Tujuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">Kecamatan</label>
                        <select id="shipping_district" disabled
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="">Pilih Kecamatan Tujuan</option>
                        </select>
                        <p id="error-district" class="text-red-500 text-xs mt-1 font-medium hidden error-text"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">Kode Pos (5-6 Digit)</label>
                        <input type="text" id="postal_code"
                            minlength="5" maxlength="6"
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black"
                            placeholder="Kode Pos">
                        <p id="error-postal" class="text-red-500 text-xs mt-1 font-medium hidden error-text"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">Alamat Lengkap</label>
                        <textarea id="shipping_address" rows="4" placeholder="Nama jalan, RT/RW, nomor rumah..."
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black"></textarea>
                        <p id="error-address" class="text-red-500 text-xs mt-1 font-medium hidden error-text"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">Opsi Pengiriman</label>
                        <div id="cheapest_delivery_info" class="w-full border border-dashed border-gray-300 bg-gray-50 rounded-lg p-4 text-sm text-gray-500 italic">
                            Silakan lengkapi wilayah pengiriman...
                        </div>
                        <p id="error-courier" class="text-red-500 text-xs mt-1 font-medium hidden error-text"></p>
                    </div>

                    <div id="error-general" class="mt-4 text-red-600 text-sm font-bold hidden error-text"></div>

                    <div class="pt-4 flex items-center justify-between">
                        <a href="{{ route('customer.cart.index') }}" class="text-sm text-gray-500 hover:text-black">← Return to cart</a>
                        <button id="checkout-button"
                            class="bg-black hover:bg-gray-900 text-white px-10 py-4 rounded-lg font-semibold transition-all">
                            Continue to payment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#fafafa] px-8 lg:px-12 py-10">
            <div class="space-y-8">
                @php $subtotal = 0; @endphp
                @foreach($cartItems as $item)
                    @php
                        $itemTotal = ($item->variant->price ?? $item->product->price) * $item->quantity;
                        $subtotal += $itemTotal;
                    @endphp
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex gap-4">
                            <div class="relative w-20 h-20 bg-white border rounded-lg overflow-hidden flex-shrink-0">
                                <img src="{{ asset('storage/' . ($item->variant->image_path ?? 'placeholder.jpg')) }}" class="w-full h-full object-cover">
                                <span class="absolute -top-2 -right-2 bg-black text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center">{{ $item->quantity }}</span>
                            </div>
                            <div>
                                <h3 class="font-medium text-sm leading-5 max-w-[230px] text-black">{{ $item->product->name }}</h3>
                                <p class="text-sm text-gray-400 mt-1">
                                    {{ $item->product->category->name ?? 'Batik' }} 
                                    @if($item->variant) • {{ $item->variant->motif }} ({{ $item->variant->size }}) @endif
                                </p>
                            </div>
                        </div>
                        <div class="font-medium whitespace-nowrap text-black">Rp {{ number_format($itemTotal,0,',','.') }}</div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 border-t border-gray-300 pt-8 space-y-4">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 text-black">Punya Voucher Diskon?</label>
                    <select id="user_voucher_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="" data-discount="0">Tidak memakai voucher</option>
                        @if(isset($myActiveVouchers) && $myActiveVouchers->count() > 0)
                            @foreach($myActiveVouchers as $voucher)
                                <option value="{{ $voucher->pivot->id }}" data-discount="{{ $voucher->discount_amount }}">
                                    {{ $voucher->name }} (Potongan Rp {{ number_format($voucher->discount_amount, 0, ',', '.') }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="flex justify-between text-gray-700 mt-4"><span>Subtotal</span><span>Rp {{ number_format($subtotal,0,',','.') }}</span></div>
                <div class="flex justify-between text-gray-700"><span>Shipping</span><span id="shipping-cost">Rp 0</span></div>
                
                <div class="flex justify-between text-green-600 font-medium hidden" id="voucher-discount-row">
                    <span>Discount Voucher</span>
                    <span id="voucher-discount-amount">- Rp 0</span>
                </div>

                <div class="border-t border-gray-300 pt-6 flex justify-between items-center">
                    <div><span class="text-3xl font-bold text-black">Total</span></div>
                    <div class="text-right">
                        <div class="text-xs text-gray-400 uppercase">IDR</div>
                        <div id="grand-total" data-subtotal="{{ $subtotal }}" class="text-4xl font-black text-black">Rp {{ number_format($subtotal,0,',','.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

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
        $('#shipping_city').empty().append('<option value="">Pilih Kota Tujuan</option>').prop('disabled', true);
        $('#shipping_district').empty().append('<option value="">Pilih Kecamatan Tujuan</option>').prop('disabled', true);
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
        $('#shipping_district').empty().append('<option value="">Pilih Kecamatan Tujuan</option>').prop('disabled', true);
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
                    $('#cheapest_delivery_info').html(`<div class="p-4 bg-green-50 border border-green-300 rounded-lg text-green-700">✅ <strong>${selectedCourierName} - ${selectedCourierService}</strong><br>Biaya: <strong>Rp ${selectedCourierCost.toLocaleString('id-ID')}</strong></div>`);
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
        $('#cheapest_delivery_info').html('Silakan lengkapi wilayah pengiriman...');
        updateGrandTotal();
    }

    // Fungsi Kalkulasi Total
    function updateGrandTotal() {
        let subtotal = parseInt($('#grand-total').data('subtotal'));
        let finalTotal = (subtotal + selectedCourierCost) - selectedVoucherDiscount;
        if (finalTotal < 0) finalTotal = 0; // Cegah hasil minus

        $('#shipping-cost').text('Rp ' + selectedCourierCost.toLocaleString('id-ID'));
        $('#grand-total').text('Rp ' + finalTotal.toLocaleString('id-ID'));
    }

    // Deteksi jika User memilih Voucher
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
        let rawPhone = $('#phone').val();
        let postal = $('#postal_code').val();
        let detailAddress = $('#shipping_address').val();
        let district = $('#shipping_district').val();
        
        let isValid = true;

        $('.error-text').addClass('hidden').text('');

        if (!rawPhone.startsWith('8')) {
            $('#error-phone').removeClass('hidden').text('Nomor telepon harus dimulai dengan angka 8 (contoh: 812...).');
            isValid = false;
        } else if (rawPhone.length < 9 || rawPhone.length > 12) { 
            $('#error-phone').removeClass('hidden').text('Nomor telepon harus berisi 9-12 angka.'); 
            isValid = false;
        }
        
        if (!district) {
            $('#error-district').removeClass('hidden').text('Kecamatan harus dipilih.'); 
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
            $('#error-courier').removeClass('hidden').text('Opsi pengiriman belum tersedia/dipilih.');
            isValid = false;
        }

        if (!isValid) return; 

        let finalPhone = "+62" + rawPhone;
        let formattedFullAddress = detailAddress + ", Kec. " + $('#shipping_district option:selected').text() + ", " + $('#shipping_city option:selected').text() + ", " + $('#shipping_province option:selected').text() + " (" + postal + ") — Telp: " + finalPhone;

        let btn = $(this);
        let originalText = btn.text();
        btn.text('Memproses...').prop('disabled', true);

        $.ajax({
            url: "{{ route('customer.checkout.process') }}",
            type: "POST",
            data: { 
                _token: "{{ csrf_token() }}", 
                shipping_address: formattedFullAddress, 
                courier: selectedCourierName, 
                courier_service: selectedCourierService, 
                shipping_cost: selectedCourierCost,
                user_voucher_id: selectedVoucherId // Kirim ID Voucher
            },
            success: function (response) {
                // PERBAIKAN: Jika Pesanan Gratis (Dipotong Full Voucher), Jangan Panggil Midtrans
                if (response.is_free) {
                    window.location.href = "/checkout/finish?order_id=" + response.order_code;
                    return; // Hentikan eksekusi script ini
                }

                // Jika Tidak Gratis, Buka Popup Midtrans
                if (response.snap_token) {
                    snap.pay(response.snap_token, {
                        onSuccess: function() { window.location.href = "/checkout/finish?order_id=" + response.order_code; },
                        onPending: function() { window.location.href = "/checkout/finish?order_id=" + response.order_code; },
                        onError: function() { 
                            $('#error-general').removeClass('hidden').text('Proses Pembayaran Gagal.'); 
                            btn.text(originalText).prop('disabled', false);
                        },
                        onClose: function() {
                            btn.text(originalText).prop('disabled', false);
                        }
                    });
                }
            },
            error: function (xhr) {
                $('#error-general').removeClass('hidden').text('Sistem Gagal Memproses Checkout. Pastikan keranjang tidak kosong.');
                btn.text(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
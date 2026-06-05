@extends('layouts.customer')

@section('content')
<div class="min-h-screen bg-[#f5f5f5] text-black">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 min-h-screen text-black">

        <div class="bg-white px-8 lg:px-16 py-10 border-r border-gray-200">

            <div class="mb-6">
                <h1 class="text-5xl font-black tracking-[14px] text-black">
                    BATIK
                </h1>
            </div>

            <div class="flex items-center gap-3 text-sm text-gray-400 mb-10">
                <a href="{{ route('customer.cart.index') }}" class="hover:text-black transition-all">
                    Cart
                </a>
                <span>›</span>
                <span class="font-semibold text-black">
                    Information & Shipping
                </span>
                <span>›</span>
                <span class="text-gray-300">
                    Payment
                </span>
            </div>

            <div class="mb-10">
                <h2 class="text-3xl font-bold mb-5 text-black">
                    Contact
                </h2>
                <div class="text-gray-700">
                    {{ auth()->user()->name }} ({{ auth()->user()->email }})
                </div>
            </div>

            <div>
                <h2 class="text-3xl font-bold mb-6 text-black">
                    Shipping address
                </h2>

                <div class="space-y-5">

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">
                            Nama Penerima
                        </label>
                        <input type="text" id="receiver_name" value="{{ auth()->user()->name }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black"
                            placeholder="Nama penerima">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">
                            Nomor Telepon
                        </label>
                        <input type="text" id="phone"
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">
                            Provinsi
                        </label>
                        <select id="shipping_province"
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="">Pilih Provinsi Tujuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">
                            Kota / Kabupaten
                        </label>
                        <select id="shipping_city" disabled
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="">Pilih Kota Tujuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">
                            Kecamatan
                        </label>
                        <select id="shipping_district" disabled
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="">Pilih Kecamatan Tujuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">
                            Kode Pos
                        </label>
                        <input type="text" id="postal_code"
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black"
                            placeholder="Kode Pos">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">
                            Alamat Lengkap
                        </label>
                        <textarea id="shipping_address" rows="4" placeholder="Nama jalan, RT/RW, nomor rumah, atau patokan detail..."
                            class="w-full border border-gray-300 rounded-lg px-4 py-4 text-black bg-white focus:outline-none focus:ring-2 focus:ring-black"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-black">
                            Opsi Pengiriman (Termurah)
                        </label>
                        <div id="cheapest_delivery_info" class="w-full border border-dashed border-gray-300 bg-gray-50 rounded-lg p-4 text-sm text-gray-500 italic">
                            Silakan lengkapi wilayah pengiriman untuk mencari ongkir terbaik...
                        </div>
                    </div>

                    <div class="pt-6 flex items-center justify-between">
                        <a href="{{ route('customer.cart.index') }}" class="text-sm text-gray-500 hover:text-black">
                            ← Return to cart
                        </a>
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
                                <img src="{{ asset('storage/' . ($item->variant->image_path ?? 'placeholder.jpg')) }}"
                                     class="w-full h-full object-cover">
                                <span class="absolute -top-2 -right-2 bg-black text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center">
                                    {{ $item->quantity }}
                                </span>
                            </div>

                            <div>
                                <h3 class="font-medium text-sm leading-5 max-w-[230px] text-black">
                                    {{ $item->product->name }}
                                </h3>
                                <p class="text-sm text-gray-400 mt-1">
                                    {{ $item->product->category->name ?? 'Batik' }} 
                                    @if($item->variant) • {{ $item->variant->motif }} ({{ $item->variant->size }}) @endif
                                </p>
                            </div>
                        </div>

                        <div class="font-medium whitespace-nowrap text-black">
                            Rp {{ number_format($itemTotal,0,',','.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 border-t border-gray-300 pt-8 space-y-4">
                <div class="flex justify-between text-gray-700">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subtotal,0,',','.') }}</span>
                </div>

                <div class="flex justify-between text-gray-700">
                    <span>Shipping</span>
                    <span id="shipping-cost">Rp 0</span>
                </div>

                <div class="border-t border-gray-300 pt-6 flex justify-between items-center">
                    <div>
                        <span class="text-3xl font-bold text-black">Total</span>
                    </div>

                    <div class="text-right">
                        <div class="text-xs text-gray-400 uppercase">IDR</div>
                        <div id="grand-total" data-subtotal="{{ $subtotal }}" class="text-4xl font-black text-black">
                            Rp {{ number_format($subtotal,0,',','.') }}
                        </div>
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

    $.get("{{ route('api.provinces') }}", function (data) {
        $.each(data, function (key, value) {
            if(value && value.id) {
                $('#shipping_province').append('<option value="' + value.id + '">' + value.name + '</option>');
            }
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
                    if(value && value.id) {
                        $('#shipping_city').append('<option value="' + value.id + '">' + value.name + '</option>');
                    }
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
                    if(value && value.id) {
                        $('#shipping_district').append('<option value="' + value.id + '" data-postal="' + (value.postal_code || '') + '">' + value.name + '</option>');
                    }
                });
            });
        }
    });

    $('#shipping_district').change(function () {
        let postalCode = $(this).find(':selected').data('postal');
        if (postalCode) $('#postal_code').val(postalCode);
        
        let districtId = $(this).val();
        if (districtId) {
            fetchCheapestShipping(districtId);
        } else {
            resetShippingCost();
        }
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
                    let namaKurir = s.courier_name || s.name || 'Kurir';
                    let service = s.service || 'REG';
                    let cost = parseInt(s.cost) || 0;

                    selectedCourierName = namaKurir;
                    selectedCourierService = service;
                    selectedCourierCost = cost;

                    $('#cheapest_delivery_info').html(`
                        <div class="p-4 bg-green-50 border border-green-300 rounded-lg text-green-700">
                            ✅ <strong>${namaKurir} - ${service}</strong><br>
                            Biaya: <strong>Rp ${cost.toLocaleString('id-ID')}</strong>
                        </div>
                    `);

                    let subtotal = parseInt($('#grand-total').data('subtotal'));
                    $('#shipping-cost').text('Rp ' + cost.toLocaleString('id-ID'));
                    $('#grand-total').text('Rp ' + (subtotal + cost).toLocaleString('id-ID'));
                } else {
                    $('#cheapest_delivery_info').html('❌ Tidak ada kurir yang melayani rute ini.');
                    resetShippingCost();
                }
            },
            error: function(xhr) {
                $('#cheapest_delivery_info').html('❌ Gagal memuat ongkir.');
                resetShippingCost();
            }
        });
    }

    function resetShippingCost() {
        selectedCourierName = "";
        selectedCourierService = "";
        selectedCourierCost = 0;
        $('#cheapest_delivery_info').html('Silakan lengkapi wilayah pengiriman untuk mencari ongkir terbaik...');
        $('#shipping-cost').text('Rp 0');
        $('#grand-total').text('Rp ' + parseInt($('#grand-total').data('subtotal')).toLocaleString('id-ID'));
    }

    $('#checkout-button').click(function () {
        let province = $('#shipping_province option:selected').text();
        let city = $('#shipping_city option:selected').text();
        let district = $('#shipping_district option:selected').text();
        let postal = $('#postal_code').val();
        let detailAddress = $('#shipping_address').val();
        let phone = $('#phone').val();

        if (!district || !detailAddress || !phone || selectedCourierCost === 0) {
            alert('Harap isi nomor telepon, lengkapi alamat, dan pastikan kurir telah termuat!');
            return;
        }

        let formattedFullAddress = detailAddress + ", Kec. " + district + ", " + city + ", Prov. " + province + " (" + postal + ") — Telp: " + phone;

        $.ajax({
            url: "{{ route('customer.checkout.process') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                shipping_address: formattedFullAddress,
                courier: selectedCourierName,
                courier_service: selectedCourierService,
                shipping_cost: selectedCourierCost,
            },
            success: function (response) {
                if (response.snap_token) {
                    snap.pay(response.snap_token, {
                        onSuccess: function(result) {
                            window.location.href = "/checkout/finish?order_id=" + response.order_code;
                        },
                        onPending: function(result) {
                            window.location.href = "/checkout/finish?order_id=" + response.order_code;
                        },
                        onError: function(result) {
                            alert('Proses Pembayaran Gagal.');
                        }
                    });
                }
            },
            error: function (xhr) {
                alert('Sistem Gagal Memproses Checkout.');
            }
        });
    });
});
</script>
@endsection
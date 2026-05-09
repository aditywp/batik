{{-- resources/views/customer/checkout/index.blade.php --}}
@extends('layouts.customer')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- FORM KIRI --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Pilih Provinsi & Kota --}}
        <div class="bg-white rounded-xl border border-stone-200 p-6">
            <h3 class="font-semibold text-stone-800 mb-4">Alamat Pengiriman</h3>

            <select id="province" class="w-full rounded-lg border border-stone-200 p-2.5 text-sm mb-3">
                <option value="">-- Pilih Provinsi --</option>
                @foreach($provinces as $prov)
                    <option value="{{ $prov['province_id'] }}">{{ $prov['province'] }}</option>
                @endforeach
            </select>

            <select id="city" class="w-full rounded-lg border border-stone-200 p-2.5 text-sm mb-3" disabled>
                <option value="">-- Pilih Kota --</option>
            </select>

            <textarea id="shipping_address" rows="3"
                class="w-full rounded-lg border border-stone-200 p-2.5 text-sm"
                placeholder="Alamat lengkap, nomor rumah, RT/RW..."></textarea>
        </div>

        {{-- Pilih Kurir --}}
        <div class="bg-white rounded-xl border border-stone-200 p-6">
            <h3 class="font-semibold text-stone-800 mb-4">Kurir Pengiriman</h3>
            <div class="flex gap-3 mb-4">
                @foreach(['jne' => 'JNE', 'jnt' => 'J&T', 'pos' => 'Pos Indonesia'] as $val => $label)
                <button type="button" data-courier="{{ $val }}"
                    class="courier-btn flex-1 py-2 rounded-lg border border-stone-200 text-sm
                           text-stone-600 hover:border-stone-800 transition-colors">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Hasil ongkir muncul di sini --}}
            <div id="shipping-options" class="space-y-2 hidden"></div>
            <p id="shipping-loading" class="text-sm text-stone-400 hidden">Menghitung ongkir...</p>
        </div>
    </div>

    {{-- RINGKASAN ORDER KANAN --}}
    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-stone-200 p-5 sticky top-6">
            <h3 class="font-semibold text-stone-800 mb-4">Ringkasan Pesanan</h3>

            @foreach($cartItems as $item)
            <div class="flex justify-between text-sm mb-2">
                <span class="text-stone-600">{{ $item->product->name }} ×{{ $item->quantity }}</span>
                <span>Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
            </div>
            @endforeach

            <div class="border-t border-stone-100 mt-3 pt-3 flex justify-between text-sm">
                <span class="text-stone-500">Ongkir</span>
                <span id="display-shipping">Rp 0</span>
            </div>
            <div class="flex justify-between font-semibold mt-2">
                <span>Total</span>
                <span id="display-total">Rp {{ number_format($cartItems->sum(fn($i) => $i->product->price * $i->quantity), 0, ',', '.') }}</span>
            </div>

            <button id="btn-bayar"
                class="mt-5 w-full bg-stone-900 text-amber-200 rounded-lg py-3 text-sm
                       font-medium hover:bg-stone-800 transition-colors disabled:opacity-50
                       disabled:cursor-not-allowed"
                disabled>
                Bayar Sekarang
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ config('services.midtrans.snap_url') }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
const subtotal = {{ $cartItems->sum(fn($i) => $i->product->price * $i->quantity) }};
let selectedShipping = null;

// === Dinamis kota berdasarkan provinsi ===
document.getElementById('province').addEventListener('change', async function () {
    const cityEl = document.getElementById('city');
    cityEl.disabled = true;
    cityEl.innerHTML = '<option>Memuat kota...</option>';

    const res = await fetch(`/shipping/cities?province_id=${this.value}`);
    const cities = await res.json();

    cityEl.innerHTML = '<option value="">-- Pilih Kota --</option>' +
        cities.map(c => `<option value="${c.city_id}">${c.city_name}</option>`).join('');
    cityEl.disabled = false;
});

// === Hitung ongkir saat kota & kurir dipilih ===
async function fetchShippingCost(courier) {
    const cityId = document.getElementById('city').value;
    if (!cityId) return;

    document.getElementById('shipping-loading').classList.remove('hidden');
    document.getElementById('shipping-options').classList.add('hidden');

    const res = await fetch('/shipping/cost', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ destination_city_id: cityId, courier })
    });

    const options = await res.json();
    const container = document.getElementById('shipping-options');

    container.innerHTML = options.map(opt => `
        <label class="flex items-center justify-between p-3 rounded-lg border border-stone-200
                       cursor-pointer hover:border-stone-800 transition-colors has-[:checked]:border-stone-800
                       has-[:checked]:bg-stone-50">
            <div class="flex items-center gap-3">
                <input type="radio" name="shipping" value="${opt.cost}"
                       data-service="${opt.service}" class="shipping-radio">
                <div>
                    <p class="text-sm font-medium">${courier.toUpperCase()} ${opt.service}</p>
                    <p class="text-xs text-stone-400">Estimasi ${opt.etd}</p>
                </div>
            </div>
            <span class="text-sm font-medium">Rp ${opt.cost.toLocaleString('id-ID')}</span>
        </label>
    `).join('');

    document.querySelectorAll('.shipping-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            selectedShipping = {
                cost: parseInt(this.value),
                service: this.dataset.service,
                courier: courier,
            };
            updateTotal();
            document.getElementById('btn-bayar').disabled = false;
        });
    });

    document.getElementById('shipping-loading').classList.add('hidden');
    container.classList.remove('hidden');
}

// === Pilih kurir ===
document.querySelectorAll('.courier-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.courier-btn').forEach(b => b.classList.remove('border-stone-800', 'bg-stone-50'));
        this.classList.add('border-stone-800', 'bg-stone-50');
        fetchShippingCost(this.dataset.courier);
    });
});

// === Update total ===
function updateTotal() {
    if (!selectedShipping) return;
    const total = subtotal + selectedShipping.cost;
    document.getElementById('display-shipping').textContent = 'Rp ' + selectedShipping.cost.toLocaleString('id-ID');
    document.getElementById('display-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// === Proses pembayaran via Midtrans Snap ===
document.getElementById('btn-bayar').addEventListener('click', async function () {
    this.disabled = true;
    this.textContent = 'Memproses...';

    const res = await fetch('/checkout/process', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            shipping_address:    document.getElementById('shipping_address').value,
            destination_city_id: document.getElementById('city').value,
            courier:             selectedShipping.courier,
            courier_service:     selectedShipping.service,
            shipping_cost:       selectedShipping.cost,
        })
    });

    const data = await res.json();

    // Buka popup Midtrans Snap
    snap.pay(data.snap_token, {
        onSuccess: (result) => window.location.href = '/checkout/finish?order_id=' + result.order_id,
        onPending: (result) => window.location.href = '/checkout/finish?order_id=' + result.order_id,
        onError:   ()       => alert('Pembayaran gagal, silakan coba lagi.'),
        onClose:   ()       => { this.disabled = false; this.textContent = 'Bayar Sekarang'; },
    });
});
</script>
@endpush
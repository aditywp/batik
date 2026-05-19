@extends('layouts.customer')

@section('content')

<div class="min-h-screen bg-[#f5f5f5] flex items-center justify-center px-6 py-16">

    <div class="max-w-2xl w-full bg-white rounded-3xl shadow-sm p-12">

        <!-- ICON -->
        <div class="flex justify-center mb-8">

            <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-12 h-12 text-green-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />
                </svg>

            </div>

        </div>

        <!-- TITLE -->
        <div class="text-center mb-10">

            <h1 class="text-4xl font-black tracking-wide text-black mb-4">
                PAYMENT SUCCESS
            </h1>

            <p class="text-gray-500 text-lg">
                Terima kasih telah berbelanja di Batik Ifawati
            </p>

        </div>

        <!-- ORDER -->
        <div class="border border-gray-200 rounded-2xl p-8 space-y-5">

            <div class="flex justify-between">

                <span class="text-gray-500">
                    Order Code
                </span>

                <span class="font-semibold text-black">
                    {{ $order->order_code }}
                </span>

            </div>

            <div class="flex justify-between">

                <span class="text-gray-500">
                    Status
                </span>

                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-semibold">
                    {{ strtoupper($order->status) }}
                </span>

            </div>

            <div class="flex justify-between">

                <span class="text-gray-500">
                    Payment
                </span>

                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                    {{ strtoupper($order->payment_status) }}
                </span>

            </div>

            <div class="border-t pt-5 flex justify-between items-center">

                <span class="text-xl font-bold text-black">
                    Total
                </span>

                <span class="text-3xl font-black text-black">
                    Rp {{ number_format($order->total,0,',','.') }}
                </span>

            </div>

        </div>

        <!-- ITEMS -->
        <div class="mt-10">

            <h2 class="text-2xl font-bold text-black mb-6">
                Order Items
            </h2>

            <div class="space-y-5">

                @foreach($order->items as $item)

                    <div class="flex justify-between items-center border-b pb-4">

                        <div class="flex items-center gap-4">

                            <div class="w-16 h-16 rounded-xl overflow-hidden border bg-white">
                                <img src="{{ asset('storage/' . $item->variant->image_path ?? 'placeholder.jpg') }}"
                                class="w-full h-full object-cover">
                            </div>

                            <div>

                                <h3 class="font-semibold text-black">
                                    {{ $item->product->name }}
                                </h3>

                                <p class="text-sm text-gray-500">
                                    Qty: {{ $item->quantity }}
                                </p>

                            </div>

                        </div>

                        <div class="font-bold text-black">

                            Rp {{ number_format($item->subtotal,0,',','.') }}

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

        <!-- BUTTON -->
        <div class="mt-12 flex justify-center">

            <a
                href="{{ route('customer.home') }}"
                class="bg-black hover:bg-gray-900 text-white px-10 py-4 rounded-xl font-semibold transition-all"
            >
                Back to Home
            </a>

        </div>

    </div>

</div>

@endsection
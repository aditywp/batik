<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RajaOngkirController extends Controller
{
    /**
     * Header API RajaOngkir (Komerce Bypass)
     */
    private function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'key'    => config('rajaongkir.api_key'), // Membaca file config/rajaongkir.php
        ];
    }

    /**
     * Halaman Raja Ongkir / Checkout
     */
    public function index()
    {
        $provinces = $this->getProvinces();

        return view('customer.checkout.index', compact('provinces'));
    }

    /**
     * AMBIL DATA PROVINSI (Untuk Internal Blade via Server-side)
     */
    public function getProvinces()
    {
        return Cache::remember('rajaongkir_provinces', 86400, function () {
            $response = Http::withHeaders($this->getHeaders())
                ->get('https://rajaongkir.komerce.id/api/v1/destination/province');

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? $json;
            }

            Log::error('RajaOngkir Province Error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [];
        });
    }

    /**
     * GET PROVINCES JSON (Untuk Request AJAX Frontend)
     */
    public function getProvincesJson()
    {
        try {
            $provinces = $this->getProvinces();
            return response()->json($provinces);
        } catch (\Exception $e) {
            Log::error('RajaOngkir Province JSON Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data provinsi'], 500);
        }
    }

    /**
     * GET CITIES BY PROVINCE (CACHE 24 JAM)
     */
    public function getCities(int $provinceId)
    {
        try {
            $cacheKey = 'rajaongkir_cities_' . $provinceId;

            $cities = Cache::remember($cacheKey, 86400, function () use ($provinceId) {
                $response = Http::withHeaders($this->getHeaders())
                    ->get("https://rajaongkir.komerce.id/api/v1/destination/city/{$provinceId}");

                if ($response->successful()) {
                    $json = $response->json();
                    return $json['data'] ?? $json;
                }

                return [];
            });

            return response()->json($cities);

        } catch (\Exception $e) {
            Log::error('RajaOngkir City Error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET DISTRICTS BY CITY (CACHE 24 JAM)
     */
    public function getDistricts(int $cityId)
    {
        try {
            $cacheKey = 'rajaongkir_districts_' . $cityId;

            $districts = Cache::remember($cacheKey, 86400, function () use ($cityId) {
                $response = Http::withHeaders($this->getHeaders())
                    ->get("https://rajaongkir.komerce.id/api/v1/destination/district/{$cityId}");

                if ($response->successful()) {
                    $json = $response->json();
                    return $json['data'] ?? $json;
                }

                return [];
            });

            return response()->json($districts);

        } catch (\Exception $e) {
            Log::error('RajaOngkir District Error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
                 * CALCULATE DOMESTIC COST / AUTOMATIC CHEAPEST SELECTION
     * (Menyaring kurir termurah langsung di server, menghemat kuota API hit harian)
     */
    public function checkOngkir(Request $request)
    {
        try {
            $request->validate([
                'district_id' => 'required',
                'weight'      => 'required|numeric'
            ]);

            $districtId = $request->district_id;
            $cacheKey = 'cheapest_ongkir_district_' . $districtId;

            // Simpan hasil kalkulasi termurah di Cache selama 1 jam demi efisiensi kuota
            $cheapestDelivery = Cache::remember($cacheKey, 3600, function () use ($districtId, $request) {
                
                // Memicu pencarian dasar dari sistem kurir (Komerce mengembalikan multi-ekspedisi)
                $response = Http::asForm()
                    ->withHeaders($this->getHeaders())
                    ->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                        'origin'      => 2104, // ID asal toko baju batik (Yogyakarta / Depok)
                        'destination' => $districtId,
                        'weight'      => $request->weight,
                        'courier'     => 'jne', // Parameter pemicu pencarian dasar
                    ]);

                if ($response->successful()) {
                    $allServices = $response->json()['data'] ?? [];
                    $cheapest = null;

                    // Mengurai dan menyaring satu per satu objek pengiriman dari API Komerce
                    foreach ($allServices as $service) {
                        $cost = intval($service['cost'] ?? 0);

                        // Pastikan harga kurir valid (di atas 0 rupiah)
                        if ($cost > 0) {
                            if ($cheapest === null || $cost < $cheapest['cost']) {
                                $cheapest = [
                                    'courier'     => strtoupper($service['courier_name'] ?? 'JNE'),
                                    'service'     => $service['service'] ?? $service['service_name'] ?? 'Reguler',
                                    'description' => $service['description'] ?? 'Pengiriman Standar',
                                    'cost'        => $cost,
                                    'etd'         => $service['etd'] ? $service['etd'] . ' Hari' : ''
                                ];
                            }
                        }
                    }
                    return $cheapest;
                }

                Log::error('RajaOngkir Multi-Courier Hit Failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return null;
            });

            if ($cheapestDelivery) {
                return response()->json($cheapestDelivery);
            }

            return response()->json([
                'message' => 'Gagal mendapatkan atau menyaring data kurir termurah'
            ], 400);

        } catch (\Exception $e) {
            Log::error('RajaOngkir Error (Cost Calculation Server-Side): ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan sistem penentuan ongkir otomatis'
            ], 500);
        }
    }
}
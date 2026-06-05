<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RajaOngkirController extends Controller
{
    /**
     * Header API RajaOngkir (Centralized)
     */
    private function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'key'    => config('rajaongkir.api_key'),
        ];
    }

    /**
     * Halaman Checkout
     */
    public function index()
    {
        return view('customer.checkout.index');
    }

    /**
     * Mengambil daftar provinsi
     */
    public function getProvinces()
    {
        return Cache::remember('rajaongkir_provinces', 86400, function () {
            $response = Http::withHeaders($this->getHeaders())
                ->get('https://rajaongkir.komerce.id/api/v1/destination/province');
            return $response->successful() ? ($response->json()['data'] ?? []) : [];
        });
    }

    // Metode ini wajib ada untuk route 'api.provinces'
    public function getProvincesJson()
    {
        return response()->json($this->getProvinces());
    }

    public function getCities($provinceId)
    {
        // Cache hanya menyimpan array, bukan objek response
        $data = Cache::remember('rajaongkir_cities_' . $provinceId, 86400, function () use ($provinceId) {
            $response = Http::withHeaders($this->getHeaders())
                ->get("https://rajaongkir.komerce.id/api/v1/destination/city/{$provinceId}");
                
            return $response->successful() ? ($response->json()['data'] ?? []) : [];
        });

        // Bungkus data menjadi JSON di luar cache
         return response()->json($data);
    }

    public function getDistricts($cityId)
    {
        // Cache hanya menyimpan array
        $data = Cache::remember('rajaongkir_districts_' . $cityId, 86400, function () use ($cityId) {
            $response = Http::withHeaders($this->getHeaders())
                ->get("https://rajaongkir.komerce.id/api/v1/destination/district/{$cityId}");
            
            return $response->successful() ? ($response->json()['data'] ?? []) : [];
        });

        // Bungkus data menjadi JSON di luar cache
        return response()->json($data);
    }

    /**
     * Menghitung ongkos kirim dengan semua kurir
     */
    public function checkOngkir(Request $request)
    {
        try {
            $districtId = $request->district_id;
            $weight = $request->weight ?? 1000;
            $cacheKey = 'ongkir_paling_murah_' . $districtId . '_' . $weight;

            return Cache::remember($cacheKey, 86400, function () use ($districtId, $weight) {
                $kurirs = ['jne', 'jnt', 'sicepat'];
                $semuaLayanan = [];

                foreach ($kurirs as $kodeKurir) {
                    $response = Http::withHeaders($this->getHeaders())
                        ->asForm()
                        ->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                            'origin'      => 2581,
                            'destination' => (int) $districtId,
                            'weight'      => (float) $weight,
                            'courier'     => $kodeKurir,
                        ]);

                    if ($response->successful()) {
                        // API Komerce mengembalikan list layanan dalam 'data'
                        $data = $response->json()['data'] ?? [];
                        foreach ($data as $item) {
                            // Pastikan harga valid
                            if (isset($item['cost']) && (int)$item['cost'] > 0) {
                                $semuaLayanan[] = [
                                    'courier_name' => strtoupper($item['courier_name'] ?? $kodeKurir),
                                    'service'      => $item['service'] ?? 'REG',
                                    'cost'         => (int)$item['cost'],
                                    'etd'          => $item['etd'] ?? '-'
                                ];
                            }
                        }
                    }
                }

                // Jika tidak ada data sama sekali
                if (empty($semuaLayanan)) return [];

                // URUTKAN: Yang paling murah ditaruh di indeks ke-0
                usort($semuaLayanan, fn($a, $b) => $a['cost'] <=> $b['cost']);

                // Mengembalikan 1 data termurah saja (hasil perbandingan semua kurir & layanan)
                return [$semuaLayanan[0]];
            });

        } catch (\Exception $e) {
            Log::error('Ongkir Fatal Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal'], 500);
        }
    }
}
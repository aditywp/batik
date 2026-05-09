<?php
// app/Services/RajaOngkirService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    private string $baseUrl;
    private string $apiKey;
    private int    $originCityId;

    public function __construct()
    {
        $this->baseUrl      = config('services.rajaongkir.base_url');
        $this->apiKey       = config('services.rajaongkir.api_key');
        $this->originCityId = config('services.rajaongkir.origin_city_id');
    }

    /**
     * Ambil semua provinsi (di-cache 24 jam — jarang berubah)
     */
    public function getProvinces(): array
    {
        return Cache::remember('rajaongkir_provinces', 86400, function () {
            $response = Http::withHeaders(['key' => $this->apiKey])
                ->get("{$this->baseUrl}/province");

            return $response->json('rajaongkir.results', []);
        });
    }

    /**
     * Ambil kota berdasarkan provinsi (di-cache 24 jam)
     */
    public function getCities(int $provinceId): array
    {
        return Cache::remember("rajaongkir_cities_{$provinceId}", 86400, function () use ($provinceId) {
            $response = Http::withHeaders(['key' => $this->apiKey])
                ->get("{$this->baseUrl}/city", ['province' => $provinceId]);

            return $response->json('rajaongkir.results', []);
        });
    }

    /**
     * Hitung ongkir — di-cache 5 menit per kombinasi origin+destination+weight+courier
     */
    public function getCost(int $destinationCityId, int $weightGram, string $courier = 'jne'): array
    {
        $cacheKey = "ongkir_{$this->originCityId}_{$destinationCityId}_{$weightGram}_{$courier}";

        return Cache::remember($cacheKey, 300, function () use ($destinationCityId, $weightGram, $courier) {
            $response = Http::withHeaders(['key' => $this->apiKey])
                ->post("{$this->baseUrl}/cost", [
                    'origin'          => $this->originCityId,
                    'destination'     => $destinationCityId,
                    'weight'          => $weightGram,
                    'courier'         => $courier, // jne | pos | tiki
                ]);

            $results = $response->json('rajaongkir.results.0.costs', []);

            // Format ulang supaya lebih mudah dipakai di View
            return collect($results)->map(fn($cost) => [
                'service'     => $cost['service'],
                'description' => $cost['description'],
                'cost'        => $cost['cost'][0]['value'],
                'etd'         => $cost['cost'][0]['etd'] . ' hari',
            ])->toArray();
        });
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class WilayahDbController extends Controller
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.rajaongkir.url'), '/');
        $this->apiKey = config('services.rajaongkir.key');
    }

    protected function client()
    {
        return Http::withHeaders([
            'accept' => 'application/json',
            'key'    => $this->apiKey,
        ]);
    }

    // 1. PROVINSI 
    public function getProvinces(): JsonResponse
    {
        $response = $this->client()->get("{$this->baseUrl}/destination/province");

        return response()->json([
            'success' => $response->successful(),
            'data'    => $response->json('data') ?? [],
        ], $response->status());
    }

    // 2. KOTA / KABUPATEN 
    public function getCities($province): JsonResponse
    {
        $response = $this->client()->get("{$this->baseUrl}/destination/city/{$province}");

        return response()->json([
            'success' => $response->successful(),
            'data'    => $response->json('data') ?? [],
        ], $response->status());
    }

    // 3. KECAMATAN 
    public function getDistricts($city): JsonResponse
    {
        $response = $this->client()->get("{$this->baseUrl}/destination/district/{$city}");

        return response()->json([
            'success' => $response->successful(),
            'data'    => $response->json('data') ?? [],
        ], $response->status());
    }

    // 4. KELURAHAN / DESA 
    public function getSubDistrict($district): JsonResponse
    {
        $response = $this->client()->get("{$this->baseUrl}/destination/sub-district/{$district}");

        return response()->json([
            'success' => $response->successful(),
            'data'    => $response->json('data') ?? [],
        ], $response->status());
    }
}

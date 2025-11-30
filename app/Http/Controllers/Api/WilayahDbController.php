<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class WilayahDbController extends Controller
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.wilayah.url'), '/');
    }

    protected function client()
    {
        return Http::timeout(10)->acceptJson();
    }

    // PROVINCES
    public function getProvinces(): JsonResponse
    {
        $resp = $this->client()->get("{$this->baseUrl}/provinces.json");

        return response()->json([
            'success' => $resp->successful(),
            'data' => $resp->json() ?? [],
        ], $resp->status());
    }

    // KABUPATEN/KOTA (REGENCIES)
    public function getRegencies($province)
    {
        $resp = Http::get("{$this->baseUrl}/regencies/{$province}.json");

        return response()->json([
            'success' => $resp->successful(),
            'data' => $resp->json('data') ?? [],
        ]);
    }

    public function getDistricts($regency)
    {
        $resp = Http::get("{$this->baseUrl}/districts/{$regency}.json");

        return response()->json([
            'success' => $resp->successful(),
            'data' => $resp->json('data') ?? [],
        ]);
    }

    public function getVillages($district)
    {
        $resp = Http::get("{$this->baseUrl}/villages/{$district}.json");

        return response()->json([
            'success' => $resp->successful(),
            'data' => $resp->json('data') ?? [],
        ]);
    }

}

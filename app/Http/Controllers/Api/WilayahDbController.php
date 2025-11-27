<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WilayahDbController extends Controller
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $url = config('services.rajaongkir.url');
        $key = config('services.rajaongkir.key');
        $this->baseUrl = rtrim(trim((string)($url ?? '')), '/');
        $this->apiKey  = trim((string)($key ?? ''));
        if (empty($this->baseUrl) || empty($this->apiKey)) {
            Log::warning('Wilayah API config missing', [
                'url_set' => !empty($this->baseUrl),
                'key_set' => !empty($this->apiKey),
            ]);
        }
    }

    protected function client()
    {
        return Http::timeout(12)->withHeaders([
            'Accept'        => 'application/json',
            'key'           => $this->apiKey,
            'X-API-KEY'     => $this->apiKey,
            'Authorization' => 'Bearer ' . $this->apiKey,
        ]);
    }

    // PROVINSI
    public function getProvinces(): JsonResponse
    {
        if (empty($this->baseUrl) || empty($this->apiKey)) {
            return response()->json(['success' => false, 'message' => 'Konfigurasi API wilayah belum diatur.', 'data' => []], 500);
        }
        // Prefer Komerce aggregator endpoints first, then fallbacks
        $endpoints = ['/destination/province', '/region/provinces', '/province'];
        $resp = null; $used = null;
        foreach ($endpoints as $p) {
            try {
                $used = $p;
                $resp = $this->client()->get($this->baseUrl . $p);
                if ($resp->successful()) break;
            } catch (\Throwable $e) {
                Log::info('Fetch provinces failed', ['path' => $p, 'error' => $e->getMessage()]);
            }
        }
        $resp = $resp ?? response()->json([], 502);
        $data = $resp->json('data');
        if ($data === null) { $data = $resp->json('results') ?? $resp->json(); }
        $payload = [
            'success' => $resp->successful(),
            'data'    => is_array($data) ? $data : [],
            'message' => $resp->successful() ? null : ($resp->json('message') ?? 'Gagal mengambil data provinsi')
        ];
        if (config('app.debug')) {
            $payload['debug'] = [
                'endpoint' => $used,
                'upstream_status' => method_exists($resp, 'status') ? $resp->status() : null,
                'upstream_body' => method_exists($resp, 'body') ? $resp->body() : null,
            ];
        }
        return response()->json($payload, method_exists($resp, 'status') ? $resp->status() : 502);
    }

    // KOTA
    public function getCities($province): JsonResponse
    {
        if (empty($this->baseUrl) || empty($this->apiKey)) {
            return response()->json(['success' => false, 'message' => 'Konfigurasi API wilayah belum diatur.', 'data' => []], 500);
        }
        $attempts = [
            fn() => $this->client()->get("{$this->baseUrl}/destination/city/{$province}"),
            fn() => $this->client()->get("{$this->baseUrl}/region/cities", ['province_id' => $province]),
            fn() => $this->client()->get("{$this->baseUrl}/city", ['province' => $province]),
        ];
        $resp = null;
        foreach ($attempts as $call) { try { $resp = $call(); if ($resp->successful()) break; } catch (\Throwable $e) {} }
        $resp = $resp ?? response()->json([], 502);
        $data = $resp->json('data');
        if ($data === null) { $data = $resp->json('results') ?? $resp->json(); }
        return response()->json([
            'success' => $resp->successful(),
            'data'    => is_array($data) ? $data : [],
            'message' => $resp->successful() ? null : ($resp->json('message') ?? 'Gagal mengambil data kota/kabupaten')
        ], $resp->status());
    }

    // KECAMATAN
    public function getDistricts($city): JsonResponse
    {
        if (empty($this->baseUrl) || empty($this->apiKey)) {
            return response()->json(['success' => false, 'message' => 'Konfigurasi API wilayah belum diatur.', 'data' => []], 500);
        }
        $attempts = [
            fn() => $this->client()->get("{$this->baseUrl}/destination/district/{$city}"),
            fn() => $this->client()->get("{$this->baseUrl}/region/districts", ['city_id' => $city]),
            fn() => $this->client()->get("{$this->baseUrl}/districts", ['city' => $city]),
        ];
        $resp = null;
        foreach ($attempts as $call) { try { $resp = $call(); if ($resp->successful()) break; } catch (\Throwable $e) {} }
        $resp = $resp ?? response()->json([], 502);
        $data = $resp->json('data');
        if ($data === null) { $data = $resp->json('results') ?? $resp->json(); }
        return response()->json([
            'success' => $resp->successful(),
            'data'    => is_array($data) ? $data : [],
            'message' => $resp->successful() ? null : ($resp->json('message') ?? 'Gagal mengambil data kecamatan')
        ], $resp->status());
    }

    // KELURAHAN
    public function getSubDistrict($district): JsonResponse
    {
        if (empty($this->baseUrl) || empty($this->apiKey)) {
            return response()->json(['success' => false, 'message' => 'Konfigurasi API wilayah belum diatur.', 'data' => []], 500);
        }
        $attempts = [
            fn() => $this->client()->get("{$this->baseUrl}/destination/sub-district/{$district}"),
            fn() => $this->client()->get("{$this->baseUrl}/region/subdistricts", ['district_id' => $district]),
            fn() => $this->client()->get("{$this->baseUrl}/villages", ['district' => $district]),
        ];
        $resp = null;
        foreach ($attempts as $call) { try { $resp = $call(); if ($resp->successful()) break; } catch (\Throwable $e) {} }
        $resp = $resp ?? response()->json([], 502);
        $data = $resp->json('data');
        if ($data === null) { $data = $resp->json('results') ?? $resp->json(); }
        return response()->json([
            'success' => $resp->successful(),
            'data'    => is_array($data) ? $data : [],
            'message' => $resp->successful() ? null : ($resp->json('message') ?? 'Gagal mengambil data kelurahan/desa')
        ], $resp->status());
    }
}

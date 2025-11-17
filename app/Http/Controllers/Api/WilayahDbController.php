<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class WilayahDbController extends Controller
{
    public function provinces(): JsonResponse
    {
        try {
            $base = rtrim(config('services.wilayah.base_url', 'https://wilayah.id/api'), '/');
            $url = "$base/provinces.json";
            $rows = $this->normalize($this->fetch($url));
            return response()->json($rows);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) { $this->logException($e, ['action' => 'WilayahDbController@provinces']); }
            $message = method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal memuat data provinsi.') : 'Gagal memuat data.';
            return response()->json(['message' => $message], 500);
        }
    }

    public function regencies(string $provinceId): JsonResponse
    {
        try {
            $base = rtrim(config('services.wilayah.base_url', 'https://wilayah.id/api'), '/');
            $url = "$base/regencies/" . urlencode($provinceId) . ".json";
            $rows = $this->normalize($this->fetch($url));
            return response()->json($rows);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) { $this->logException($e, ['action' => 'WilayahDbController@regencies', 'provinceId' => $provinceId]); }
            $message = method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal memuat data kabupaten/kota.') : 'Gagal memuat data.';
            return response()->json(['message' => $message], 500);
        }
    }

    public function districts(string $regencyId): JsonResponse
    {
        try {
            $base = rtrim(config('services.wilayah.base_url', 'https://wilayah.id/api'), '/');
            $url = "$base/districts/" . urlencode($regencyId) . ".json";
            $rows = $this->normalize($this->fetch($url));
            return response()->json($rows);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) { $this->logException($e, ['action' => 'WilayahDbController@districts', 'regencyId' => $regencyId]); }
            $message = method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal memuat data kecamatan.') : 'Gagal memuat data.';
            return response()->json(['message' => $message], 500);
        }
    }

    public function villages(string $districtId): JsonResponse
    {
        try {
            $base = rtrim(config('services.wilayah.base_url', 'https://wilayah.id/api'), '/');
            $url = "$base/villages/" . urlencode($districtId) . ".json";
            $rows = $this->normalize($this->fetch($url));
            return response()->json($rows);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) { $this->logException($e, ['action' => 'WilayahDbController@villages', 'districtId' => $districtId]); }
            $message = method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal memuat data kelurahan.') : 'Gagal memuat data.';
            return response()->json(['message' => $message], 500);
        }
    }

    protected function fetch(string $url)
    {
        $resp = Http::timeout(30)->retry(2, 500)->get($url);
        $resp->throw();
        return $resp->json();
    }

    protected function normalize($payload): array
    {
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $payload = $decoded;
            }
        }
        $items = is_array($payload) ? $payload : [];

        foreach (['data','provinsi','provinces','regencies','kabupaten','kecamatan','kelurahan','villages'] as $k) {
            if (isset($items[$k]) && is_array($items[$k])) {
                $items = $items[$k];
                break;
            }
        }

        $out = [];
        foreach ($items as $key => $item) {
            if (is_array($item)) {
                $id = $item['id']
                    ?? $item['code'] ?? $item['kode'] ?? $item['value']
                    ?? $item['province_id'] ?? $item['provinsi_id']
                    ?? $item['regency_id'] ?? $item['kabupaten_id']
                    ?? $item['district_id'] ?? $item['kecamatan_id']
                    ?? $item['village_id'] ?? $item['kelurahan_id']
                    ?? null;
                $name = $item['name']
                    ?? $item['nama'] ?? $item['text']
                    ?? $item['province'] ?? $item['provinsi']
                    ?? $item['regency'] ?? $item['kabupaten']
                    ?? $item['district'] ?? $item['kecamatan']
                    ?? $item['village'] ?? $item['kelurahan']
                    ?? null;
                if ($id === null && (is_string($key) || is_int($key))) {
                    $id = (string) $key;
                }
                if ($id !== null && $name !== null) {
                    $out[] = ['id' => (string) $id, 'name' => (string) $name];
                }
            } elseif (is_string($item)) {
                $out[] = ['id' => (string) $item, 'name' => (string) $item];
            }
        }

        return $out;
    }
}

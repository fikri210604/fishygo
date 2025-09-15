<?php

namespace App\Console\Commands;

use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Exception;

class FetchWilayah extends Command
{
    protected $signature = 'app:fetch-wilayah';
    protected $description = 'Fetch data wilayah Indonesia dari API wilayah.id';

    public function handle()
    {
        $this->info('Fetching data wilayah Indonesia...');

        try {
            $response = Http::timeout(30)->get('https://wilayah.id/api/provinces.json');

            if ($response->failed()) {
                $this->error("❌ Gagal fetch provinces: " . $response->status());
                return;
            }

            $provinces = $this->normalizeJson($response->json());

            foreach ($provinces as $province) {
                if (empty($province['code']) || empty($province['name'])) {
                    $this->warn("⚠️ Province data tidak valid: " . json_encode($province));
                    continue;
                }

                $prov = Province::updateOrCreate(
                    ['code' => $province['code']],
                    ['name' => $province['name']]
                );

                $this->info("Fetched province: {$prov->name}");

                // ambil kabupaten/kota
                $this->fetchRegencies($prov);
            }

            $this->info('✅ Data wilayah Indonesia fetched successfully!');
        } catch (Exception $e) {
            $this->error("❌ Fatal error: " . $e->getMessage());
        }
    }

    private function fetchRegencies($prov)
    {
        try {
            $regResp = Http::timeout(30)->get("https://wilayah.id/api/regencies/{$prov->code}.json");

            if ($regResp->failed()) {
                $this->warn("  ⚠️ Gagal fetch regencies untuk {$prov->name}");
                return;
            }

            $regencies = $this->normalizeJson($regResp->json());

            foreach ($regencies as $regency) {
                if (empty($regency['code']) || empty($regency['name'])) {
                    $this->warn("⚠️ Regency data tidak valid: " . json_encode($regency));
                    continue;
                }

                $reg = Regency::updateOrCreate(
                    ['code' => $regency['code']],
                    [
                        'name' => $regency['name'],
                        'province_code' => $prov->code,
                    ]
                );

                $this->info("  Fetched regency: {$reg->name}");

                // ambil kecamatan
                $this->fetchDistricts($reg);
            }
        } catch (Exception $e) {
            $this->warn("  ❌ Error regencies {$prov->name}: " . $e->getMessage());
        }
    }

    private function fetchDistricts($reg)
    {
        try {
            $distResp = Http::timeout(30)->get("https://wilayah.id/api/districts/{$reg->code}.json");

            if ($distResp->failed()) {
                $this->warn("    ⚠️ Gagal fetch districts untuk {$reg->name}");
                return;
            }

            $districts = $this->normalizeJson($distResp->json());

            foreach ($districts as $district) {
                if (empty($district['code']) || empty($district['name'])) {
                    $this->warn("⚠️ District data tidak valid: " . json_encode($district));
                    continue;
                }

                $dist = District::updateOrCreate(
                    ['code' => $district['code']],
                    [
                        'name' => $district['name'],   // ✅ diperbaiki, jangan pakai code
                        'regency_code' => $reg->code,
                    ]
                );

                $this->info("    Fetched district: {$dist->name}");

                // ambil desa
                $this->fetchVillages($dist);
            }
        } catch (Exception $e) {
            $this->warn("    ❌ Error districts {$reg->name}: " . $e->getMessage());
        }
    }

    private function fetchVillages($dist)
    {
        try {
            $vilResp = Http::timeout(30)->get("https://wilayah.id/api/villages/{$dist->code}.json");

            if ($vilResp->failed()) {
                $this->warn("      ⚠️ Gagal fetch villages untuk {$dist->name}");
                return;
            }

            $villages = $this->normalizeJson($vilResp->json());

            foreach ($villages as $village) {
                if (empty($village['code']) || empty($village['name'])) {
                    $this->warn("⚠️ Village data tidak valid: " . json_encode($village));
                    continue;
                }

                $vil = Village::updateOrCreate(
                    ['code' => $village['code']],
                    [
                        'name' => $village['name'],
                        'district_code' => $dist->code,
                    ]
                );

                $this->info("      Fetched village: {$vil->name}");
            }
        } catch (Exception $e) {
            $this->warn("      ❌ Error villages {$dist->name}: " . $e->getMessage());
        }
    }

    /**
     * Normalisasi response API, biar bisa handle ["data" => [...]] atau langsung array
     */
    private function normalizeJson($json)
    {
        if (is_array($json) && isset($json['data']) && is_array($json['data'])) {
            return $json['data'];
        }
        if (is_array($json)) {
            return $json;
        }
        return [];
    }
}

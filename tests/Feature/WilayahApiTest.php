<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\WilayahSeeder;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;

class WilayahApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(WilayahSeeder::class);
    }

    /**
     * Test if provinces can be fetched.
     */
    public function test_can_fetch_provinces(): void
    {
        $response = $this->getJson('/api/wilayah/provinces');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*', // Asserts that the response is an array
                     [
                         'id',
                         'name',
                     ]
                 ])
                 ->assertJsonCount(Province::count());
    }

    /**
     * Test if regencies can be fetched by province ID.
     */
    public function test_can_fetch_regencies_by_province_id(): void
    {
        $province = Province::first(); // Get the first province from seeded data
        $this->assertNotNull($province, 'No provinces found to test regencies.');

        $response = $this->getJson('/api/wilayah/regencies/' . $province->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*',
                     [
                         'id',
                         'province_id',
                         'name',
                     ]
                 ])
                 ->assertJsonCount(Regency::where('province_id', $province->id)->count());
    }

    /**
     * Test if districts can be fetched by regency ID.
     */
    public function test_can_fetch_districts_by_regency_id(): void
    {
        $regency = Regency::first(); // Get the first regency from seeded data
        $this->assertNotNull($regency, 'No regencies found to test districts.');

        $response = $this->getJson('/api/wilayah/districts/' . $regency->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*',
                     [
                         'id',
                         'regency_id',
                         'name',
                     ]
                 ])
                 ->assertJsonCount(District::where('regency_id', $regency->id)->count());
    }

    /**
     * Test if villages can be fetched by district ID.
     */
    public function test_can_fetch_villages_by_district_id(): void
    {
        $district = District::first(); // Get the first district from seeded data
        $this->assertNotNull($district, 'No districts found to test villages.');

        $response = $this->getJson('/api/wilayah/villages/' . $district->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*',
                     [
                         'id',
                         'district_id',
                         'name',
                     ]
                 ])
                 ->assertJsonCount("App\Models\Village"::where('district_id', $district->id)->count());
    }
}
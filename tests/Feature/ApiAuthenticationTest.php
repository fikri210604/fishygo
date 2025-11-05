<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\RoleSeeder;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_be_fetched_via_api(): void
    {
        // 0. Seed the roles
        $this->seed(RoleSeeder::class);

        // 1. Create a user
        $user = User::factory()->create();

        // 2. Authenticate the user for API requests using Sanctum
        Sanctum::actingAs($user);

        // 3. Make a GET request to the /api/user endpoint
        $response = $this->getJson('/api/user');

        // 4. Assert the response is successful
        $response->assertStatus(200);

        // 5. Assert the response contains the user's data
        $response->assertJson([
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }
}
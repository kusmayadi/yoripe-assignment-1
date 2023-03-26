<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_login(): void
    {
        $password = 'simple';

        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => $password
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'errors', 'data']);

        $data = $response->json('data');

        $this->assertEquals('Bearer', $response->json('data.token_type'));
        $this->assertArrayHasKey('access_token', $data);
    }

    public function test_logout(): void
    {
        $password = 'simple';

        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);

        $responseLogin = $this->post('/api/login', [
            'email' => $user->email,
            'password' => $password
        ]);

        $responseLogin->assertStatus(200);

        $token = $responseLogin->json('data.access_token');

        $responseLogout = $this->withToken($token)->post('/api/logout');

        $responseLogout->assertStatus(200);

        $this->assertEquals(0, $user->tokens()->count());
    }
}

<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'user@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];

        $response = $this->postJson('/api/v1/register', $payload);

        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user' => ['uuid', 'name', 'email', 'created_at'],
                'token'
            ]
        ]);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'user@gmail.com',
            'password' => bcrypt('password')
        ]);

        $payload = [
            'email' => 'user@gmail.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/v1/login', $payload);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user' => ['uuid', 'name', 'email', 'created_at'],
                'token'
            ]
        ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $payload = [
            'email' => 'not_user@gmail.com',
            'password' => '12345678'
        ];

        $response = $this->postJson('/api/v1/login', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_login_validation_fails(): void
    {
        $response = $this->postJson('/api/v1/login', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/logout');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'User logged out successfully',
        ]);
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->postJson('/api/v1/logout');

        $response->assertUnauthorized();
    }
}

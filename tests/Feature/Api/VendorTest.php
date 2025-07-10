<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class VendorTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser();
    }

    public function createUser(): User
    {
        return User::factory()->create();
    }

    public function test_user_can_create_vendor(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/vendor', [
            'name' => 'Test Vendor',
            'address' => 'Test Address',
            'phone_number' => '1234567890',
        ]);

        $response->assertCreated();
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'uuid',
                'name',
                'address',
                'phone_number',
            ],
        ]);
    }

    public function test_user_cannot_create_vendor_without_authentication(): void
    {
        $response = $this->postJson('/api/v1/vendor', [
            'name' => 'Test Vendor',
            'address' => 'Test Address',
            'phone_number' => '1234567890',
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_cannot_create_vendor_with_invalid_data(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/vendor', [
            'name' => '',
            'address' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'address', 'phone_number']);
    }
}

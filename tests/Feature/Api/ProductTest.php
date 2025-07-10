<?php

namespace Tests\Feature\Api\V1;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser();
        $this->vendor = $this->createVendor($this->user);
    }

    private function createUser(): User
    {
        return User::factory()->create();
    }

    private function createVendor(User $user): Vendor
    {
        return Vendor::factory()->for($user)->create();
    }

    private function createProduct(int $count = 1): Collection|Product
    {
        if ($count === 1) {
            return Product::factory()->for($this->vendor)->create();
        }
        return Product::factory($count)->for($this->vendor)->create();
    }

    public function test_vendor_can_get_their_products(): void
    {
        Sanctum::actingAs($this->user);
        $this->createProduct(3);

        $response = $this->getJson('/api/v1/products');

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'uuid',
                    'name',
                    'details',
                    'price',
                    'stock',
                ]
            ],
        ]);
        $response->assertJsonCount(3, 'data');
    }

    public function test_user_without_vendor_cannot_get_products(): void
    {
        $userWithoutVendor = $this->createUser();
        Sanctum::actingAs($userWithoutVendor);

        $response = $this->getJson('/api/v1/products');

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_get_products(): void
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertUnauthorized();
    }

    public function test_vendor_can_create_product(): void
    {
        Sanctum::actingAs($this->user);
        $productData = [
            'name' => 'Product 1',
            'description' => 'Product description here.',
            'price' => 99000,
            'stock' => 100,
        ];

        $response = $this->postJson('/api/v1/products', $productData);

        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'uuid',
                'name',
                'details',
                'price',
                'stock',
            ],
        ]);
        $this->assertDatabaseHas('products', [
            'vendor_id' => $this->vendor->id,
            'name' => 'Product 1',
        ]);
    }

    public function test_vendor_cannot_create_product_with_invalid_data(): void
    {
        Sanctum::actingAs($this->user);
        $productData = [
            'name' => '',
            'price' => -10,
            'stock' => 'not-an-integer',
        ];

        $response = $this->postJson('/api/v1/products', $productData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'price', 'stock']);
    }

    public function test_vendor_can_view_their_own_product(): void
    {
        Sanctum::actingAs($this->user);
        $product = $this->createProduct();

        $response = $this->getJson("/api/v1/products/{$product->uuid}");

        $response->assertOk();
        $response->assertJson([
            'data' => [
                'uuid' => $product->uuid,
                'name' => $product->name,
            ]
        ]);
    }

    public function test_vendor_cannot_view_another_vendors_product(): void
    {
        $otherUser = $this->createUser();
        $otherVendor = $this->createVendor($otherUser);
        $productForOtherVendor = Product::factory()->for($otherVendor)->create();

        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/v1/products/{$productForOtherVendor->uuid}");

        $response->assertForbidden();
    }

    public function test_vendor_can_update_their_product(): void
    {
        Sanctum::actingAs($this->user);
        $product = $this->createProduct();
        $updateData = ['name' => 'Updated Product Name'];

        $response = $this->putJson("/api/v1/products/{$product->uuid}", $updateData);

        $response->assertOk();
        $response->assertJsonFragment(['name' => 'Updated Product Name']);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
        ]);
    }

    public function test_vendor_cannot_update_another_vendors_product(): void
    {
        $otherUser = $this->createUser();
        $otherVendor = $this->createVendor($otherUser);
        $productForOtherVendor = Product::factory()->for($otherVendor)->create();

        Sanctum::actingAs($this->user);
        $updateData = ['name' => 'Updated Product Name'];

        $response = $this->putJson("/api/v1/products/{$productForOtherVendor->uuid}", $updateData);

        $response->assertForbidden();
    }

    public function test_vendor_can_delete_their_product(): void
    {
        Sanctum::actingAs($this->user);
        $product = $this->createProduct();

        $response = $this->deleteJson("/api/v1/products/{$product->uuid}");

        $response->assertOk();
        $response->assertJsonFragment(['message' => 'Product deleted successfully.']);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_vendor_cannot_delete_another_vendors_product(): void
    {
        $otherUser = $this->createUser();
        $otherVendor = $this->createVendor($otherUser);
        $productForOtherVendor = Product::factory()->for($otherVendor)->create();

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("/api/v1/products/{$productForOtherVendor->uuid}");

        $response->assertForbidden();
    }
}

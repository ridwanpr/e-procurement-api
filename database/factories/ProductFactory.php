<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 5, 2000),
            'stock' => $this->faker->numberBetween(0, 150),
        ];
    }
}

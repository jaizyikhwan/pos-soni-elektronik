<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_barang' => $this->faker->word(),
            'tipe_barang' => $this->faker->randomElement(['Electronics', 'Furniture', 'Clothing', 'Books']),
            'harga_jual' => $this->faker->numberBetween(50000, 10000000),
            'harga_beli' => $this->faker->numberBetween(30000, 8000000),
            'tanggal_order' => $this->faker->date(),
            'stok' => $this->faker->numberBetween(1, 100),
            'barcode' => $this->faker->unique()->ean13(),
        ];
    }

    /**
     * State for out of stock items
     */
    public function outOfStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'stok' => 0,
        ]);
    }

    /**
     * State for specific barcode
     */
    public function withBarcode(string $barcode): static
    {
        return $this->state(fn(array $attributes) => [
            'barcode' => $barcode,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hargaSatuan = $this->faker->numberBetween(50000, 10000000);
        $jumlah = $this->faker->numberBetween(1, 10);

        return [
            'item_id' => Item::factory(),
            'jumlah' => $jumlah,
            'nama_pembeli' => $this->faker->name(),
            'no_hp' => $this->faker->phoneNumber(),
            'alamat' => $this->faker->address(),
            'harga_satuan' => $hargaSatuan,
            'total_harga' => $hargaSatuan * $jumlah,
            'nomor_seri' => $this->faker->unique()->numerify('SN-###-###'),
            'tanggal' => $this->faker->dateTime(),
            'status' => $this->faker->randomElement(['PENDING', 'COMPLETED', 'CANCELLED']),
        ];
    }

    /**
     * State for completed transactions
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'COMPLETED',
        ]);
    }

    /**
     * State for cancelled transactions
     */
    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'CANCELLED',
        ]);
    }

    /**
     * State for pending transactions
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'PENDING',
        ]);
    }
}

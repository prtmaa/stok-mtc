<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\\Models\\Item>
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
        $kategoriList = ['1', '2', '4'];
        $satuanList = ['1', '2', '4', '5', '6'];

        return [
            'code' => strtoupper($this->faker->unique()->bothify('ITM-#####')),

            'kategori_id' => $this->faker->randomElement($kategoriList),
            'nama' => ucfirst($this->faker->words(2, true)),
            'satuan_id' => $this->faker->randomElement($satuanList),
            'stok_akhir' => $this->faker->numberBetween(0, 500),
        ];
    }
}

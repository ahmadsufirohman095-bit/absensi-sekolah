<?php

namespace Database\Factories;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class KelasFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Kelas::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama_kelas' => $this->faker->unique()->word . ' ' . $this->faker->randomElement(['IPA', 'IPS', 'Bahasa']) . ' ' . $this->faker->numberBetween(1, 3),
            'wali_kelas_id' => null, // Can be set later if needed
        ];
    }
}

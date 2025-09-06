<?php

namespace Database\Factories;

use App\Models\MataPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

class MataPelajaranFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MataPelajaran::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kode_mapel' => $this->faker->unique()->bothify('MAPEL-###'),
            'nama_mapel' => $this->faker->unique()->word . ' ' . $this->faker->randomElement(['Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris', 'Agama']),
            'deskripsi' => $this->faker->sentence,
        ];
    }
}

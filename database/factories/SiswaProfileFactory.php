<?php

namespace Database\Factories;

use App\Models\SiswaProfile;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiswaProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SiswaProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(), // Will be overridden in tests
            'kelas_id' => Kelas::factory(), // Will be overridden in tests
            'nis' => $this->faker->unique()->numerify('########'),
            'nama_lengkap' => $this->faker->name,
            'jenis_kelamin' => $this->faker->randomElement(['laki-laki', 'perempuan']),
            'tempat_lahir' => $this->faker->city,
            'tanggal_lahir' => $this->faker->date(),
            'alamat' => $this->faker->address,
            'telepon_ayah' => $this->faker->phoneNumber,
            'telepon_ibu' => $this->faker->phoneNumber,
            'nama_ayah' => $this->faker->name('male'),
            'nama_ibu' => $this->faker->name('female'),
            'foto' => null,
        ];
    }
}

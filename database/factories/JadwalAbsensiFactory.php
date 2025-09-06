<?php

namespace Database\Factories;

use App\Models\JadwalAbsensi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JadwalAbsensiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JadwalAbsensi::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kelas_id' => Kelas::factory(),
            'mata_pelajaran_id' => MataPelajaran::factory(),
            'hari' => $this->faker->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']),
            'jam_mulai' => $this->faker->time('H:i', '12:00'),
            'jam_selesai' => $this->faker->time('H:i', '17:00'),
            'guru_id' => User::factory()->create(['role' => 'guru'])->id,
        ];
    }
}

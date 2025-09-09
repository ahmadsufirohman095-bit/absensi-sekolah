<?php

namespace Database\Factories;

use App\Models\Absensi;
use App\Models\User;
use App\Models\JadwalAbsensi;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AbsensiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Absensi::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = $this->faker->randomElement(['hadir', 'terlambat', 'sakit', 'izin']);
        $waktuMasuk = null;
        $waktuKeluar = null;
        if (in_array($status, ['hadir', 'terlambat'])) {
            $waktuMasuk = $this->faker->time('H:i:s');
            $waktuKeluar = $this->faker->boolean(70) ? $this->faker->time('H:i:s', $waktuMasuk) : null;
        }

        return [
            'user_id' => User::factory()->create(['role' => 'siswa'])->id,
            'jadwal_absensi_id' => JadwalAbsensi::factory()->create()->id,
            'tanggal_absensi' => $this->faker->date(),
            'status' => $status,
            'waktu_masuk' => $waktuMasuk,
            'waktu_keluar' => $waktuKeluar,
            'lokasi_absensi' => $lokasiAbsensi,
            'keterangan' => $this->faker->sentence,
        ];
    }
}
n' => $this->faker->sentence,
        ];
    }
}

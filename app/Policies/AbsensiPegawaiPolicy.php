<?php

namespace App\Policies;

use App\Models\AbsensiPegawai;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsensiPegawaiPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Admin selalu diizinkan untuk melakukan semua tindakan pada AbsensiPegawai.
        if ($user->isAdmin()) {
            return true;
        }

        return null; // Untuk peran non-admin, lanjutkan ke metode kebijakan spesifik.
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Pengguna dengan peran Guru, TU, atau Other dapat melihat semua model absensi pegawai.
        return $user->isGuru() || $user->isTu() || $user->isOther();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        // Pengguna dengan peran Guru, TU, atau Other dapat melihat model absensi pegawai jika itu milik mereka.
        return ($user->isGuru() || $user->isTu() || $user->isOther()) && $user->id === $absensiPegawai->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Pengguna dengan peran Guru, TU, atau Other dapat membuat model absensi pegawai.
        // Admin sudah ditangani di metode 'before'.
        return $user->isGuru() || $user->isTu() || $user->isOther();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return ($user->isGuru() || $user->isTu() || $user->isOther()) && $user->id === $absensiPegawai->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return ($user->isGuru() || $user->isTu() || $user->isOther()) && $user->id === $absensiPegawai->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return false; // Typically only admin can restore
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return false; // Typically only admin can force delete
    }

    /**
     * Determine whether the user can perform bulk delete.
     */
    public function bulkDelete(User $user): bool
    {
        return $user->isGuru() || $user->isTu() || $user->isOther();
    }

    /**
     * Determine whether the user can export models.
     */
    public function export(User $user): bool
    {
        return $user->isAdmin() || $user->isGuru() || $user->isTu() || $user->isOther();
    }
}

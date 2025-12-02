<?php

namespace App\Policies;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsensiPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            // Admin dapat melakukan semua tindakan untuk keperluan absensi manual atau scan,
            // tetapi otorisasi lebih lanjut akan ditangani di controller berdasarkan target user.
            return null;
        }

        if ($user->isGuru()) {
            return true; // Guru dapat melakukan semua tindakan
        }

        return false; // Pengguna lain tidak diizinkan
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false; // Ditangani oleh metode before
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Absensi $absensi): bool
    {
        return false; // Ditangani oleh metode before
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false; // Ditangani oleh metode before
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Absensi $absensi): bool
    {
        return false; // Ditangani oleh metode before
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Absensi $absensi): bool
    {
        return false; // Ditangani oleh metode before
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Absensi $absensi): bool
    {
        return false; // Ditangani oleh metode before
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Absensi $absensi): bool
    {
        return false; // Ditangani oleh metode before
    }

    // Custom methods for bulk delete and export
    public function bulkDelete(User $user): bool
    {
        return false; // Ditangani oleh metode before
    }

    public function export(User $user): bool
    {
        if ($user->isGuru() || $user->isAdmin()) {
            return true;
        }
        return false;
    }
}

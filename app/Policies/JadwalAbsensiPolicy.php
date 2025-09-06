<?php

namespace App\Policies;

use App\Models\JadwalAbsensi;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JadwalAbsensiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JadwalAbsensi $jadwalAbsensi): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JadwalAbsensi $jadwalAbsensi): bool
    {
        // Admin can update any JadwalAbsensi
        if ($user->isAdmin()) {
            return true;
        }

        // Guru can update their own JadwalAbsensi
        if ($user->hasRole('guru')) {
            return $user->id === $jadwalAbsensi->guru_id;
        }

        return false; // Other roles cannot update
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JadwalAbsensi $jadwalAbsensi): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JadwalAbsensi $jadwalAbsensi): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JadwalAbsensi $jadwalAbsensi): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete all models.
     */
    public function deleteAll(User $user): bool
    {
        return $user->isAdmin();
    }
}

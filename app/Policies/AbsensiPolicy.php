<?php

namespace App\Policies;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsensiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Absensi $absensi): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Absensi $absensi): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Absensi $absensi): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Absensi $absensi): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Absensi $absensi): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    // Custom methods for bulk delete and export
    public function bulkDelete(User $user): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }

    public function export(User $user): bool
    {
        return $user->isAdmin() || $user->isGuru();
    }
}

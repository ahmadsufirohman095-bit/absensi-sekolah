<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\JadwalAbsensi;
use App\Policies\JadwalAbsensiPolicy;
use App\Models\Absensi;
use App\Policies\AbsensiPolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use App\Models\Setting;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        JadwalAbsensi::class => JadwalAbsensiPolicy::class,
        Absensi::class => AbsensiPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('isGuru', function (User $user) {
            return $user->role === 'guru';
        });

        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('manage-absensi', function (User $user) {
            return $user->role === 'admin' || $user->role === 'guru';
        });

        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->role === $role;
        });
    }
}

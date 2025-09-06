<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define 'isGuru' Gate
        // Returns true if user role is 'guru'
        Gate::define('isGuru', function(User $user) {
            return $user->role == 'guru';
        });

        // Gate for attendance management (QR display)
        // Access granted to 'admin' and 'guru' roles
        Gate::define('manage-absensi', function(User $user) {
            return in_array($user->role, ['admin', 'guru']);
        });

        // Gate for user management
        // Access granted only to 'admin' role
        Gate::define('manage-users', function(User $user) {
            return $user->role === 'admin';
        });

        // Gate untuk user yang merupakan admin sejati
        Gate::define('isAdmin', function(User $user) {
            return $user->role === 'admin';
        });
    }
}
